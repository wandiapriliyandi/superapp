<?php

namespace Spp\Controllers;

use App\Controllers\BaseController;
use Spp\Models\SppTagihanModel;
use Spp\Models\SppPembayaranModel;
use App\Traits\Exportable;

class Pembayaran extends BaseController
{
    use Exportable;
    protected $tagihanModel;
    protected $pembayaranModel;

    public function __construct()
    {
        $this->tagihanModel = new SppTagihanModel();
        $this->pembayaranModel = new SppPembayaranModel();
    }

    public function index()
    {
        // Untuk saat ini, index pembayaran bisa menampilkan histori pembayaran terakhir
        $data = [
            'title' => 'Riwayat Pembayaran',
            'pembayaran' => $this->pembayaranModel
                                 ->select('spp_pembayaran.*, santri.nama_lengkap as nama_santri')
                                 ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                 ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                 ->orderBy('spp_pembayaran.created_at', 'DESC')
                                 ->findAll()
        ];
        return view('Spp\Views\pembayaran\index', $data);
    }

    public function cari()
    {
        $nisn = $this->request->getGet('nisn');
        $q = $this->request->getGet('q');
        
        $santriModel = new \App\Models\SantriModel();
        $results = [];
        $selected_santri = null;
        $tagihan = [];

        if ($q) {
            $results = $santriModel->like('nama_lengkap', $q)->orLike('nisn', $q)->limit(10)->findAll();
        }

        if ($nisn) {
            $selected_santri = $santriModel->where('nisn', $nisn)->first();
            if ($selected_santri) {
                $tagihanModel = new \Spp\Models\SppTagihanModel();
                $tagihan = $tagihanModel->getUnpaidBySantri($nisn);

                // Fetch Payment History
                $pembayaranModel = new \Spp\Models\SppPembayaranModel();
                $history = $pembayaranModel->select('spp_pembayaran.*, spp_tarif.nama_tarif, spp_tagihan.bulan, spp_tagihan.tahun')
                                           ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                           ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                                           ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                           ->where('santri.nisn', $nisn)
                                           ->orderBy('spp_pembayaran.created_at', 'DESC')
                                           ->findAll();
            }
        }

        $data = [
            'title'   => 'Bayar SPP',
            'results' => $results,
            'q'       => $q,
            'selected_santri' => $selected_santri,
            'tagihan' => $tagihan,
            'history' => $history ?? []
        ];

        return view('Spp\Views\pembayaran\cari', $data);
    }

