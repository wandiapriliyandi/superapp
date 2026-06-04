<?php

namespace Akademik\Controllers;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use App\Models\SantriModel;
use App\Models\MapelModel;
use App\Models\TahunAjaranModel;
use App\Models\KelasModel;

class Nilai extends BaseController
{
    protected $nilaiModel;

    public function __construct()
    {
        $this->nilaiModel = new NilaiModel();
    }

    public function index()
    {
        $id_kelas = $this->request->getGet('kelas');
        $id_mapel = $this->request->getGet('mapel');
        
        $data = [
            'title' => 'Input Nilai Santri',
            'kelas' => (new KelasModel())->findAll(),
            'mapel' => (new MapelModel())->findAll(),
            'santri' => $id_kelas ? (new SantriModel())->where('status_santri', 'Aktif')->findAll() : [], // In real app, filter by class
            'selected_kelas' => $id_kelas,
            'selected_mapel' => $id_mapel,
            'tahun_ajaran' => (new TahunAjaranModel())->where('status', 'Aktif')->first()
        ];

        return view('Akademik\Views\nilai\index', $data);
    }

    public function store()
    {
        helper('activity');
        $id_mapel = $this->request->getPost('id_mapel');
        $id_tahun_ajaran = $this->request->getPost('id_tahun_ajaran');
        $nilai_data = $this->request->getPost('nilai') ?? []; // [nisn => [tugas, uts, uas]]

        foreach ($nilai_data as $nisn => $n) {
            $tugas = $n['tugas'] ?? 0;
            $uts = $n['uts'] ?? 0;
            $uas = $n['uas'] ?? 0;
            
            // Calculate final score (e.g. 40% tugas, 30% uts, 30% uas)
            $nilai_akhir = ($tugas * 0.4) + ($uts * 0.3) + ($uas * 0.3);
            
            // Determine grade
            $predikat = 'E';
            if ($nilai_akhir >= 85) $predikat = 'A';
            elseif ($nilai_akhir >= 75) $predikat = 'B';
            elseif ($nilai_akhir >= 65) $predikat = 'C';
            elseif ($nilai_akhir >= 50) $predikat = 'D';

            // Check if exists
            $existing = $this->nilaiModel->where([
                'nisn' => $nisn,
                'id_mapel' => $id_mapel,
                'id_tahun_ajaran' => $id_tahun_ajaran
            ])->first();

            $data_save = [
                'nisn' => $nisn,
                'id_mapel' => $id_mapel,
                'id_tahun_ajaran' => $id_tahun_ajaran,
                'nilai_tugas' => $tugas,
                'nilai_uts' => $uts,
                'nilai_uas' => $uas,
                'nilai_akhir' => $nilai_akhir,
                'predikat' => $predikat,
                'keterangan' => $n['keterangan'] ?? ''
            ];

            if ($existing) {
                $this->nilaiModel->update($existing['id'], $data_save);
            } else {
                $this->nilaiModel->save($data_save);
            }
        }

        $mapelModel = new MapelModel();
        $mapel = $mapelModel->find($id_mapel);
        log_activity('Menginput Nilai Santri', 'Akademik', 'Mata Pelajaran: ' . ($mapel['nama_mapel'] ?? '') . ', Total Santri: ' . count($nilai_data));

        return redirect()->to(base_url('akademik/nilai'))->with('success', 'Nilai berhasil disimpan');
    }

    public function rapor($nisn)
    {
        $santriModel = new SantriModel();
        $taModel = new TahunAjaranModel();
        
        $santri = $santriModel->where('nisn', $nisn)->first();
        $ta = $taModel->where('status', 'Aktif')->first();
        
        $data = [
            'title' => 'Rapor Santri',
            'santri' => $santri,
            'tahun_ajaran' => $ta,
            'nilai' => $this->nilaiModel->getNilaiSantri($nisn, $ta['id'])
        ];

        return view('Akademik\Views\nilai\rapor', $data);
    }
}
