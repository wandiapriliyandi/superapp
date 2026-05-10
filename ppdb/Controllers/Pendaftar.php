<?php

namespace Ppdb\Controllers;

use App\Controllers\BaseController;
use Ppdb\Models\PendaftarModel;

use App\Traits\Exportable;
use App\Traits\Indexable;
use App\Traits\Selectable;

class Pendaftar extends BaseController
{
    use Exportable, Indexable, Selectable;
    protected $pendaftarModel;

    public function selection()
    {
        return $this->renderSelection(
            $this->pendaftarModel,
            'nama_lengkap',
            ['status_seleksi' => 'Pending'] // Filter HANYA yang masih Pending
        );
    }

    public function __construct()
    {
        $this->pendaftarModel = new PendaftarModel();
    }

    public function index()
    {
        log_activity('Melihat Daftar Pendaftar', 'PPDB');
        return $this->renderIndex(
            $this->pendaftarModel,
            ['status_seleksi' => 'status'], // Map 'status' from GET to 'status_seleksi' in DB
            'Ppdb\Views\pendaftar\index',
            'Daftar Pendaftar PPDB',
            'pendaftar'
        );
    }

    public function add()
    {
        $taModel = new \App\Models\TahunAjaranModel();
        $data = [
            'title' => 'Tambah Pendaftar Baru',
            'tahun_ajaran' => $taModel->findAll()
        ];
        return view('Ppdb\Views\pendaftar\form', $data);
    }

    public function save()
    {
        $data = [
            'nomor_pendaftaran' => $this->pendaftarModel->generateNomor(),
            'nama_lengkap'      => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin'     => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'      => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'     => $this->request->getPost('tanggal_lahir'),
            'alamat'            => $this->request->getPost('alamat'),
            'no_hp_ortu'        => $this->request->getPost('no_hp_ortu'),
            'status_seleksi'    => $this->request->getPost('status_seleksi'),
            'id_tahun_ajaran'   => $this->request->getPost('id_tahun_ajaran'),
        ];

        $this->pendaftarModel->insert($data);
        log_activity('Menambah Pendaftar Baru', 'PPDB', 'Nama: ' . $data['nama_lengkap']);

        return redirect()->to('ppdb/pendaftar')->with('success', 'Data pendaftar berhasil ditambahkan.');
    }

    public function set_status($id, $status)
    {
        $map = [
            'lulus'      => 'Lulus',
            'gagal'      => 'Tidak Lulus',
            'pending'    => 'Pending',
            'terdaftar'  => 'Santri Terdaftar'
        ];

        if (isset($map[$status])) {
            $newStatus = $map[$status];
            $pendaftar = $this->pendaftarModel->find($id);
            $this->pendaftarModel->update($id, ['status_seleksi' => $newStatus]);

            log_activity('Mengubah Status Seleksi', 'PPDB', 'Status: ' . $newStatus . ' (Nama: ' . $pendaftar['nama_lengkap'] . ')');

            // JIKA MENJADI SANTRI TERDAFTAR, TAMBAHKAN KE TABEL SANTRI
            if ($newStatus === 'Santri Terdaftar') {
                $santriModel = new \App\Models\SantriModel();

                // Cek apakah sudah pernah ditambahkan sebelumnya
                $existing = $santriModel->where([
                    'nama_lengkap'  => $pendaftar['nama_lengkap'],
                    'tanggal_lahir' => $pendaftar['tanggal_lahir']
                ])->first();

                if (!$existing) {
                    $santriModel->insert([
                        'nisn'            => null,
                        'nis'             => null,
                        'nik'             => null,
                        'nama_lengkap'    => $pendaftar['nama_lengkap'],
                        'jenis_kelamin'   => $pendaftar['jenis_kelamin'],
                        'tempat_lahir'    => $pendaftar['tempat_lahir'],
                        'tanggal_lahir'   => $pendaftar['tanggal_lahir'],
                        'alamat'          => $pendaftar['alamat'],
                        'no_hp'           => $pendaftar['no_hp_ortu'],
                        'status_santri'   => 'Aktif',
                        'id_tahun_ajaran' => $pendaftar['id_tahun_ajaran'],
                    ]);
                    log_activity('Migrasi Pendaftar ke Santri', 'Akademik', 'Nama: ' . $pendaftar['nama_lengkap']);
                }
                return redirect()->to(base_url('ppdb/berkas/verifikasi'))->with('success', 'Verifikasi selesai! Pendaftar kini resmi menjadi Santri Aktif.');
            }

            return redirect()->back()->with('success', 'Status pendaftar berhasil diperbarui.');
        }
        return redirect()->back()->with('error', 'Status tidak valid.');
    }

