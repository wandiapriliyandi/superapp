<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\AkunModel;
use Keuangan\Models\JurnalDetailModel;

class BukuBesar extends BaseController
{
    public function index()
    {
        $akunModel = new AkunModel();
        $data = [
            'title' => 'Buku Besar',
            'akun' => $akunModel->where('is_aktif', 1)->orderBy('kode_akun', 'ASC')->findAll(),
            'selected_akun' => $this->request->getGet('akun_id'),
            'tgl_mulai' => $this->request->getGet('tgl_mulai') ?: date('Y-m-01'),
            'tgl_selesai' => $this->request->getGet('tgl_selesai') ?: date('Y-m-t'),
            'transaksi' => [],
            'saldo_awal' => 0,
        ];

        if ($data['selected_akun']) {
            $detailModel = new JurnalDetailModel();
            
            // Hitung Saldo Awal (sebelum tgl_mulai)
            // Ini penyederhanaan, aslinya perlu cek kategori akun
            $akun = $akunModel->find($data['selected_akun']);
            $prev = $detailModel->selectSum('debit')
                                ->selectSum('kredit')
                                ->join('keu_jurnal', 'keu_jurnal.id = keu_jurnal_detail.jurnal_id')
                                ->where('akun_id', $data['selected_akun'])
                                ->where('keu_jurnal.tanggal <', $data['tgl_mulai'])
                                ->first();
            
            if ($akun['saldo_normal'] == 'Debit') {
                $data['saldo_awal'] = ($prev['debit'] ?? 0) - ($prev['kredit'] ?? 0);
            } else {
                $data['saldo_awal'] = ($prev['kredit'] ?? 0) - ($prev['debit'] ?? 0);
            }

            // Ambil Transaksi dalam periode
            $data['transaksi'] = $detailModel->select('keu_jurnal_detail.*, keu_jurnal.tanggal, keu_jurnal.nomor_jurnal, keu_jurnal.keterangan as ket_jurnal')
                                            ->join('keu_jurnal', 'keu_jurnal.id = keu_jurnal_detail.jurnal_id')
                                            ->where('akun_id', $data['selected_akun'])
                                            ->where('keu_jurnal.tanggal >=', $data['tgl_mulai'])
                                            ->where('keu_jurnal.tanggal <=', $data['tgl_selesai'])
                                            ->orderBy('keu_jurnal.tanggal', 'ASC')
                                            ->findAll();
        }

        return view('Keuangan\Views\buku_besar\index', $data);
    }
}
