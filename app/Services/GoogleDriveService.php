<?php

namespace App\Services;

class GoogleDriveService
{
    protected $accessToken;
    protected $serviceAccount;
    protected $tokenPath;
    protected $authConfigPath;
    protected $mode = 'service_account'; // 'oauth' atau 'service_account'

    public function __construct()
    {
        $this->tokenPath = WRITEPATH . 'token.json';
        if (!file_exists($this->tokenPath) && file_exists(WRITEPATH . 'uploads/token.json')) {
            $this->tokenPath = WRITEPATH . 'uploads/token.json';
        }

        $this->authConfigPath = WRITEPATH . 'client_secret.json';
        if (!file_exists($this->authConfigPath) && file_exists(WRITEPATH . 'uploads/client_secret.json')) {
            $this->authConfigPath = WRITEPATH . 'uploads/client_secret.json';
        }

        // Cek mode mana yang tersedia: OAuth token (seperti project 2) vs Service Account
        if (file_exists($this->tokenPath) && file_exists($this->authConfigPath)) {
            $this->mode = 'oauth';
        } else {
            $path = WRITEPATH . 'google-credentials.json';
            if (file_exists($path)) {
                $this->serviceAccount = json_decode(file_get_contents($path), true);
            } elseif (env('GOOGLE_DRIVE_CREDENTIALS')) {
                $this->serviceAccount = json_decode(env('GOOGLE_DRIVE_CREDENTIALS'), true);
            }
        }
    }

    public function isConfigured()
    {
        return $this->mode === 'oauth' || !empty($this->serviceAccount);
    }

    /**
     * Mendapatkan token & konfigurasi untuk Direct Client Upload dari Browser
     */
    public function getUploadConfig()
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        return [
            'access_token' => $token,
            'is_configured' => true
        ];
    }

    protected function getAccessToken()
    {
        if ($this->accessToken) return $this->accessToken;

        if (!$this->isConfigured()) return null;

        if ($this->mode === 'oauth') {
            return $this->getOAuthAccessToken();
        }

        return $this->getServiceAccountAccessToken();
    }

    protected function getOAuthAccessToken()
    {
        $token = json_decode(file_get_contents($this->tokenPath), true);
        $creds = json_decode(file_get_contents($this->authConfigPath), true);

        if (isset($token['refresh_token'])) {
            $tokenUri = $creds['web']['token_uri'] ?? 'https://oauth2.googleapis.com/token';
            $clientId = $creds['web']['client_id'] ?? '';
            $clientSecret = $creds['web']['client_secret'] ?? '';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $tokenUri);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $token['refresh_token'],
                'grant_type'    => 'refresh_token'
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            curl_close($ch);

            $newToken = json_decode($response, true);
            if (isset($newToken['access_token'])) {
                $token['access_token'] = $newToken['access_token'];
                if (isset($newToken['expires_in'])) {
                    $token['expires_in'] = $newToken['expires_in'];
                }
                $token['created'] = time();
                file_put_contents($this->tokenPath, json_encode($token));
                $this->accessToken = $token['access_token'];
                return $this->accessToken;
            }
        }

        $this->accessToken = $token['access_token'] ?? null;
        return $this->accessToken;
    }

    protected function getServiceAccountAccessToken()
    {
        // Simple JWT implementation for Google Service Account
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss'   => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $this->serviceAccount['private_key'], "SHA256");
        $base64UrlSignature = $this->base64UrlEncode($signature);

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $data = json_decode($response, true);
        curl_close($ch);

        $this->accessToken = $data['access_token'] ?? null;
        return $this->accessToken;
    }

    protected function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Upload sebuah file ke Google Drive
     *
     * @param string $filePath Path file fisik di server (misal: /tmp/file.pdf)
     * @param string $fileName Nama file yang akan tersimpan di Google Drive
     * @param string|null $folderId ID Folder Google Drive (opsional)
     * @return string|null Mengembalikan File ID dari Google Drive jika sukses
     * @throws \Exception Jika gagal upload atau token tidak ada
     */
    public function upload($filePath, $fileName, $folderId = null)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            throw new \Exception('Google Drive tidak dikonfigurasi atau token gagal didapatkan.');
        }

        if (!file_exists($filePath)) {
            throw new \Exception('File fisik tidak ditemukan: ' . $filePath);
        }

        $metadata = [
            'name' => $fileName,
            'parents' => $folderId ? [$folderId] : []
        ];

        // Deteksi MIME Type secara otomatis
        $mimeType = mime_content_type($filePath);
        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }

        $boundary = '-------' . md5(time());
        $content = "--{$boundary}\r\n";
        $content .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
        $content .= json_encode($metadata) . "\r\n";
        $content .= "--{$boundary}\r\n";
        $content .= "Content-Type: {$mimeType}\r\n\r\n";
        $content .= file_get_contents($filePath) . "\r\n";
        $content .= "--{$boundary}--";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: multipart/related; boundary=' . $boundary,
            'Content-Length: ' . strlen($content)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        
        if ($httpCode >= 400 || isset($data['error'])) {
            $errorMessage = $data['error']['message'] ?? 'Gagal mengupload ke Google Drive.';
            throw new \Exception('Google Drive API Error: ' . $errorMessage);
        }

        return $data['id'] ?? null;
    }

    /**
     * Menghapus file di Google Drive
     *
     * @param string $fileId ID file Google Drive
     * @return bool True jika berhasil
     */
    public function delete($fileId)
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?supportsAllDrives=true');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 204;
    }

    /**
     * Mendapatkan tautan untuk melihat file (WebViewLink)
     *
     * @param string $fileId ID file Google Drive
     * @return string|null
     */
    public function getFileLink($fileId)
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/drive/v3/files/' . $fileId . '?supportsAllDrives=true&fields=webViewLink,webContentLink');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['webViewLink'] ?? null;
    }
}
