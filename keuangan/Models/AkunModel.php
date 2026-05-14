<?php

namespace Keuangan\Models;

use CodeIgniter\Model;

class AkunModel extends Model
{
    protected $table            = 'keu_akun';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_akun', 'nama_akun', 'kategori', 'parent_id', 'saldo_normal', 'is_aktif'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getHierarchy()
    {
        $accounts = $this->orderBy('kode_akun', 'ASC')->findAll();
        return $this->buildTree($accounts);
    }

    private function buildTree(array $elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
