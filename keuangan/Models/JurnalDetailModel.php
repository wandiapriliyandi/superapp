<?php

namespace Keuangan\Models;

use CodeIgniter\Model;

class JurnalDetailModel extends Model
{
    protected $table            = 'keu_jurnal_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['jurnal_id', 'akun_id', 'debit', 'kredit', 'keterangan_item'];

    public function getByJurnal($jurnal_id)
    {
        return $this->select('keu_jurnal_detail.*, keu_akun.kode_akun, keu_akun.nama_akun')
                    ->join('keu_akun', 'keu_akun.id = keu_jurnal_detail.akun_id')
                    ->where('jurnal_id', $jurnal_id)
                    ->findAll();
    }
}
