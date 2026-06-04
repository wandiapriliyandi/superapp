<?php

namespace Poskestren\Controllers;

use App\Controllers\BaseController;
use Poskestren\Models\ObatModel;
use Poskestren\Models\StokMutasiModel;

class Stok extends BaseController
{
    protected $obatModel;
    protected $stokMutasiModel;

    public function __construct()
    {
        $this->obatModel       = new ObatModel();
        $this->stokMutasiModel = new StokMutasiModel();
    }

    public function riwayat()
    {
        $obatId = $this->request->getGet('obat_id');

        $data = [
            'title'   => 'Kartu Stok Obat',
            'obat'    => $this->obatModel->orderBy('nama_obat', 'ASC')->findAll(),
            'filter'  => ['obat_id' => $obatId],
            'mutasi'  => $this->stokMutasiModel->getRiwayat($obatId ? (int) $obatId : null),
        ];

        return view('Poskestren\Views\stok\riwayat', $data);
    }

    public function pengadaan()
    {
        $data = [
            'title' => 'Pengadaan Obat (Stok Masuk)',
            'obat'  => $this->obatModel->orderBy('nama_obat', 'ASC')->findAll(),
        ];

        return view('Poskestren\Views\stok\pengadaan', $data);
    }

    public function simpan_pengadaan()
    {
        $items   = $this->request->getPost('items');
        $ket     = trim((string) $this->request->getPost('keterangan'));
        $tanggal = $this->request->getPost('tanggal');

        if (empty($items) || !is_array($items)) {
            return redirect()->back()->with('error', 'Belum ada data obat yang diinput.')->withInput();
        }

        $createdAt = null;
        if (!empty($tanggal)) {
            $createdAt = date('Y-m-d H:i:s', strtotime($tanggal));
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $successCount = 0;
        foreach ($items as $item) {
            $obatId = (int) ($item['obat_id'] ?? 0);
            $jumlah = (int) ($item['jumlah'] ?? 0);

            if (!$obatId || !$jumlah) {
                continue;
            }

            $result = $this->stokMutasiModel->catat(
                $obatId,
                $jumlah,
                'masuk',
                'pengadaan',
                $ket ?: 'Pengadaan obat',
                null,
                null,
                session()->get('user_id'),
                false,
                $createdAt
            );

            if (!$result['ok']) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Gagal mencatat pengadaan: ' . $result['message'])->withInput();
            }
            $successCount++;
        }

        if ($successCount === 0) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Pilih minimal satu obat dan isi jumlahnya.')->withInput();
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan pengadaan.')->withInput();
        }

        return redirect()->to(base_url('poskestren/stok/riwayat'))
            ->with('success', 'Berhasil mencatat pengadaan untuk ' . $successCount . ' jenis obat.');
    }

    public function keluar()
    {
        $data = [
            'title' => 'Barang Keluar (Musnah / Buang)',
            'obat'  => $this->obatModel->where('stok >', 0)->orderBy('nama_obat', 'ASC')->findAll(),
        ];

        return view('Poskestren\Views\stok\keluar', $data);
    }

    public function simpan_keluar()
    {
        $obatId = (int) $this->request->getPost('obat_id');
        $jumlah = (int) $this->request->getPost('jumlah');
        $ket    = trim((string) $this->request->getPost('keterangan'));

        if (!$obatId || !$jumlah) {
            return redirect()->back()->with('error', 'Pilih obat dan isi jumlah.')->withInput();
        }

        if ($ket === '') {
            return redirect()->back()->with('error', 'Keterangan wajib diisi (mis: kadaluarsa, rusak).')->withInput();
        }

        $result = $this->stokMutasiModel->catat(
            $obatId,
            $jumlah,
            'keluar',
            'musnah',
            $ket,
            null,
            null,
            session()->get('user_id')
        );

        if (!$result['ok']) {
            return redirect()->back()->with('error', $result['message'])->withInput();
        }

        return redirect()->to(base_url('poskestren/stok/riwayat?obat_id=' . $obatId))
            ->with('success', 'Barang keluar dicatat. Stok berkurang ' . $jumlah . '.');
    }
}
