<?php

namespace App\Controllers;

use App\Models\SantriModel;
use Keuangan\Models\SppPembayaranModel;
use Keuangan\Models\SppSantriTarifModel;

class Verify extends BaseController
{
    public function receipt($no_trx)
    {
        $pembayaranModel = new SppPembayaranModel();
        $santriModel = new SantriModel();

        $details = $pembayaranModel->select('spp_pembayaran.*, spp_tarif.nama_tarif')
            ->join('spp_tarif', 'spp_tarif.id = spp_pembayaran.id_tarif')
            ->where('no_trx', $no_trx)
            ->findAll();

        if (empty($details)) {
            return "<h3>Data transaksi tidak ditemukan atau tidak valid.</h3>";
        }

        $santri = $santriModel->find($details[0]['id_santri']);

        $data = [
            'title' => 'Verifikasi Kwitansi',
            'no_trx' => $no_trx,
            'details' => $details,
            'santri' => $santri,
            'hide_sidebar' => true // Agar tampilan bersih di mobile/scan
        ];

        return view('verify/receipt', $data);
    }

    public function agreement($id_santri)
    {
        $santriModel = new SantriModel();
        $mappingModel = new SppSantriTarifModel();

        $santri = $santriModel->find($id_santri);
        if (!$santri) {
            return "<h3>Data santri tidak ditemukan.</h3>";
        }

        $mapping = $mappingModel->select('spp_santri_tarif.*, spp_tarif.nama_tarif, spp_tarif.tipe, spp_tarif.nominal')
            ->join('spp_tarif', 'spp_tarif.id = spp_santri_tarif.tarif_id')
            ->where('spp_santri_tarif.santri_id', $id_santri)
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
