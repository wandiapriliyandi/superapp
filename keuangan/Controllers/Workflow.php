<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;

class Workflow extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Petunjuk Pembayaran SPP',
        ];

        return view('Keuangan\Views\workflow', $data);
    }
}
