<?php

namespace Osis\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        return view('Osis\Views\index');
    }
}
