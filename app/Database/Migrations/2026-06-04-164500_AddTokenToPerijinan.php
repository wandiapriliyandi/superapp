<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTokenToPerijinan extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('perijinan') && !$this->db->fieldExists('token', 'perijinan')) {
            $this->forge->addColumn('perijinan', [
                'token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => '9',
                    'null'       => true,
                    'after'      => 'nisn',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('perijinan') && $this->db->fieldExists('token', 'perijinan')) {
            $this->forge->dropColumn('perijinan', 'token');
        }
    }
}
