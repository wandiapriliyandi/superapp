<?php

namespace Keuangan\Controllers;

use App\Controllers\BaseController;
use Keuangan\Models\AkunModel;
use Keuangan\Models\JurnalModel;
use Keuangan\Models\JurnalDetailModel;

class Jurnal extends BaseController
{
    protected $akunModel;
    protected $jurnalModel;
    protected $jurnalDetailModel;

    public function __construct()
    {
        $this->akunModel = new AkunModel();
        $this->jurnalModel = new JurnalModel();
        $this->jurnalDetailModel = new JurnalDetailModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Jurnal Umum',
            'jurnal' => $this->jurnalModel->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll(),
            'akun_kas' => $this->akunModel->where('kategori', 'Aset')->where('is_aktif', 1)->findAll(),
            'akun_lawan' => $this->akunModel->where('is_aktif', 1)->orderBy('kode_akun', 'ASC')->findAll(),
        ];

        // Ambil detail untuk setiap jurnal
        foreach ($data['jurnal'] as &$j) {
            $j['details'] = $this->jurnalDetailModel->getByJurnal($j['id']);
        }

        return view('Keuangan\Views\jurnal\index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Tambah Jurnal Umum',
            'nomor_jurnal' => $this->jurnalModel->generateNumber('JV'),
            'akun' => $this->akunModel->where('is_aktif', 1)->orderBy('kode_akun', 'ASC')->findAll(),
        ];

        return view('Keuangan\Views\jurnal\add', $data);
    }

    public function pemasukan()
    {
        $data = [
            'title' => 'Catat Kas Masuk (Pemasukan)',
            'nomor_jurnal' => $this->jurnalModel->generateNumber('KM'),
            'akun_kas' => $this->akunModel->where('kategori', 'Aset')->where('is_aktif', 1)->findAll(),
            'akun_lawan' => $this->akunModel->where('is_aktif', 1)->orderBy('kode_akun', 'ASC')->findAll(),
        ];

        return view('Keuangan\Views\jurnal\pemasukan', $data);
    }

    public function pengeluaran()
    {
        $data = [
            'title' => 'Catat Kas Keluar (Pengeluaran)',
            'nomor_jurnal' => $this->jurnalModel->generateNumber('KK'),
            'akun_kas' => $this->akunModel->where('kategori', 'Aset')->where('is_aktif', 1)->findAll(),
            'akun_lawan' => $this->akunModel->where('is_aktif', 1)->orderBy('kode_akun', 'ASC')->findAll(),
        ];

        return view('Keuangan\Views\jurnal\pengeluaran', $data);
    }

    public function save_simple()
    {
        $type = $this->request->getPost('type'); // pemasukan / pengeluaran
        $nominal = $this->request->getPost('nominal');
        $akun_kas = $this->request->getPost('akun_kas_id');
        $akun_lawan = $this->request->getPost('akun_lawan_id');

        $db = \Config\Database::connect();
        $db->transStart();

        $prefix = ($type == 'pemasukan') ? 'KM' : 'KK';
        $jurnal_id = $this->jurnalModel->insert([
            'nomor_jurnal' => $this->jurnalModel->generateNumber($prefix),
            'tanggal' => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'referensi' => $this->request->getPost('referensi'),
            'jenis_jurnal' => ($type == 'pemasukan') ? 'Kas Masuk' : 'Kas Keluar',
        ]);

        if ($type == 'pemasukan') {
            // Debit Kas, Kredit Lawan
            $this->jurnalDetailModel->insert([
                'jurnal_id' => $jurnal_id,
                'akun_id' => $akun_kas,
                'debit' => $nominal,
                'kredit' => 0,
            ]);
            $this->jurnalDetailModel->insert([
                'jurnal_id' => $jurnal_id,
                'akun_id' => $akun_lawan,
                'debit' => 0,
                'kredit' => $nominal,
            ]);
        } else {
            // Debit Lawan, Kredit Kas
            $this->jurnalDetailModel->insert([
                'jurnal_id' => $jurnal_id,
                'akun_id' => $akun_lawan,
                'debit' => $nominal,
                'kredit' => 0,
            ]);
            $this->jurnalDetailModel->insert([
                'jurnal_id' => $jurnal_id,
                'akun_id' => $akun_kas,
                'debit' => 0,
                'kredit' => $nominal,
            ]);
        }

        $db->transComplete();
        return redirect()->to(base_url('keuangan/jurnal'))->with('success', 'Transaksi berhasil dicatat.');
    }

    public function save()
    {
        $rules = [
            'tanggal' => 'required|valid_date',
            'keterangan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $akun_ids = $this->request->getPost('akun_id');
        $debits = $this->request->getPost('debit');
        $kredits = $this->request->getPost('kredit');

        // Validasi Balance
        $total_debit = array_sum($debits);
        $total_kredit = array_sum($kredits);

        if ($total_debit != $total_kredit) {
            return redirect()->back()->withInput()->with('error', 'Total Debit dan Kredit tidak balance!');
        }

        if ($total_debit <= 0) {
            return redirect()->back()->withInput()->with('error', 'Nominal harus lebih dari 0!');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $jurnal_id = $this->jurnalModel->insert([
            'nomor_jurnal' => $this->jurnalModel->generateNumber(),
            'tanggal' => $this->request->getPost('tanggal'),
            'keterangan' => $this->request->getPost('keterangan'),
            'referensi' => $this->request->getPost('referensi'),
            'jenis_jurnal' => 'Umum',
        ]);

        foreach ($akun_ids as $key => $akun_id) {
            if ($debits[$key] > 0 || $kredits[$key] > 0) {
                $this->jurnalDetailModel->insert([
                    'jurnal_id' => $jurnal_id,
                    'akun_id' => $akun_id,
                    'debit' => $debits[$key],
                    'kredit' => $kredits[$key],
                    'keterangan_item' => $this->request->getPost('keterangan_item')[$key] ?? null,
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan jurnal.');
        }

        return redirect()->to(base_url('keuangan/jurnal'))->with('success', 'Jurnal berhasil disimpan.');
    }

    public function delete($id)
    {
        $this->jurnalModel->delete($id);
        return redirect()->to(base_url('keuangan/jurnal'))->with('success', 'Jurnal berhasil dihapus.');
    }
}
