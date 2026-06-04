<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('activity'));
        }

        $data = [
            'title' => 'Login SuperApp',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/login', $data);
    }

    public function proses_login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Username dan password wajib diisi.')->withInput();
        }

        $user = $this->userModel->getUserWithRole($username);

        if (!$user) {
            return redirect()->back()->with('error', 'Username tidak ditemukan.')->withInput();
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Password salah.')->withInput();
        }

        // Simpan data session
        $permissions = json_decode($user['permissions'], true) ?: [];
        
        session()->set([
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role'         => $user['nama_role'],
            'permissions'  => $permissions,
            'logged_in'    => true
        ]);

        log_activity('Melakukan Login', 'Autentikasi', 'User dengan username ' . $user['username'] . ' berhasil masuk ke sistem.');
 
        // Redirect sesuai hak akses modul
        if (in_array('*', $permissions)) {
            return redirect()->to(base_url('activity'));
        } elseif (in_array('perijinan', $permissions)) {
            return redirect()->to(base_url('perijinan'));
        } elseif (in_array('poskestren', $permissions)) {
            return redirect()->to(base_url('poskestren'));
        } elseif (in_array('monitoring', $permissions)) {
            return redirect()->to(base_url('monitoring'));
        }

        return redirect()->to(base_url('/'));
    }

    public function logout()
    {
        $namaLengkap = session()->get('nama_lengkap');
        if ($namaLengkap) {
            log_activity('Melakukan Logout', 'Autentikasi', 'User ' . $namaLengkap . ' keluar dari sistem.');
        }

        session()->destroy();
        return redirect()->to(base_url('login'));
    }
}
