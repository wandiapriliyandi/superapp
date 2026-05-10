<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('dashboard', [
            'title'        => 'Beranda Utama',
            'hide_sidebar' => true
        ]);
    }
}
