<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use App\Libraries\JWT;
use Exception;

class Auth extends BaseController
{
    use ResponseTrait;

    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        // Mendapatkan request body baik berupa JSON maupun POST biasa
        $json = null;
        try {
            $json = $this->request->getJSON();
        } catch (\Exception $e) {
            // Abaikan error parsing JSON
        }
        
        $username = null;
        $password = null;

        if ($json) {
            $username = $json->username ?? null;
            $password = $json->password ?? null;
        }

        if (empty($username)) {
            $username = $this->request->getPost('username');
        }
        if (empty($password)) {
            $password = $this->request->getPost('password');
        }

        if (empty($username) || empty($password)) {
            return $this->fail('Username dan password wajib diisi.', 400);
        }

        $user = $this->userModel->getUserWithRole($username);

        if (!$user) {
            return $this->fail('Username tidak ditemukan.', 404);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->fail('Password salah.', 401);
        }

        // Decode hak akses (permissions)
        $permissions = json_decode($user['permissions'], true) ?: [];

        // Buat payload untuk JWT token
        $issuedAt = time();
        $expirationTime = $issuedAt + (60 * 60 * 24); // Berlaku selama 24 jam
        
        $payload = [
            'iat'          => $issuedAt,
            'exp'          => $expirationTime,
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role'         => $user['nama_role'],
            'permissions'  => $permissions
        ];

        try {
            $secretKey = getenv('JWT_SECRET') ?: 'super_secret_key_123';
            $token = JWT::encode($payload, $secretKey);
            
            // Catat log aktivitas login
            // Perhatikan: Karena helper log_activity menggunakan session untuk mendapatkan nama_lengkap,
            // kita set temporary session agar log_activity dapat merekam nama dengan benar
            session()->set(['nama_lengkap' => $user['nama_lengkap']]);
            log_activity('Melakukan Login API', 'Autentikasi API', 'User ' . $user['username'] . ' login dari aplikasi eksternal.');
            session()->remove('nama_lengkap');

            return $this->respond([
                'status' => 200,
                'message' => 'Login berhasil',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'nama_lengkap' => $user['nama_lengkap'],
                        'role' => $user['nama_role'],
                        'permissions' => $permissions
                    ]
                ]
            ]);

        } catch (Exception $e) {
            return $this->fail('Gagal menghasilkan token keamanan: ' . $e->getMessage(), 500);
        }
    }
}
