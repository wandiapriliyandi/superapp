<?php

namespace Poskestren\Controllers;

use App\Controllers\BaseController;
use Poskestren\Models\KunjunganModel;
use Poskestren\Models\ObatModel;
use Poskestren\Models\PemberianObatModel;
use Poskestren\Models\StokMutasiModel;
use App\Models\SantriModel;

class Poskestren extends BaseController
{
    protected $kunjunganModel;
    protected $obatModel;
    protected $pemberianObatModel;
    protected $stokMutasiModel;
    protected $santriModel;

    public function __construct()
    {
        $this->kunjunganModel = new KunjunganModel();
        $this->obatModel = new ObatModel();
        $this->pemberianObatModel = new PemberianObatModel();
        $this->stokMutasiModel = new StokMutasiModel();
        $this->santriModel = new SantriModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $data = [
            'title' => 'Dashboard Poskestren',
            'stats' => [
                'total_kunjungan' => $this->kunjunganModel->countAllResults(),
                'kunjungan_hari_ini' => $this->kunjunganModel->where('DATE(tgl_kunjungan)', date('Y-m-d'))->countAllResults(),
                'stok_obat_low' => $this->obatModel->where('stok <', 10)->countAllResults(),
                'santri_sakit' => $this->kunjunganModel->where('status', 'Observasi')->countAllResults(),
            ],
            'recent_kunjungan' => $this->kunjunganModel->getKunjungan()
        ];

        return view('Poskestren\Views\index', $data);
    }

    public function kunjungan()
    {
        $data = [
            'title' => 'Data Kunjungan Santri',
            'kunjungan' => $this->kunjunganModel->getKunjungan()
        ];
        return view('Poskestren\Views\kunjungan\index', $data);
    }

    public function tambah_kunjungan()
    {
        $data = [
            'title' => 'Catat Kunjungan Baru',
            'santri' => $this->santriModel->select('nisn, nama_lengkap, nis')->orderBy('nama_lengkap', 'ASC')->findAll(),
            'obat' => $this->obatModel->where('stok >', 0)->findAll(),
        ];
        return view('Poskestren\Views\kunjungan\form', $data);
    }

    public function simpan_kunjungan()
    {
        $nisn = $this->request->getPost('nisn');
        $tgl = $this->request->getPost('tgl_kunjungan');
        $keluhan = $this->request->getPost('keluhan');
        $diagnosa = $this->request->getPost('diagnosa');
        $tindakan = $this->request->getPost('tindakan');
        $status = $this->request->getPost('status');
        
        $obat_ids = $this->request->getPost('obat_id');
        $jumlahs = $this->request->getPost('jumlah');
        $dosiss = $this->request->getPost('dosis');

        $db = \Config\Database::connect();
        $db->transStart();

        $kunjungan_id = $this->kunjunganModel->insert([
            'nisn' => $nisn,
            'tgl_kunjungan' => $tgl,
            'keluhan' => $keluhan,
            'diagnosa' => $diagnosa,
            'tindakan' => $tindakan,
            'status' => $status,
            'petugas_id' => session()->get('user_id')
        ]);

        if (!empty($obat_ids)) {
            foreach ($obat_ids as $key => $obat_id) {
                if (!empty($obat_id)) {
                    $qty = (int) $jumlahs[$key];
                    if ($qty <= 0) {
                        continue;
                    }

                    $result = $this->stokMutasiModel->catat(
                        (int) $obat_id,
                        $qty,
                        'keluar',
                        'konsumsi',
                        'Pemberian ke pasien (kunjungan #' . $kunjungan_id . ')',
                        (int) $kunjungan_id,
                        'kunjungan',
                        session()->get('user_id'),
                        false
                    );

                    if (!$result['ok']) {
                        $db->transRollback();
                        return redirect()->back()->with('error', $result['message'])->withInput();
                    }

                    $this->pemberianObatModel->insert([
                        'kunjungan_id' => $kunjungan_id,
                        'obat_id'      => $obat_id,
                        'jumlah'       => $qty,
                        'dosis'        => $dosiss[$key],
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan data rekam medis.')->withInput();
        }

        return redirect()->to(base_url('poskestren/kunjungan'))->with('success', 'Data rekam medis berhasil disimpan.');
    }

    public function detail_kunjungan($id)
    {
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();

        $data = [
            'title' => 'Detail Rekam Medis',
            'setting' => $setting,
            'kunjungan' => $this->kunjunganModel->getKunjungan($id),
            'obat' => $this->pemberianObatModel->getByKunjungan($id)
        ];
        
        if (empty($data['kunjungan'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data tidak ditemukan');
        }

        return view('Poskestren\Views\kunjungan\detail', $data);
    }

    public function hapus_kunjungan($id)
    {
        $this->kunjunganModel->delete($id);
        $this->pemberianObatModel->where('kunjungan_id', $id)->delete();
        return redirect()->to(base_url('poskestren/kunjungan'))->with('success', 'Data kunjungan berhasil dihapus.');
    }

    public function get_santri()
    {
        $term = $this->request->getGet('term');
        $santri = $this->santriModel->like('nama_lengkap', $term)->orLike('nis', $term)->limit(10)->find();
        return $this->response->setJSON($santri);
    }
}
