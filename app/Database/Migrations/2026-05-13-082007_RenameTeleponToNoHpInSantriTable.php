<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameTeleponToNoHpInSantriTable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('santri', [
            'telepon' => [
                'name'       => 'no_hp',
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('santri', [
            'no_hp' => [
                'name'       => 'telepon',
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
        ]);
    }
}
