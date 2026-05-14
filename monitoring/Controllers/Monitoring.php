<?php

namespace Monitoring\Controllers;

use App\Controllers\BaseController;

class Monitoring extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Analisis Eksekutif',
            'stats' => [
                'total_santri' => $this->db->table('santri')->countAllResults(),
                'total_pegawai' => $this->db->table('hr_pegawai')->countAllResults() ?? 0,
                'pendaftar_ppdb' => $this->db->table('ppdb_pendaftar')->countAllResults() ?? 0,
                'total_kas' => $this->getTotalKas(),
            ],
            'chart_pendaftaran' => $this->getPendaftaranTrend(),
            'chart_kesehatan'   => $this->getKesehatanTrend(),
            'chart_keuangan'    => $this->getKeuanganTrend(),
        ];

        return view('Monitoring\Views\index', $data);
    }

    public function akademik()
    {
        $data = [
            'title' => 'Laporan Analisis Akademik',
            'summary' => $this->db->table('akademik_nilai')
                        ->select('akademik_mapel.nama_mapel, AVG(nilai_akhir) as rata_rata')
                        ->join('akademik_mapel', 'akademik_mapel.id = akademik_nilai.id_mapel')
                        ->groupBy('id_mapel')
                        ->get()->getResultArray()
        ];
        return view('Monitoring\Views\akademik', $data);
    }

    public function keuangan()
    {
        $data = [
            'title' => 'Laporan Analisis Keuangan',
            'jurnal' => $this->db->table('keu_jurnal')
                        ->orderBy('tanggal', 'DESC')
                        ->limit(20)
                        ->get()->getResultArray()
        ];
        return view('Monitoring\Views\keuangan', $data);
    }

    public function santri()
    {
        $data = [
            'title' => 'Analisis Demografi Santri',
            'gender' => $this->db->table('santri')
                        ->select('jenis_kelamin, COUNT(*) as jumlah')
                        ->groupBy('jenis_kelamin')
                        ->get()->getResultArray()
        ];
        return view('Monitoring\Views\santri', $data);
    }

    private function getTotalKas()
    {
        // Ambil saldo kas dari modul keuangan (keu_jurnal_detail)
        $res = $this->db->table('keu_jurnal_detail')
                        ->selectSum('debit')
                        ->selectSum('kredit')
                        ->get()->getRow();
        return ($res->debit ?? 0) - ($res->kredit ?? 0);
    }

    private function getPendaftaranTrend()
    {
        // Ambil data pendaftaran 6 bulan terakhir
        $query = $this->db->query("
            SELECT MONTHNAME(created_at) as bulan, COUNT(*) as jumlah 
            FROM ppdb_pendaftar 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY MONTH(created_at)
            ORDER BY created_at ASC
        ");
        return $query->getResultArray();
    }

    private function getKesehatanTrend()
    {
        // Ambil data penyakit terbanyak di Poskestren
        $query = $this->db->query("
            SELECT diagnosa, COUNT(*) as jumlah 
            FROM pos_kunjungan 
            WHERE diagnosa IS NOT NULL AND diagnosa != ''
            GROUP BY diagnosa 
            ORDER BY jumlah DESC 
            LIMIT 5
        ");
        return $query->getResultArray();
    }

    private function getKeuanganTrend()
    {
        // Ambil data pemasukan vs pengeluaran 6 bulan terakhir (keu_jurnal)
        $query = $this->db->query("
            SELECT MONTHNAME(tanggal) as bulan, 
                   SUM(debit) as masuk, 
                   SUM(kredit) as keluar
            FROM keu_jurnal_detail
            JOIN keu_jurnal ON keu_jurnal.id = keu_jurnal_detail.jurnal_id
            WHERE tanggal >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY MONTH(tanggal)
            ORDER BY MIN(tanggal) ASC
        ");
        return $query->getResultArray();
    }
}
