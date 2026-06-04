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
        $permissions = session()->get('permissions') ?: [];
        if (!in_array('*', $permissions) && !in_array('poskestren', $permissions)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anda tidak memiliki hak akses.');
        }

        $this->kunjunganModel = new KunjunganModel();
        $this->obatModel = new ObatModel();
        $this->pemberianObatModel = new PemberianObatModel();
        $this->stokMutasiModel = new StokMutasiModel();
        $this->santriModel = new SantriModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        // Ambil data santri sedang sakit (status = Sakit atau Observasi atau NULL)
        $santriSakit = $this->kunjunganModel->select('pos_kunjungan.*, santri.nama_lengkap as nama_santri, santri.nis, akademik_kelas.nama_kelas')
            ->join('santri', 'santri.nisn = pos_kunjungan.nisn')
            ->join('akademik_kelas', 'akademik_kelas.id = santri.kelas_id', 'left')
            ->groupStart()
                ->whereIn('pos_kunjungan.status', ['Sakit', 'Observasi'])
                ->orWhere('pos_kunjungan.status IS NULL')
            ->groupEnd()
            ->orderBy('pos_kunjungan.tgl_kunjungan', 'ASC')
            ->get()->getResultArray();

        $data = [
            'title' => 'Dashboard Poskestren',
            'stats' => [
                'total_kunjungan' => $this->kunjunganModel->countAllResults(),
                'kunjungan_hari_ini' => $this->kunjunganModel->where('DATE(tgl_kunjungan)', date('Y-m-d'))->countAllResults(),
                'stok_obat_low' => $this->obatModel->where('stok <', 10)->countAllResults(),
                'santri_sakit' => count($santriSakit),
            ],
            'recent_kunjungan' => $this->kunjunganModel->getKunjungan(),
            'santri_sakit_list' => $santriSakit
        ];

        // Batasi recent kunjungan ke 5 terakhir
        $data['recent_kunjungan'] = array_slice($data['recent_kunjungan'], 0, 5);

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

    public function update_perkembangan($id)
    {
        $status = $this->request->getPost('status');
        $diagnosa = $this->request->getPost('diagnosa');
        $tindakan_baru = $this->request->getPost('tindakan');
        
        $kunjungan = $this->kunjunganModel->find($id);
        if (!$kunjungan) {
            return redirect()->back()->with('error', 'Data kunjungan tidak ditemukan.');
        }

        $obat_ids = $this->request->getPost('obat_id');
        $jumlahs = $this->request->getPost('jumlah');
        $dosiss = $this->request->getPost('dosis');

        $db = \Config\Database::connect();
        $db->transStart();

        // Gabungkan tindakan lama dengan tindakan baru
        $tindakan_final = $kunjungan['tindakan'];
        if (!empty($tindakan_baru)) {
            $log_tindakan = "\n[" . date('d/m/Y H:i') . "] " . $tindakan_baru;
            $tindakan_final = !empty($tindakan_final) ? $tindakan_final . $log_tindakan : $tindakan_baru;
        }

        $updateData = [
            'status' => $status,
        ];
        if (!empty($diagnosa)) {
            $updateData['diagnosa'] = $diagnosa;
        }
        if (!empty($tindakan_baru)) {
            $updateData['tindakan'] = $tindakan_final;
        }

        $this->kunjunganModel->update($id, $updateData);

        // Catat pemberian obat tambahan
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
                        'Pemberian obat tambahan (kunjungan #' . $id . ')',
                        (int) $id,
                        'kunjungan',
                        session()->get('user_id'),
                        false
                    );

                    if (!$result['ok']) {
                        $db->transRollback();
                        return redirect()->back()->with('error', $result['message']);
                    }

                    $this->pemberianObatModel->insert([
                        'kunjungan_id' => $id,
                        'obat_id'      => $obat_id,
                        'jumlah'       => $qty,
                        'dosis'        => $dosiss[$key],
                    ]);
                }
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memperbarui perkembangan rekam medis.');
        }

        return redirect()->to(base_url('poskestren/kunjungan/detail/' . $id))
            ->with('success', 'Perkembangan rekam medis berhasil diperbarui.');
    }

    public function detail_kunjungan($id)
    {
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();

        $data = [
            'title' => 'Detail Rekam Medis',
            'setting' => $setting,
            'kunjungan' => $this->kunjunganModel->getKunjungan($id),
            'obat' => $this->pemberianObatModel->getByKunjungan($id),
            'obat_list' => $this->obatModel->where('stok >', 0)->findAll()
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
