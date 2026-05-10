<?php

namespace App\Models;

use CodeIgniter\Model;

class PesertaTesModel extends Model
{
    protected $table            = 'ppdb_peserta_tes';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_pendaftar', 'id_jadwal', 'kehadiran', 'nilai'];

    public function getPesertaByJadwal($id_jadwal)
    {
        return $this->select('ppdb_peserta_tes.*, ppdb_pendaftar.nama_lengkap, ppdb_pendaftar.nomor_pendaftaran')
                    ->join('ppdb_pendaftar', 'ppdb_pendaftar.id = ppdb_peserta_tes.id_pendaftar')
                    ->where('id_jadwal', $id_jadwal)
                    ->findAll();
    }
}
