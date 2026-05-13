<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipeToSppTarifTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('spp_tarif', [
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['Bulanan', 'Tahunan'],
                'default'    => 'Bulanan',
                'after'      => 'nama_tarif'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('spp_tarif', 'tipe');
    }
}
