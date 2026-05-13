<?php

namespace Ppdb\Controllers;

use App\Controllers\BaseController;

class Workflow extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Petunjuk Penggunaan PPDB',
        ];

        return view('Ppdb\Views\workflow', $data);
    }
}
