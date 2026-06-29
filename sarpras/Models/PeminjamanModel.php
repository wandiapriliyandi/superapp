<?php

namespace Sarpras\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table            = 'inv_peminjaman';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'barang_id', 'peminjam_nama', 'peminjam_tipe', 'jumlah', 'tgl_pinjam', 
        'tgl_kembali_rencana', 'tgl_kembali_realisasi', 'status', 'keterangan', 'petugas_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil data peminjaman beserta detail nama barang dan kodenya.
     */
    public function getPeminjaman($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('inv_peminjaman.*, inv_barang.nama_barang, inv_barang.kode_barang, inv_barang.satuan');
        $builder->join('inv_barang', 'inv_barang.id = inv_peminjaman.barang_id');

        if ($id) {
            return $builder->where('inv_peminjaman.id', $id)->get()->getRowArray();
        }

        return $builder->orderBy('inv_peminjaman.tgl_pinjam', 'DESC')->get()->getResultArray();
    }
}
