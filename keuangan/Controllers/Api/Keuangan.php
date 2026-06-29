<?php

namespace Keuangan\Controllers\Api;

use App\Controllers\BaseController;
use Keuangan\Models\AkunModel;
use Keuangan\Models\JurnalModel;
use Keuangan\Models\JurnalDetailModel;

class Keuangan extends BaseController
{
    protected $akunModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;

    public function __construct()
    {
        $this->akunModel         = new AkunModel();
        $this->jurnalModel       = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
    }

    // ===================== AKUN / COA =====================

    public function indexAkun()
    {
        $data = $this->akunModel->orderBy('kode_akun', 'ASC')->findAll();
        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Data akun berhasil diambil',
            'data'    => $data
        ]);
    }

    public function saveAkun()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['kode_akun']) || empty($data['nama_akun']) || empty($data['kategori'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Kode, nama, dan kategori wajib diisi']);
        }

        $payload = [
            'id'           => !empty($data['id']) ? $data['id'] : null,
            'kode_akun'    => $data['kode_akun'],
            'nama_akun'    => $data['nama_akun'],
            'kategori'     => $data['kategori'],
            'parent_id'    => !empty($data['parent_id']) ? (int) $data['parent_id'] : null,
            'saldo_normal' => $data['saldo_normal'] ?? 'Debit',
            'is_aktif'     => isset($data['is_aktif']) ? (int) $data['is_aktif'] : 1,
        ];

        $isUpdate = !empty($data['id']);
        $this->akunModel->save($payload);

        $aksi = $isUpdate ? 'Mengubah' : 'Menambah';
        log_activity($aksi . ' Akun Keuangan (API)', 'Keuangan', 'Nama Akun: ' . $data['nama_akun']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Akun berhasil disimpan']);
    }

    public function deleteAkun($id)
    {
        helper('activity');
        $akun = $this->akunModel->find($id);
        if (!$akun) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Akun tidak ditemukan']);
        }

        $this->akunModel->delete($id);
        log_activity('Menghapus Akun Keuangan (API)', 'Keuangan', 'Nama: ' . $akun['nama_akun']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Akun berhasil dihapus']);
    }

    // ===================== JURNAL UMUM =====================

    public function indexJurnal()
    {
        $jurnals = $this->jurnalModel->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll();
        foreach ($jurnals as &$j) {
            $j['details'] = $this->jurnalDetailModel->getByJurnal($j['id']);
        }

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $jurnals
        ]);
    }

    public function getJurnalNomor()
    {
        $prefix = $this->request->getGet('prefix') ?? 'JV';
        return $this->response->setJSON([
            'status' => 200,
            'nomor'  => $this->jurnalModel->generateNumber($prefix)
        ]);
    }

    public function saveJurnal()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['tanggal']) || empty($data['keterangan']) || empty($data['details'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Tanggal, keterangan, dan detail item wajib diisi']);
        }

        // Validate Balance
        $totalDebit  = 0;
        $totalKredit = 0;
        foreach ($data['details'] as $detail) {
            $totalDebit  += (float) ($detail['debit'] ?? 0);
            $totalKredit += (float) ($detail['kredit'] ?? 0);
        }

        if (abs($totalDebit - $totalKredit) > 0.01) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Total Debit dan Kredit tidak balance!']);
        }

        if ($totalDebit <= 0) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nominal transaksi harus lebih besar dari 0']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $prefix    = $data['jenis_jurnal'] === 'Kas Masuk' ? 'KM' : ($data['jenis_jurnal'] === 'Kas Keluar' ? 'KK' : 'JV');
        $jurnal_id = $this->jurnalModel->insert([
            'nomor_jurnal' => $this->jurnalModel->generateNumber($prefix),
            'tanggal'      => $data['tanggal'],
            'keterangan'   => $data['keterangan'],
            'referensi'    => $data['referensi'] ?? '',
            'jenis_jurnal' => $data['jenis_jurnal'] ?? 'Umum',
        ]);

        foreach ($data['details'] as $detail) {
            if (($detail['debit'] ?? 0) > 0 || ($detail['kredit'] ?? 0) > 0) {
                $this->jurnalDetailModel->insert([
                    'jurnal_id'       => $jurnal_id,
                    'akun_id'         => $detail['akun_id'],
                    'debit'           => (float) ($detail['debit'] ?? 0),
                    'kredit'          => (float) ($detail['kredit'] ?? 0),
                    'keterangan_item' => $detail['keterangan_item'] ?? null,
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal menyimpan transaksi jurnal']);
        }

        log_activity('Menyimpan Jurnal Keuangan (API)', 'Keuangan', 'No Jurnal: ' . $prefix);

        return $this->response->setJSON(['status' => 200, 'message' => 'Transaksi jurnal berhasil disimpan']);
    }

    public function deleteJurnal($id)
    {
        helper('activity');
        $j = $this->jurnalModel->find($id);
        if (!$j) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Jurnal tidak ditemukan']);
        }

        $this->jurnalModel->delete($id);
        log_activity('Menghapus Jurnal Keuangan (API)', 'Keuangan', 'No Jurnal: ' . $j['nomor_jurnal']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Transaksi jurnal berhasil dihapus']);
    }

    // ===================== BUKU BESAR =====================

    public function indexBukuBesar()
    {
        $akunId     = $this->request->getGet('akun_id');
        $tglMulai   = $this->request->getGet('tgl_mulai') ?: date('Y-m-01');
        $tglSelesai = $this->request->getGet('tgl_selesai') ?: date('Y-m-t');

        if (!$akunId) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Akun wajib dipilih']);
        }

        $akun = $this->akunModel->find($akunId);
        if (!$akun) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Akun tidak valid']);
        }

        // Saldo Awal
        $prev = $this->jurnalDetailModel->selectSum('debit')->selectSum('kredit')
            ->join('keu_jurnal', 'keu_jurnal.id = keu_jurnal_detail.jurnal_id')
            ->where('akun_id', $akunId)
            ->where('keu_jurnal.tanggal <', $tglMulai)
            ->first();

        $saldoAwal = 0;
        if ($akun['saldo_normal'] == 'Debit') {
            $saldoAwal = ((float)$prev['debit'] ?? 0) - ((float)$prev['kredit'] ?? 0);
        } else {
            $saldoAwal = ((float)$prev['kredit'] ?? 0) - ((float)$prev['debit'] ?? 0);
        }

        // Transaksi
        $transaksi = $this->jurnalDetailModel->select('keu_jurnal_detail.*, keu_jurnal.tanggal, keu_jurnal.nomor_jurnal, keu_jurnal.keterangan as ket_jurnal')
            ->join('keu_jurnal', 'keu_jurnal.id = keu_jurnal_detail.jurnal_id')
            ->where('akun_id', $akunId)
            ->where('keu_jurnal.tanggal >=', $tglMulai)
            ->where('keu_jurnal.tanggal <=', $tglSelesai)
            ->orderBy('keu_jurnal.tanggal', 'ASC')
            ->findAll();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'saldo_awal' => $saldoAwal,
                'transaksi'  => $transaksi,
                'akun'       => $akun
            ]
        ]);
    }

    // ===================== LAPORAN =====================

    public function laporanLabaRugi()
    {
        $tglMulai   = $this->request->getGet('tgl_mulai') ?: date('Y-m-01');
        $tglSelesai = $this->request->getGet('tgl_selesai') ?: date('Y-m-t');

        // Ambil semua akun pendapatan dan beban
        $akuns = $this->akunModel->whereIn('kategori', ['Pendapatan', 'Beban'])->orderBy('kode_akun', 'ASC')->findAll();

        $dataPendapatan = [];
        $dataBeban      = [];
        $totalPendapatan = 0;
        $totalBeban      = 0;

        foreach ($akuns as $a) {
            $sum = $this->jurnalDetailModel->selectSum('debit')->selectSum('kredit')
                ->join('keu_jurnal', 'keu_jurnal.id = keu_jurnal_detail.jurnal_id')
                ->where('akun_id', $a['id'])
                ->where('keu_jurnal.tanggal >=', $tglMulai)
                ->where('keu_jurnal.tanggal <=', $tglSelesai)
                ->first();

            $debit  = (float) ($sum['debit'] ?? 0);
            $kredit = (float) ($sum['kredit'] ?? 0);
            $saldo  = 0;

            if ($a['saldo_normal'] === 'Debit') {
                $saldo = $debit - $kredit;
            } else {
                $saldo = $kredit - $debit;
            }

            if ($saldo != 0) {
                $item = [
                    'kode_akun' => $a['kode_akun'],
                    'nama_akun' => $a['nama_akun'],
                    'saldo'     => $saldo
                ];
                if ($a['kategori'] === 'Pendapatan') {
                    $dataPendapatan[] = $item;
                    $totalPendapatan  += $saldo;
                } else {
                    $dataBeban[] = $item;
                    $totalBeban  += $saldo;
                }
            }
        }

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'pendapatan'       => $dataPendapatan,
                'beban'            => $dataBeban,
                'total_pendapatan' => $totalPendapatan,
                'total_beban'      => $totalBeban,
                'laba_bersih'      => $totalPendapatan - $totalBeban
            ]
        ]);
    }

    public function laporanNeraca()
    {
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');

        // Neraca: Aset, Kewajiban, Ekuitas
        $akuns = $this->akunModel->whereIn('kategori', ['Aset', 'Kewajiban', 'Ekuitas'])->orderBy('kode_akun', 'ASC')->findAll();

        $dataAset      = [];
        $dataKewajiban = [];
        $dataEkuitas   = [];

        $totalAset      = 0;
        $totalKewajiban = 0;
        $totalEkuitas   = 0;

        foreach ($akuns as $a) {
            $sum = $this->jurnalDetailModel->selectSum('debit')->selectSum('kredit')
                ->join('keu_jurnal', 'keu_jurnal.id = keu_jurnal_detail.jurnal_id')
                ->where('akun_id', $a['id'])
                ->where('keu_jurnal.tanggal <=', $tanggal)
                ->first();

            $debit  = (float) ($sum['debit'] ?? 0);
            $kredit = (float) ($sum['kredit'] ?? 0);
            $saldo  = 0;

            if ($a['saldo_normal'] === 'Debit') {
                $saldo = $debit - $kredit;
            } else {
                $saldo = $kredit - $debit;
            }

            if ($saldo != 0) {
                $item = [
                    'kode_akun' => $a['kode_akun'],
                    'nama_akun' => $a['nama_akun'],
                    'saldo'     => $saldo
                ];
                if ($a['kategori'] === 'Aset') {
                    $dataAset[] = $item;
                    $totalAset  += $saldo;
                } elseif ($a['kategori'] === 'Kewajiban') {
                    $dataKewajiban[] = $item;
                    $totalKewajiban  += $saldo;
                } else {
                    $dataEkuitas[] = $item;
                    $totalEkuitas  += $saldo;
                }
            }
        }

        // Laba tahun berjalan (dari pendapatan - beban s/d tanggal tsb) untuk dimasukkan ke ekuitas
        $sumLaba = $this->akunModel->whereIn('kategori', ['Pendapatan', 'Beban'])->findAll();
        $pTotal = 0;
        $bTotal = 0;
        foreach ($sumLaba as $sl) {
            $sum = $this->jurnalDetailModel->selectSum('debit')->selectSum('kredit')
                ->join('keu_jurnal', 'keu_jurnal.id = keu_jurnal_detail.jurnal_id')
                ->where('akun_id', $sl['id'])
                ->where('keu_jurnal.tanggal <=', $tanggal)
                ->first();
            $debit  = (float) ($sum['debit'] ?? 0);
            $kredit = (float) ($sum['kredit'] ?? 0);
            
            if ($sl['kategori'] === 'Pendapatan') {
                $pTotal += ($kredit - $debit);
            } else {
                $bTotal += ($debit - $kredit);
            }
        }
        $labaBerjalan = $pTotal - $bTotal;
        if ($labaBerjalan != 0) {
            $dataEkuitas[] = [
                'kode_akun' => '39999',
                'nama_akun' => 'Laba Tahun Berjalan',
                'saldo'     => $labaBerjalan
            ];
            $totalEkuitas += $labaBerjalan;
        }

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'aset'            => $dataAset,
                'kewajiban'       => $dataKewajiban,
                'ekuitas'         => $dataEkuitas,
                'total_aset'      => $totalAset,
                'total_kewajiban' => $totalKewajiban,
                'total_ekuitas'   => $totalEkuitas,
                'total_pasiva'    => $totalKewajiban + $totalEkuitas
            ]
        ]);
    }
}
