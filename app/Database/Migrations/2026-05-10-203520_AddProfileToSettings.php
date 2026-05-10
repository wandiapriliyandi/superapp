<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileToSettings extends Migration
{
    public function up()
    {
        $this->forge->addColumn('app_settings', [
            'pesantren_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
                'null'       => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'telepon' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('app_settings', ['pesantren_name', 'alamat', 'telepon', 'email']);
    }
}
