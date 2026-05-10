<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePpdbStatusEnum extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('ppdb_pendaftar', [
            'status_seleksi' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Lulus', 'Tidak Lulus', 'Santri Terdaftar'],
                'default'    => 'Pending',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('ppdb_pendaftar', [
            'status_seleksi' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Lulus', 'Tidak Lulus'],
                'default'    => 'Pending',
            ],
        ]);
    }
}
