<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JWT;
use Exception;

class JWTAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during normal execution.
     * However, when an abnormal state is found, it should return an instance of
     * a ResponseInterface. If it does, script execution will end and that Response
     * will be sent back to the client, allowing for error pages, redirects, etc.
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');

        if (!$authHeader) {
            // Cek alternatif jika header tidak diawali HTTP_
            $authHeader = $request->header('Authorization');
            if ($authHeader) {
                $authHeader = $authHeader->getValue();
            }
        }

        if (!$authHeader) {
            return $this->unauthorizedResponse('Token autentikasi tidak ditemukan.');
        }

        // Token berformat "Bearer <token>"
        $arr = explode(' ', $authHeader);
        $token = isset($arr[1]) ? $arr[1] : null;

        if (!$token) {
            return $this->unauthorizedResponse('Format token tidak valid.');
        }

        try {
            $secretKey = getenv('JWT_SECRET') ?: 'super_secret_key_123';
            $decoded = JWT::decode($token, $secretKey, ['HS256']);
            
            // Simpan data user hasil decode ke request agar bisa diakses di Controller
            $request->user = $decoded;
            
        } catch (Exception $e) {
            return $this->unauthorizedResponse('Token tidak valid atau telah kedaluwarsa: ' . $e->getMessage());
        }
    }

    /**
     * We don't need to do anything in the after filter.
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed
    }

    /**
     * Return a standardized JSON 401 Unauthorized response
     */
    private function unauthorizedResponse(string $message): ResponseInterface
    {
        $response = service('response');
        $response->setStatusCode(401);
        $response->setJSON([
            'status'  => 401,
            'error'   => 'Unauthorized',
            'message' => $message
        ]);
        return $response;
    }
}
