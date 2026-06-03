<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNisnToSppSantriTarif extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('nisn', 'spp_santri_tarif')) {
            $this->forge->addColumn('spp_santri_tarif', [
                'nisn' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '10',
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('nisn', 'spp_santri_tarif')) {
            $this->forge->dropColumn('spp_santri_tarif', 'nisn');
        }
    }
}