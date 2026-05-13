<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\SppTagihanModel;
use Keuangan\Models\SppPembayaranModel;

class Pembayaran extends BaseController
{
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
                                 ->orderBy('created_at', 'DESC')
                                 ->findAll()
        ];
        return view('Keuangan\Views\pembayaran\index', $data);
    }

    public function cari()
    {
        $santri_id = $this->request->getGet('santri_id');
        $q = $this->request->getGet('q');
        
        $santriModel = new \App\Models\SantriModel();
        $results = [];
        $selected_santri = null;
        $tagihan = [];

        if ($q) {
            $results = $santriModel->like('nama_lengkap', $q)->orLike('nisn', $q)->limit(10)->findAll();
        }

        if ($santri_id) {
            $selected_santri = $santriModel->find($santri_id);
            if ($selected_santri) {
                $tagihanModel = new \Keuangan\Models\SppTagihanModel();
                $tagihan = $tagihanModel->getUnpaidBySantri($santri_id);

                // Fetch Payment History
                $pembayaranModel = new \Keuangan\Models\SppPembayaranModel();
                $history = $pembayaranModel->select('spp_pembayaran.*, spp_tarif.nama_tarif, spp_tagihan.bulan, spp_tagihan.tahun')
                                           ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                           ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                                           ->where('spp_tagihan.santri_id', $santri_id)
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

        return view('Keuangan\Views\pembayaran\cari', $data);
    }

    public function bayar($tagihan_id)
    {
        $tagihan = $this->tagihanModel
                        ->select('spp_tagihan.*, santri.nama_lengkap as nama_santri, spp_tarif.nama_tarif')
                        ->join('santri', 'santri.id = spp_tagihan.santri_id')
                        ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                        ->find($tagihan_id);

        if (!$tagihan) return redirect()->to(base_url('keuangan/tagihan'))->with('error', 'Tagihan tidak ditemukan.');

        $data = [
            'title'   => 'Input Pembayaran',
            'tagihan' => $tagihan,
            'sisa'    => $tagihan['nominal_tagihan'] - $tagihan['total_terbayar'],
            'history' => $this->pembayaranModel->where('tagihan_id', $tagihan_id)->orderBy('created_at', 'DESC')->findAll()
        ];
        return view('Keuangan\Views\pembayaran\bayar', $data);
    }

    public function bayarMassal()
    {
        $tagihan_ids = $this->request->getPost('tagihan_ids');
        $nominal_bayars = $this->request->getPost('nominal_bayar');
        $metode = $this->request->getPost('metode_pembayaran');
        $santri_id = $this->request->getPost('santri_id');

        if (empty($tagihan_ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu tagihan.');
        }

        $pembayaranModel = new \Keuangan\Models\SppPembayaranModel();
        $tagihanModel = new \Keuangan\Models\SppTagihanModel();
        
        // Generate Nomor Transaksi: TRX-20231027-ID-RAND
        $no_transaksi = 'TRX-' . date('Ymd') . '-' . $santri_id . '-' . strtoupper(bin2hex(random_bytes(2)));

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
            $status = ($new_total >= $tagihan['nominal_tagihan']) ? 'Lunas' : 'Cicilan';

            $tagihanModel->update($tid, [
                'total_terbayar' => $new_total,
                'status'         => $status
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran.');
        }

        return redirect()->to(base_url('keuangan/pembayaran/kwitansi/' . $no_transaksi))->with('success', 'Pembayaran berhasil diproses.');
    }

    public function transaksi()
    {
        $pembayaranModel = new \Keuangan\Models\SppPembayaranModel();
        
        // Grouping by nomor_transaksi to show unique receipts
        $list = $pembayaranModel->select('spp_pembayaran.nomor_transaksi, spp_pembayaran.tanggal_bayar, spp_pembayaran.metode_pembayaran, spp_pembayaran.created_at, SUM(spp_pembayaran.nominal_bayar) as total_bayar, santri.nama_lengkap, santri.nisn')
                                ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                ->groupBy('spp_pembayaran.nomor_transaksi')
                                ->orderBy('spp_pembayaran.created_at', 'DESC')
                                ->findAll();

        $data = [
            'title' => 'Riwayat Transaksi (Kwitansi)',
            'list'  => $list
        ];

        return view('Keuangan\Views\pembayaran\transaksi', $data);
    }

    public function kwitansi($no_transaksi)
    {
        $pembayaranModel = new \Keuangan\Models\SppPembayaranModel();
        $details = $pembayaranModel->select('spp_pembayaran.*, spp_tarif.nama_tarif, spp_tagihan.bulan, spp_tagihan.tahun, santri.nama_lengkap, santri.nisn')
                                   ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                   ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                                   ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                   ->where('spp_pembayaran.nomor_transaksi', $no_transaksi)
                                   ->findAll();

        if (empty($details)) return redirect()->to(base_url('keuangan/pembayaran/cari'))->with('error', 'Data kwitansi tidak ditemukan.');

        $data = [
            'title'   => 'Kwitansi Pembayaran',
            'details' => $details,
            'no_trx'  => $no_transaksi,
            'santri'  => $details[0] // Info santri dari baris pertama
        ];

        return view('Keuangan\Views\pembayaran\kwitansi', $data);
    }

    public function detail($no_transaksi)
    {
        $pembayaranModel = new \Keuangan\Models\SppPembayaranModel();
        $details = $pembayaranModel->select('spp_pembayaran.*, spp_tarif.nama_tarif, spp_tagihan.bulan, spp_tagihan.tahun, santri.nama_lengkap, santri.nisn')
                                   ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
                                   ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
                                   ->join('santri', 'santri.id = spp_tagihan.santri_id')
                                   ->where('spp_pembayaran.nomor_transaksi', $no_transaksi)
                                   ->findAll();

        return $this->response->setJSON([
            'success' => !empty($details),
            'data'    => $details
        ]);
    }

    public function save()
    {
        helper('activity');
        $tagihan_id = $this->request->getPost('tagihan_id');
        $nominal_bayar = $this->request->getPost('nominal_bayar');
        
        $tagihan = $this->tagihanModel
                        ->join('santri', 'santri.id = spp_tagihan.santri_id')
                        ->find($tagihan_id);
        
        // Simpan Log Pembayaran
        $this->pembayaranModel->save([
            'tagihan_id'        => $tagihan_id,
            'tanggal_bayar'     => date('Y-m-d'),
            'nominal_bayar'     => $nominal_bayar,
            'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
            'keterangan'        => $this->request->getPost('keterangan'),
            'recorded_by'       => null // Hardcoded null for now
        ]);

        // Update Total Terbayar di Tagihan
        $new_total = $tagihan['total_terbayar'] + $nominal_bayar;
        $status = 'Cicilan';
        if ($new_total >= $tagihan['nominal_tagihan']) {
            $status = 'Lunas';
        }

        $this->tagihanModel->update($tagihan_id, [
            'total_terbayar' => $new_total,
            'status'         => $status
        ]);

        log_activity('Menerima Pembayaran SPP', 'Keuangan', 'Santri: ' . $tagihan['nama_lengkap'] . ', Nominal: ' . $nominal_bayar);

        return redirect()->to(base_url('keuangan/tagihan'))->with('success', 'Pembayaran berhasil disimpan.');
    }
}
