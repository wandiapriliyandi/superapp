<?php

namespace Keuangan\Models;

use CodeIgniter\Model;

class JurnalModel extends Model
{
    protected $table            = 'keu_jurnal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nomor_jurnal', 'tanggal', 'keterangan', 'referensi', 'jenis_jurnal'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateNumber($prefix = 'JV')
    {
        $date = date('Ymd');
        $last = $this->like('nomor_jurnal', $prefix . '-' . $date, 'after')
                     ->orderBy('nomor_jurnal', 'DESC')
                     ->first();
        
        if ($last) {
            $num = (int) substr($last['nomor_jurnal'], -3);
            $num++;
        } else {
            $num = 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }
}
