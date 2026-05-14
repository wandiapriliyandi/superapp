<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelasIdToSantri extends Migration
{
    public function up()
    {
        $this->forge->addColumn('santri', [
            'kelas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_tahun_ajaran'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('santri', 'kelas_id');
    }
}
