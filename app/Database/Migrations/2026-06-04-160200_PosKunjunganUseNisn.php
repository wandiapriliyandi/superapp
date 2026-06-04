<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PosKunjunganUseNisn extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('pos_kunjungan')) {
            return;
        }

        if (!$this->db->fieldExists('nisn', 'pos_kunjungan')) {
            $this->forge->addColumn('pos_kunjungan', [
                'nisn' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '10',
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if ($this->db->fieldExists('santri_id', 'pos_kunjungan')) {
            $this->db->query("
                UPDATE pos_kunjungan
                LEFT JOIN santri ON santri.id = pos_kunjungan.santri_id
                SET pos_kunjungan.nisn = santri.nisn
                WHERE pos_kunjungan.nisn IS NULL
                    AND pos_kunjungan.santri_id IS NOT NULL
            ");

            $this->forge->dropColumn('pos_kunjungan', 'santri_id');
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('pos_kunjungan')) {
            return;
        }

        if (!$this->db->fieldExists('santri_id', 'pos_kunjungan')) {
            $this->forge->addColumn('pos_kunjungan', [
                'santri_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if ($this->db->fieldExists('nisn', 'pos_kunjungan')) {
            $this->db->query("
                UPDATE pos_kunjungan
                LEFT JOIN santri ON santri.nisn = pos_kunjungan.nisn
                SET pos_kunjungan.santri_id = santri.id
                WHERE pos_kunjungan.santri_id IS NULL
                    AND pos_kunjungan.nisn IS NOT NULL
                    AND pos_kunjungan.nisn != ''
            ");

            $this->forge->dropColumn('pos_kunjungan', 'nisn');
        }
    }
}
