<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdTahunAjaranToSantriTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('santri', [
            'id_tahun_ajaran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status_santri'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('santri', 'id_tahun_ajaran');
    }
}
