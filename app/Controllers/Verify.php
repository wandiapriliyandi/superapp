<?php

namespace App\Controllers;

use App\Models\SantriModel;
use Spp\Models\SppPembayaranModel;
use Spp\Models\SppSantriTarifModel;

class Verify extends BaseController
{
    public function receipt($no_trx)
    {
        $pembayaranModel = new SppPembayaranModel();
        $santriModel = new SantriModel();

        $details = $pembayaranModel->select('spp_pembayaran.*, spp_tarif.nama_tarif, santri.nisn')
            ->join('spp_tagihan', 'spp_tagihan.id = spp_pembayaran.tagihan_id')
            ->join('santri', 'santri.id = spp_tagihan.santri_id')
            ->join('spp_tarif', 'spp_tarif.id = spp_tagihan.tarif_id')
            ->where('spp_pembayaran.nomor_transaksi', $no_trx)
            ->findAll();

        if (empty($details)) {
            return "<h3>Data transaksi tidak ditemukan atau tidak valid.</h3>";
        }

        $santri = $santriModel->where('nisn', $details[0]['nisn'])->first();

        $data = [
            'title' => 'Verifikasi Kwitansi',
            'no_trx' => $no_trx,
            'details' => $details,
            'santri' => $santri,
            'hide_sidebar' => true // Agar tampilan bersih di mobile/scan
        ];

        return view('verify/receipt', $data);
    }

    public function agreement($nisn)
    {
        $santriModel = new SantriModel();
        $mappingModel = new SppSantriTarifModel();

        $santri = $santriModel->where('nisn', $nisn)->first();
        if (!$santri) {
            return "<h3>Data santri tidak ditemukan.</h3>";
        }

        $mapping = $mappingModel->select('spp_santri_tarif.*, spp_tarif.nama_tarif, spp_tarif.tipe, spp_tarif.nominal')
            ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
            ->where('spp_santri_tarif.nisn', $nisn)
            ->findAll();

        $data = [
            'title' => 'Verifikasi Kesepakatan',
            'santri' => $santri,
            'mapping' => $mapping,
            'hide_sidebar' => true
        ];

        return view('verify/agreement', $data);
    }
}
