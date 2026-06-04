<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek hak akses dinamis berdasarkan path URL
        $uri = $request->getUri();
        $path = $uri->getPath(); // Mendapatkan path relatif, misal: 'perijinan/rekap' atau 'poskestren'
        $segments = explode('/', trim($path, '/'));
        $module = $segments[0] ?? '';

        $permissions = session()->get('permissions') ?: [];

        // Superadmin (*) dilewati untuk semua modul
        if (in_array('*', $permissions)) {
            return;
        }

        // Modul penting yang dilindungi
        $protectedModules = ['perijinan', 'poskestren', 'monitoring', 'setting', 'migrate', 'activity'];

        if (in_array($module, $protectedModules)) {
            // Untuk modul sistem (setting, migrate, activity) hanya boleh diakses superadmin (*)
            $systemModules = ['setting', 'migrate', 'activity'];
            if (in_array($module, $systemModules)) {
                return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki hak akses superadmin.');
            }

            // Cek permission modul bersangkutan
            if (!in_array($module, $permissions)) {
                return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki hak akses ke modul ' . ucfirst($module) . '.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request selesai
    }
}
