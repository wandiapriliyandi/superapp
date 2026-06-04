<?php

namespace Kepegawaian\Controllers;

use App\Controllers\BaseController;
use Kepegawaian\Models\PayrollModel;
use Kepegawaian\Models\PegawaiModel;
use Kepegawaian\Models\JabatanModel;

class Payroll extends BaseController
{
    protected $payrollModel;
    protected $pegawaiModel;

    public function __construct()
    {
        $this->payrollModel = new PayrollModel();
        $this->pegawaiModel = new PegawaiModel();
    }

    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        $data = [
            'title' => 'Payroll & Gaji Asatidz',
            'payroll' => $this->payrollModel->getPayrollFull($bulan, $tahun),
            'bulan_filter' => $bulan,
            'tahun_filter' => $tahun
        ];

        return view('Kepegawaian\Views\payroll\index', $data);
    }

    public function generate()
    {
        helper('activity');
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        
        $pegawai = $this->pegawaiModel->getPegawaiFull();
        $count = 0;
        
        foreach ($pegawai as $p) {
            // Cek jika sudah digenerate
            $exist = $this->payrollModel->where('pegawai_id', $p['id'])
                                        ->where('bulan', $bulan)
                                        ->where('tahun', $tahun)
                                        ->first();
            
            if (!$exist) {
                $gaji_pokok = $p['gaji_pokok'] ?? 0;
                $tunjangan = ($p['tunjangan_makan'] ?? 0) + ($p['tunjangan_transport'] ?? 0);
                
                $this->payrollModel->save([
                    'pegawai_id' => $p['id'],
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'gaji_pokok' => $gaji_pokok,
                    'total_tunjangan' => $tunjangan,
                    'gaji_bersih' => $gaji_pokok + $tunjangan,
                    'status_bayar' => 'Belum Dibayar'
                ]);
                $count++;
            }
        }

        log_activity('Generate Payroll Pegawai', 'Kepegawaian', 'Bulan: ' . $bulan . ', Tahun: ' . $tahun . ', Total: ' . $count);

        return redirect()->to(base_url("kepegawaian/payroll?bulan=$bulan&tahun=$tahun"))->with('success', 'Payroll berhasil digenerate.');
    }
}