    public function bayar($tagihan_id)
    {
        $tagihan = $this->tagihanModel
                        ->select('spp_tagihan.*, santri.nisn, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                        ->join('santri', 'santri.id = spp_tagihan.santri_id')
                        ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                        ->find($tagihan_id);

        if (!$tagihan) return redirect()->to(base_url('spp/tagihan'))->with('error', 'Tagihan tidak ditemukan.');

        $data = [
            'title'   => 'Input Pembayaran',
            'tagihan' => $tagihan,
            'sisa'    => $tagihan['nominal_tagihan'] - $tagihan['diskon'] - $tagihan['total_terbayar'],
            'history' => $this->pembayaranModel->where('tagihan_id', $tagihan_id)->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('Spp\Views\pembayaran\bayar', $data);
    }

    public function bayarMassal()
    {
        $tagihan_ids = $this->request->getPost('tagihan_ids');
        $nominal_bayars = $this->request->getPost('nominal_bayar');
        $metode = $this->request->getPost('metode_pembayaran');
        $nisn = $this->request->getPost('nisn');

        if (empty($tagihan_ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu tagihan.');
        }

        $pembayaranModel = new \Spp\Models\SppPembayaranModel();
        $tagihanModel = new \Spp\Models\SppTagihanModel();
        
        // Generate Nomor Transaksi: TRX-20231027-NISN-RAND
        $no_transaksi = 'TRX-' . date('Ymd') . '-' . $nisn . '-' . strtoupper(bin2hex(random_bytes(2)));

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($tagihan_ids as $tid) {
            $nominal = $nominal_bayars[$tid] ?? 0;
            if ($nominal <= 0) continue;

            $tagihan = $tagihanModel->find($tid);
            if (!$tagihan) continue;

            // 1. Simpan Pembayaran
            $pembayaranModel->save([
                'tagihan_id'        => $tid,
                'nomor_transaksi'   => $no_transaksi,
                'nominal_bayar'     => $nominal,
                'tanggal_bayar'     => date('Y-m-d'),
                'metode_pembayaran' => $metode,
                'recorded_by'       => session()->get('user_id') ?? 1
            ]);

            // 2. Update Tagihan
            $new_total = $tagihan['total_terbayar'] + $nominal;
            $status = ($new_total >= ($tagihan['nominal_tagihan'] - $tagihan['diskon'])) ? 'Lunas' : 'Cicilan';

            $tagihanModel->update($tid, [
                'total_terbayar' => $new_total,
                'status'         => $status
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran.');
        }

        // INTEGRASI KEUANGAN: Catat ke Jurnal Umum
        $total_bayar = array_sum($nominal_bayars);
        $santriModel = new \App\Models\SantriModel();
        $santri = $santriModel->where('nisn', $nisn)->first();
        $this->recordToJournal($no_transaksi, date('Y-m-d'), $total_bayar, $santri['nama_lengkap'] ?? 'Santri');

        return redirect()->to(base_url('spp/pembayaran/kwitansi/' . $no_transaksi))->with('success', 'Pembayaran berhasil diproses.');
    }

    public function transaksi()
    {
        $tgl_mulai   = $this->request->getGet('tgl_mulai');
        $tgl_selesai = $this->request->getGet('tgl_selesai');

        $pembayaranModel = new \Spp\Models\SppPembayaranModel();
        
        $query = $pembayaranModel->select('spp_pembayaran.nomor_transaksi, spp_pembayaran.tanggal_bayar, spp_pembayaran.metode_pembayaran, spp_pembayaran.created_at, SUM(spp_pembayaran.nominal_bayar) as total_bayar, santri.nama_lengkap, santri.nisn')
                                ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                ->join('santri', 'santri.id = spp_tagihan.santri_id');

        if ($tgl_mulai)   $query->where('spp_pembayaran.tanggal_bayar >=', $tgl_mulai);
        if ($tgl_selesai) $query->where('spp_pembayaran.tanggal_bayar <=', $tgl_selesai);

        $list = $query->groupBy('spp_pembayaran.nomor_transaksi')
                      ->orderBy('spp_pembayaran.created_at', 'DESC')
                      ->findAll();

        $data = [
            'title' => 'Riwayat Transaksi (Kwitansi)',
            'list'  => $list,
            'filter'=> [
                'tgl_mulai'   => $tgl_mulai,
                'tgl_selesai' => $tgl_selesai
            ]
        ];

        return view('Spp\Views\pembayaran\transaksi', $data);
    }

    public function export_transaksi($format)
    {
        $tgl_mulai   = $this->request->getGet('tgl_mulai');
        $tgl_selesai = $this->request->getGet('tgl_selesai');

        $pembayaranModel = new \Spp\Models\SppPembayaranModel();
        $query = $pembayaranModel->select('spp_pembayaran.nomor_transaksi, spp_pembayaran.tanggal_bayar, spp_pembayaran.metode_pembayaran, SUM(spp_pembayaran.nominal_bayar) as total_bayar, santri.nama_lengkap')
                                ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                ->join('santri', 'santri.id = spp_tagihan.santri_id');

        if ($tgl_mulai)   $query->where('spp_pembayaran.tanggal_bayar >=', $tgl_mulai);
        if ($tgl_selesai) $query->where('spp_pembayaran.tanggal_bayar <=', $tgl_selesai);

        $data = $query->groupBy('spp_pembayaran.nomor_transaksi')
                      ->orderBy('spp_pembayaran.tanggal_bayar', 'DESC')
                      ->findAll();

        $data_export = [];
        foreach ($data as $row) {
            $data_export[] = [
                'No. Transaksi' => $row['nomor_transaksi'],
                'Tanggal'       => date('d-m-Y', strtotime($row['tanggal_bayar'])),
                'Nama Santri'   => $row['nama_lengkap'],
                'Total Bayar'   => (float)$row['total_bayar'],
                'Metode'        => $row['metode_pembayaran']
            ];
        }

        if ($format == 'excel') {
            return $this->exportToExcel($data_export, "Riwayat_Transaksi_SPP_" . date('Ymd'));
        } elseif ($format == 'word') {
            return $this->exportToWord($data_export, "Riwayat Transaksi SPP", "Riwayat_Transaksi_SPP_" . date('Ymd'));
        } elseif ($format == 'pdf') {
            return $this->exportToPdf($data_export, "Riwayat Transaksi SPP");
        }
    }

    public function kwitansi($no_transaksi)
    {
        $pembayaranModel = new \Spp\Models\SppPembayaranModel();
        $details = $pembayaranModel->select('spp_pembayaran.*, spp_tarif.nama_tarif, spp_tagihan.bulan, spp_tagihan.tahun, santri.nama_lengkap, santri.nisn')
                                   ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                   ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                                   ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                   ->where('spp_pembayaran.nomor_transaksi', $no_transaksi)
                                   ->findAll();

        if (empty($details)) return redirect()->to(base_url('spp/pembayaran/cari'))->with('error', 'Data kwitansi tidak ditemukan.');

        $data = [
            'title'   => 'Kwitansi Pembayaran',
            'details' => $details,
            'no_trx'  => $no_transaksi,
            'santri'  => $details[0] // Info santri dari baris pertama
        ];

        return view('Spp\Views\pembayaran\kwitansi', $data);
    }

    public function detail($no_transaksi)
    {
        try {
            $pembayaranModel = new \Spp\Models\SppPembayaranModel();
            $details = $pembayaranModel
                ->select('spp_pembayaran.*, spp_tarif.nama_tarif, spp_tagihan.bulan, spp_tagihan.tahun, santri.nama_lengkap, santri.nisn')
                ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                ->join('santri', 'santri.id = spp_tagihan.santri_id')
                ->where('spp_pembayaran.nomor_transaksi', $no_transaksi)
                ->findAll();

            return $this->response->setJSON([
                'success' => !empty($details),
                'data'    => $details
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function save()
    {
        helper('activity');
        $tagihan_id = $this->request->getPost('tagihan_id');
        $nominal_bayar = $this->request->getPost('nominal_bayar');
        
        $tagihan = $this->tagihanModel
                        ->select('spp_tagihan.*, santri.nama_lengkap')
                        ->join('santri', 'santri.id = spp_tagihan.santri_id')
                        ->find($tagihan_id);
        
        // Simpan Log Pembayaran
        $no_trx_single = 'TRX-S-' . date('Ymd') . '-' . $tagihan_id . '-' . rand(100, 999);
        $this->pembayaranModel->save([
            'tagihan_id'        => $tagihan_id,
            'nomor_transaksi'   => $no_trx_single,
            'tanggal_bayar'     => date('Y-m-d'),
            'nominal_bayar'     => $nominal_bayar,
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'keterangan'        => $this->request->getPost('keterangan'),
            'recorded_by'       => null // Hardcoded null for now
        ]);

        // Update Total Terbayar di Tagihan
        $new_total = $tagihan['total_terbayar'] + $nominal_bayar;
        $status = 'Cicilan';
        if ($new_total >= ($tagihan['nominal_tagihan'] - $tagihan['diskon'])) {
            $status = 'Lunas';
        }

        $this->tagihanModel->update($tagihan_id, [
            'total_terbayar' => $new_total,
            'status'         => $status
        ]);

        // INTEGRASI KEUANGAN: Catat ke Jurnal Umum
        $this->recordToJournal($no_trx_single, date('Y-m-d'), $nominal_bayar, $tagihan['nama_lengkap'] ?? 'Santri');

        log_activity('Menerima Pembayaran SPP', 'Spp', 'Santri: ' . $tagihan['nama_lengkap'] . ', Nominal: ' . $nominal_bayar);

        return redirect()->to(base_url('spp/tagihan'))->with('success', 'Pembayaran berhasil disimpan.');
    }

    private function recordToJournal($no_trx, $tanggal, $nominal, $santri_name)
    {
        $db = \Config\Database::connect();
        
        // Cari Akun (Kas Utama dan Pendapatan SPP)
        $akunKas = $db->table('keu_akun')->where('kode_akun', '1-101')->get()->getRowArray();
        $akunSpp = $db->table('keu_akun')->where('kode_akun', '4-101')->get()->getRowArray();
        
        if (!$akunKas || !$akunSpp) return;

        // Cek apakah nomor jurnal sudah ada (untuk menghindari duplikasi jika page direfresh)
        $exists = $db->table('keu_jurnal')->where('nomor_jurnal', 'JV-' . $no_trx)->get()->getRow();
        if ($exists) return;

        // Insert Jurnal Header
        $jurnalData = [
            'nomor_jurnal' => 'JV-' . $no_trx,
            'tanggal'      => $tanggal,
            'keterangan'   => 'Penerimaan SPP - ' . $santri_name . ' (' . $no_trx . ')',
            'referensi'    => $no_trx,
            'jenis_jurnal' => 'Kas Masuk',
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s')
        ];
        $db->table('keu_jurnal')->insert($jurnalData);
        $jurnalId = $db->insertID();

        // Insert Jurnal Detail (Debit Kas)
        $db->table('keu_jurnal_detail')->insert([
            'jurnal_id' => $jurnalId,
            'akun_id'   => $akunKas['id'],
            'debit'     => $nominal,
            'kredit'    => 0,
            'keterangan_item' => 'Penerimaan Kas SPP'
        ]);

        // Insert Jurnal Detail (Kredit Pendapatan SPP)
        $db->table('keu_jurnal_detail')->insert([
            'jurnal_id' => $jurnalId,
            'akun_id'   => $akunSpp['id'],
            'debit'     => 0,
            'kredit'    => $nominal,
            'keterangan_item' => 'Pendapatan SPP'
        ]);
    }
}
