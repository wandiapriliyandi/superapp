<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSppSantriTarifTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nisn' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ],
            'tarif_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tarif_id', 'spp_tarif', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('spp_santri_tarif');
    }

    public function down()
    {
        $this->forge->dropTable('spp_santri_tarif');
    }
}
