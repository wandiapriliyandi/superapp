<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLinkToPerpusBuku extends Migration
{
    public function up()
    {
        $this->forge->addColumn('perpus_buku', [
            'link_eksternal' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'file_digital'
            ],
            'is_drive' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'link_eksternal'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('perpus_buku', ['link_eksternal', 'is_drive']);
    }
}
