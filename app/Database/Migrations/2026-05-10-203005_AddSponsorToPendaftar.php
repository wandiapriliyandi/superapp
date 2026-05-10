<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSponsorToPendaftar extends Migration
{
    public function up()
    {
        $this->forge->addColumn('ppdb_pendaftar', [
            'sponsor' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'alamat'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('ppdb_pendaftar', 'sponsor');
    }
}
