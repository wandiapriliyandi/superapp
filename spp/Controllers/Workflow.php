<?php

namespace Spp\Controllers;

use App\Controllers\BaseController;

class Workflow extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Petunjuk Pembayaran SPP',
        ];

        return view('Spp\Views\workflow', $data);
    }
}
