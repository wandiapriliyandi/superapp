<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiskonToSppTagihan extends Migration
{
    public function up()
    {
        $fields = [
            'diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
                'after'      => 'nominal_tagihan'
            ],
            'keterangan_diskon' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'diskon'
            ],
        ];
        $this->forge->addColumn('spp_tagihan', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('spp_tagihan', ['diskon', 'keterangan_diskon']);
    }
}
