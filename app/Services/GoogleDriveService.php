<?php

namespace App\Services;

class GoogleDriveService
{
    protected $accessToken;
    protected $serviceAccount;

    public function __construct()
    {
        // Load credentials from a JSON file or setting
        // For now, we expect a file at writable/google-credentials.json
        $path = WRITEPATH . 'google-credentials.json';
        if (file_exists($path)) {
            $this->serviceAccount = json_decode(file_get_contents($path), true);
        }
    }

    public function isConfigured()
    {
        return !empty($this->serviceAccount);
    }

    protected function getAccessToken()
    {
        if ($this->accessToken) return $this->accessToken;

        if (!$this->isConfigured()) return null;

        // Simple JWT implementation for Google Service Account
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $now = time();
        $payload = json_encode([
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.file',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600
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
            'assertion' => $jwt
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix for SSL issue mentioned in composer

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

    public function upload($filePath, $fileName, $folderId = null)
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        $metadata = [
            'name' => $fileName,
            'parents' => $folderId ? [$folderId] : []
        ];

        $boundary = '-------' . md5(time());
        $content = "--{$boundary}\r\n";
        $content .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
        $content .= json_encode($metadata) . "\r\n";
        $content .= "--{$boundary}\r\n";
        $content .= "Content-Type: application/pdf\r\n\r\n";
        $content .= file_get_contents($filePath) . "\r\n";
        $content .= "--{$boundary}--";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: multipart/related; boundary=' . $boundary,
            'Content-Length: ' . strlen($content)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

        $response = curl_exec($ch);
        $data = json_decode($response, true);
        curl_close($ch);

        return $data['id'] ?? null;
    }
}
