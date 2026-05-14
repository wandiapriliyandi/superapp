<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\AkunModel;
use Keuangan\Models\JurnalDetailModel;

class Laporan extends BaseController
{
    public function neraca()
    {
        $data = [
            'title' => 'Laporan Neraca',
            'tanggal' => $this->request->getGet('tanggal') ?: date('Y-m-d'),
            'aset' => [],
            'kewajiban' => [],
            'ekuitas' => [],
        ];

        // Logika hitung saldo per akun sampai tanggal tertentu
        // ... (Implementasi singkat)

        return view('Keuangan\Views\laporan\neraca', $data);
    }

    public function labaRugi()
    {
        $data = [
            'title' => 'Laporan Laba Rugi',
            'tgl_mulai' => $this->request->getGet('tgl_mulai') ?: date('Y-m-01'),
            'tgl_selesai' => $this->request->getGet('tgl_selesai') ?: date('Y-m-t'),
            'pendapatan' => [],
            'beban' => [],
        ];

        return view('Keuangan\Views\laporan\laba_rugi', $data);
    }
}
