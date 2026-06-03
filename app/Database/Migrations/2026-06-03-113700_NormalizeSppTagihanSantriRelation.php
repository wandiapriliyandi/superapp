<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeSppTagihanSantriRelation extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('spp_tagihan')) {
            return;
        }

        if (!$this->db->fieldExists('santri_id', 'spp_tagihan')) {
            $this->forge->addColumn('spp_tagihan', [
                'santri_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if ($this->db->fieldExists('nisn', 'spp_tagihan')) {
            $this->db->query("
                UPDATE spp_tagihan
                LEFT JOIN santri ON santri.nisn = spp_tagihan.nisn
                SET spp_tagihan.santri_id = santri.id
                WHERE spp_tagihan.santri_id IS NULL
                    AND spp_tagihan.nisn IS NOT NULL
                    AND spp_tagihan.nisn != ''
            ");
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('spp_tagihan')) {
            return;
        }

        if (!$this->db->fieldExists('nisn', 'spp_tagihan')) {
            $this->forge->addColumn('spp_tagihan', [
                'nisn' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '10',
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if ($this->db->fieldExists('santri_id', 'spp_tagihan')) {
            $this->db->query("
                UPDATE spp_tagihan
                LEFT JOIN santri ON santri.id = spp_tagihan.santri_id
                SET spp_tagihan.nisn = santri.nisn
                WHERE spp_tagihan.nisn IS NULL
                    AND spp_tagihan.santri_id IS NOT NULL
            ");
        }
    }
}
