<?php

namespace App\Libraries;

use Exception;

class JWT
{
    /**
     * Encode payload into a JWT token
     */
    public static function encode(array $payload, string $key, string $alg = 'HS256'): string
    {
        $header = ['alg' => $alg, 'typ' => 'JWT'];
        
        $segments = [];
        $segments[] = self::base64UrlEncode(json_encode($header));
        $segments[] = self::base64UrlEncode(json_encode($payload));
        
        $signingInput = implode('.', $segments);
        $signature = self::sign($signingInput, $key, $alg);
        $segments[] = self::base64UrlEncode($signature);
        
        return implode('.', $segments);
    }

    /**
     * Decode a JWT token back into payload object
     */
    public static function decode(string $jwt, string $key, array $allowedAlgs = ['HS256']): object
    {
        $tks = explode('.', $jwt);
        
        if (count($tks) !== 3) {
            throw new Exception('Format token tidak valid');
        }
        
        list($headb64, $bodyb64, $cryptob64) = $tks;
        
        $header = json_decode(self::base64UrlDecode($headb64));
        if (null === $header) {
            throw new Exception('Header token tidak valid');
        }
        
        $payload = json_decode(self::base64UrlDecode($bodyb64));
        if (null === $payload) {
            throw new Exception('Payload token tidak valid');
        }
        
        $sig = self::base64UrlDecode($cryptob64);
        
        if (empty($header->alg)) {
            throw new Exception('Algoritma token kosong');
        }
        
        if (!in_array($header->alg, $allowedAlgs)) {
            throw new Exception('Algoritma tidak didukung');
        }
        
        // Verifikasi signature
        $signingInput = $headb64 . '.' . $bodyb64;
        if (!self::verify($signingInput, $sig, $key, $header->alg)) {
            throw new Exception('Tanda tangan token tidak valid / Signature verification failed');
        }
        
        // Cek exp (expiration time) jika ada
        if (isset($payload->exp) && $payload->exp < time()) {
            throw new Exception('Token telah kedaluwarsa');
        }
        
        return $payload;
    }

    /**
     * Sign string using HMAC
     */
    private static function sign(string $msg, string $key, string $alg): string
    {
        if ($alg === 'HS256') {
            return hash_hmac('sha256', $msg, $key, true);
        }
        throw new Exception('Algoritma tanda tangan tidak didukung: ' . $alg);
    }

    /**
     * Verify signature
     */
    private static function verify(string $msg, string $signature, string $key, string $alg): bool
    {
        if ($alg === 'HS256') {
            $hash = hash_hmac('sha256', $msg, $key, true);
            if (function_exists('hash_equals')) {
                return hash_equals($signature, $hash);
            }
            return $signature === $hash;
        }
        return false;
    }

    /**
     * Base64URL Encode
     */
    public static function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Base64URL Decode
     */
    public static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
    }
}
