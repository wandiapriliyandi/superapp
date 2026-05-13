<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTahunAjaranToSppTarifTable extends Migration
{
    public function up()
    {
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

    public function down()
    {
        $this->forge->dropColumn('spp_tarif', 'id_tahun_ajaran');
    }
}
