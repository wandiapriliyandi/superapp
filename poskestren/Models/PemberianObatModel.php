<?php

namespace Poskestren\Models;

use CodeIgniter\Model;

class PemberianObatModel extends Model
{
    protected $table            = 'pos_pemberian_obat';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['kunjungan_id', 'obat_id', 'jumlah', 'dosis'];

    public function getByKunjungan($kunjungan_id)
    {
        return $this->db->table($this->table)
            ->select('pos_pemberian_obat.*, pos_obat.nama_obat, pos_obat.satuan')
            ->join('pos_obat', 'pos_obat.id = pos_pemberian_obat.obat_id')
            ->where('kunjungan_id', $kunjungan_id)
            ->get()->getResultArray();
    }
}
