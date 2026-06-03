<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use App\Models\JadwalModel;
use App\Models\PresensiModel;
use App\Models\SantriModel;
use App\Models\KelasModel;

class Presensi extends BaseController
{
    protected $presensiModel;

    public function __construct()
    {
        $this->presensiModel = new PresensiModel();
    }

    public function index()
    {
        $jadwalModel = new JadwalModel();
        $id_kelas = $this->request->getGet('kelas');
        
        // Terjemahan hari ke Indonesia
        $hari_map = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];
        $hari_ini = $hari_map[date('l')];

        $jadwal = [];
        if ($id_kelas) {
            $jadwal = $jadwalModel->select('akademik_jadwal.*, akademik_kelas.nama_kelas, akademik_mapel.nama_mapel, hr_pegawai.nama_lengkap as nama_guru')
                                 ->join('akademik_kelas', 'akademik_kelas.id = akademik_jadwal.id_kelas')
                                 ->join('akademik_mapel', 'akademik_mapel.id = akademik_jadwal.id_mapel')
                                 ->join('hr_pegawai', 'hr_pegawai.id = akademik_jadwal.id_guru')
                                 ->where('akademik_jadwal.id_kelas', $id_kelas)
                                 ->where('akademik_jadwal.hari', $hari_ini)
                                 ->orderBy('jam_mulai', 'ASC')
                                 ->findAll();
        }
        
        $data = [
            'title' => 'Presensi Santri',
            'kelas' => (new KelasModel())->findAll(),
            'jadwal' => $jadwal,
            'selected_kelas' => $id_kelas,
            'hari_ini' => $hari_ini
        ];

        return view('Akademik\Views\presensi\index', $data);
    }


    public function input($id_jadwal)
    {
        $jadwalModel = new JadwalModel();
        $santriModel = new SantriModel();
        
        $jadwal = $jadwalModel->select('akademik_jadwal.*, akademik_kelas.nama_kelas, akademik_mapel.nama_mapel')
                             ->join('akademik_kelas', 'akademik_kelas.id = akademik_jadwal.id_kelas')
                             ->join('akademik_mapel', 'akademik_mapel.id = akademik_jadwal.id_mapel')
                             ->find($id_jadwal);

        // Fetch students in this class
        // In a real system, we'd have a link table santri_kelas, 
        // but for now, let's assume all active santri can be assigned if they are in the same level
        // or just fetch all active santri for simplicity of this demo
        $santri = $santriModel->where('status_santri', 'Aktif')->findAll();

        $data = [
            'title' => 'Input Presensi',
            'jadwal' => $jadwal,
            'santri' => $santri,
            'tanggal' => date('Y-m-d')
        ];

        return view('Akademik\Views\presensi\input', $data);
    }

    public function store()
    {
        $id_jadwal = $this->request->getPost('id_jadwal');
        $tanggal = $this->request->getPost('tanggal');
        $presensi_data = $this->request->getPost('presensi'); // array [nisn => status]

        foreach ($presensi_data as $nisn => $status) {
            $this->presensiModel->save([
                'id_jadwal' => $id_jadwal,
                'nisn' => $nisn,
                'tanggal' => $tanggal,
                'status' => $status,
                'catatan' => $this->request->getPost('catatan')[$nisn] ?? ''
            ]);
        }

        return redirect()->to(base_url('akademik/presensi'))->with('success', 'Presensi berhasil disimpan');
    }

    public function rekap()
    {
        $id_kelas = $this->request->getGet('kelas');
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $santri = [];
        $rekap = [];

        if ($id_kelas) {
            $santriModel = new SantriModel();
            $santri = $santriModel->where('status_santri', 'Aktif')->findAll(); // Filter by class in real app

            foreach ($santri as $s) {
                $stats = $this->presensiModel->select('status, COUNT(*) as total')
                                            ->where('nisn', $s['nisn'])
                                            ->where('MONTH(tanggal)', $bulan)
                                            ->where('YEAR(tanggal)', $tahun)
                                            ->groupBy('status')
                                            ->findAll();
                
                $data_stat = ['Hadir' => 0, 'Izin' => 0, 'Sakit' => 0, 'Alpa' => 0];
                foreach ($stats as $st) {
                    $data_stat[$st['status']] = $st['total'];
                }
                
                $rekap[$s['nisn']] = $data_stat;
            }
        }

        $data = [
            'title' => 'Rekap Presensi Bulanan',
            'kelas' => (new KelasModel())->findAll(),
            'selected_kelas' => $id_kelas,
            'selected_bulan' => $bulan,
            'selected_tahun' => $tahun,
            'santri' => $santri,
            'rekap' => $rekap
        ];

        return view('Akademik\Views\presensi\rekap', $data);
    }
}

