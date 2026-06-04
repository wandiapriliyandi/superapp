<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PerijinanUseNisn extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('perijinan')) {
            return;
        }

        if (!$this->db->fieldExists('nisn', 'perijinan')) {
            $this->forge->addColumn('perijinan', [
                'nisn' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '10',
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if ($this->db->fieldExists('santri_id', 'perijinan')) {
            $this->db->query("
                UPDATE perijinan
                LEFT JOIN santri ON santri.id = perijinan.santri_id
                SET perijinan.nisn = santri.nisn
                WHERE perijinan.nisn IS NULL
                    AND perijinan.santri_id IS NOT NULL
            ");

            $this->forge->dropColumn('perijinan', 'santri_id');
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('perijinan')) {
            return;
        }

        if (!$this->db->fieldExists('santri_id', 'perijinan')) {
            $this->forge->addColumn('perijinan', [
                'santri_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if ($this->db->fieldExists('nisn', 'perijinan')) {
            $this->db->query("
                UPDATE perijinan
                LEFT JOIN santri ON santri.nisn = perijinan.nisn
                SET perijinan.santri_id = santri.id
                WHERE perijinan.santri_id IS NULL
                    AND perijinan.nisn IS NOT NULL
                    AND perijinan.nisn != ''
            ");

            $this->forge->dropColumn('perijinan', 'nisn');
        }
    }
}
