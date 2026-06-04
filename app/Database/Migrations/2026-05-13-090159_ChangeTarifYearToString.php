<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeTarifYearToString extends Migration
{
    public function up()
    {
        $this->db->resetDataCache();

        if ($this->db->fieldExists('id_tahun_ajaran', 'spp_tarif')) {
            $this->forge->dropColumn('spp_tarif', 'id_tahun_ajaran');
        }

        if (!$this->db->fieldExists('tahun_ajaran', 'spp_tarif')) {
            $this->forge->addColumn('spp_tarif', [
                'tahun_ajaran' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'id'
                ],
            ]);
        }
    }
 
    public function down()
    {
        $this->db->resetDataCache();

        if ($this->db->fieldExists('tahun_ajaran', 'spp_tarif')) {
            $this->forge->dropColumn('spp_tarif', 'tahun_ajaran');
        }
        $this->forge->addColumn('spp_tarif', [
            'id_tahun_ajaran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id'
            ],
        ]);
    }
}