    public function show($id)
    {
        $pendaftar = $this->pendaftarModel->find($id);
        if (!$pendaftar) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $db = \Config\Database::connect();
        // Riwayat Tes
        $tes = $db->table('ppdb_peserta_tes pt')
                  ->select('pt.*, jt.nama_tes, jt.tanggal, jt.jam, jt.tempat')
                  ->join('ppdb_jadwal_tes jt', 'jt.id = pt.id_jadwal')
                  ->where('pt.id_pendaftar', $id)
                  ->get()->getResultArray();

        // Riwayat Berkas
        $berkas = $db->table('pendaftar_berkas')
                     ->where('id_pendaftar', $id)
                     ->orderBy('updated_at', 'DESC')
                     ->get()->getRowArray();

        // Riwayat Pembayaran
        $pembayaran = $db->table('ppdb_pembayaran')
                         ->where('id_pendaftar', $id)
                         ->get()->getResultArray();

        return view('Ppdb\Views\pendaftar\show', [
            'title' => 'Riwayat Perjalanan Santri',
            'p'     => $pendaftar,
            'tes'   => $tes,
            'berkas'=> $berkas,
            'pembayaran' => $pembayaran
        ]);
    }

    public function pembayaran($id)
    {
        $pendaftar = $this->pendaftarModel->find($id);
        $pembayaranModel = new \Ppdb\Models\PpdbPembayaranModel();
        
        $data = [
            'title'     => 'Input Pembayaran Pendaftaran',
            'p'         => $pendaftar,
            'kwitansi'  => $pembayaranModel->generateKwitansi(),
            'riwayat'   => $pembayaranModel->where('id_pendaftar', $id)->findAll()
        ];

        return view('Ppdb\Views\pendaftar\pembayaran', $data);
    }

    public function save_pembayaran()
    {
        $pembayaranModel = new \Ppdb\Models\PpdbPembayaranModel();
        $id_pendaftar = $this->request->getPost('id_pendaftar');
        
        $data = [
            'id_pendaftar'   => $id_pendaftar,
            'nomor_kwitansi' => $pembayaranModel->generateKwitansi(), // Generate saat simpan
            'jumlah'         => $this->request->getPost('jumlah'),
            'tanggal_bayar'  => $this->request->getPost('tanggal_bayar'),
            'metode_bayar'   => $this->request->getPost('metode_bayar'),
            'keterangan'     => $this->request->getPost('keterangan'),
        ];

        $pembayaranModel->insert($data);
        log_activity('Mencatat Pembayaran PPDB', 'PPDB', 'Kwitansi: ' . $data['nomor_kwitansi']);

        return redirect()->to(base_url('ppdb/pendaftar/pembayaran/'.$id_pendaftar))->with('success', 'Pembayaran berhasil dicatat. Nomor Kwitansi: ' . $data['nomor_kwitansi']);
    }

    public function cetak_struk($id_pembayaran)
    {
        $db = \Config\Database::connect();
        $pembayaran = $db->table('ppdb_pembayaran pt')
                         ->select('pt.*, p.nama_lengkap, p.nomor_pendaftaran')
                         ->join('ppdb_pendaftar p', 'p.id = pt.id_pendaftar')
                         ->where('pt.id', $id_pembayaran)
                         ->get()->getRowArray();

        return view('Ppdb\Views\pendaftar\struk', ['p' => $pembayaran]);
    }

    public function delete($id)
    {
        $pendaftar = $this->pendaftarModel->find($id);
        $this->pendaftarModel->delete($id);
        log_activity('Menghapus Data Pendaftar', 'PPDB', 'Nama: ' . $pendaftar['nama_lengkap']);
        
        return redirect()->back()->with('success', 'Data pendaftar berhasil dihapus.');
    }

    public function export_excel()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Excel Pendaftar', 'PPDB');
        return $this->exportToExcel($data, 'Daftar-Pendaftar-PPDB-' . date('Ymd'));
    }

    public function export_word()
    {
        $data = $this->prepare_export_data();
        log_activity('Export Word Pendaftar', 'PPDB');
        return $this->exportToWord($data, 'Data Pendaftar PPDB', 'Daftar-Pendaftar-PPDB-' . date('Ymd'));
    }

    public function export_pdf()
    {
        $data = $this->prepare_export_data();
        log_activity('Cetak PDF Pendaftar', 'PPDB');
        return $this->exportToPdf($data, 'Data Pendaftar PPDB');
    }

    private function prepare_export_data()
    {
        $query = $this->pendaftarModel;
        if ($status = $this->request->getGet('status')) {
            $query->where('status_seleksi', $status);
        }
        
        $pendaftar = $query->orderBy('id', 'DESC')->findAll();
        
        if (empty($pendaftar)) return [];

        $export = [];
        foreach ($pendaftar as $p) {
            $export[] = [
                'Nomor'     => $p['nomor_pendaftaran'],
                'Nama'      => $p['nama_lengkap'],
                'L/P'       => $p['jenis_kelamin'],
                'Tgl Lahir' => $p['tanggal_lahir'],
                'HP Ortu'   => $p['no_hp_ortu'],
                'Status'    => $p['status_seleksi']
            ];
        }
        return $export;
    }
}
