<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiskonToSppSantriTarif extends Migration
{
    public function up()
    {
        $fields = [
            'nominal_diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'after'      => 'tarif_id'
            ],
            'keterangan_diskon' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'nominal_diskon'
            ],
        ];
        $this->forge->addColumn('spp_santri_tarif', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('spp_santri_tarif', ['nominal_diskon', 'keterangan_diskon']);
    }
}
