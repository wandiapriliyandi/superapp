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
            'sisa'    => $tagihan['nominal_tagihan'] - $tagihan['total_terbayar']
        ];
        return view('Keuangan\Views\pembayaran\bayar', $data);
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
