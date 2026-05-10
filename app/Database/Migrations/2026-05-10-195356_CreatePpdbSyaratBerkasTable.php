<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePpdbSyaratBerkasTable extends Migration
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
            'nama_berkas' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'is_wajib' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppdb_syarat_berkas');
    }

    public function down()
    {
        $this->forge->dropTable('ppdb_syarat_berkas');
    }
}
