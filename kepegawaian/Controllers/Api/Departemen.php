<?php

namespace Kepegawaian\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Kepegawaian\Models\DepartemenModel;

class Departemen extends BaseController
{
    use ResponseTrait;

    protected $deptModel;

    public function __construct()
    {
        $this->deptModel = new DepartemenModel();
    }

    /**
     * Get all departments
     */
    public function index()
    {
        $departemen = $this->deptModel->findAll();
        return $this->respond([
            'status' => 200,
            'message' => 'Data departemen berhasil diambil',
            'data' => $departemen
        ]);
    }

    /**
     * Save/Create a department
     */
    public function save()
    {
        helper('activity');
        $json = null;
        try {
            $json = $this->request->getJSON();
        } catch (\Exception $e) {
            // Abaikan error parsing JSON
        }
        
        $namaDepartemen = null;
        $keterangan = null;

        if ($json) {
            $namaDepartemen = $json->nama_departemen ?? null;
            $keterangan = $json->keterangan ?? null;
        }

        if (empty($namaDepartemen)) {
            $namaDepartemen = $this->request->getPost('nama_departemen');
        }
        if (empty($keterangan)) {
            $keterangan = $this->request->getPost('keterangan');
        }

        if (empty($namaDepartemen)) {
            return $this->fail('Nama departemen wajib diisi.', 400);
        }

        $this->deptModel->save([
            'nama_departemen' => $namaDepartemen,
            'keterangan' => $keterangan
        ]);

        // Hubungkan data user dari token JWT ke session sementara untuk perekaman log
        $user = $this->request->user ?? null;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';
        
        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Menyimpan Data Departemen via API', 'Kepegawaian API', 'Nama Departemen: ' . $namaDepartemen);
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Departemen berhasil disimpan.',
            'data' => [
                'nama_departemen' => $namaDepartemen,
                'keterangan' => $keterangan
            ]
        ]);
    }

    /**
     * Delete a department by ID
     */
    public function delete($id)
    {
        helper('activity');
        
        $dept = $this->deptModel->find($id);
        if (!$dept) {
            return $this->fail('Data departemen tidak ditemukan.', 404);
        }

        $this->deptModel->delete($id);

        // Hubungkan data user dari token JWT ke session sementara untuk perekaman log
        $user = $this->request->user ?? null;
        $namaLengkap = $user ? ($user->nama_lengkap ?? 'API User') : 'API User';

        session()->set(['nama_lengkap' => $namaLengkap]);
        log_activity('Menghapus Data Departemen via API', 'Kepegawaian API', 'Nama Departemen: ' . ($dept['nama_departemen'] ?? ''));
        session()->remove('nama_lengkap');

        return $this->respond([
            'status' => 200,
            'message' => 'Departemen berhasil dihapus.'
        ]);
    }
}
