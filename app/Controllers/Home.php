<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // Redirect ke Vue SPA (hasil build Vite di public/app/)
        // Jika belum di-build, fallback ke Vite dev server
        if (is_dir(FCPATH . 'app')) {
            return redirect()->to(base_url('app/'));
        }

        // Fallback: Vite dev server (saat development)
        return redirect()->to('http://localhost:5173/');
    }
}
