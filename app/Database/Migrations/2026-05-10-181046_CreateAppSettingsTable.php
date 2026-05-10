<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppSettingsTable extends Migration
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
            'app_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => 'SuperApp Pesantren',
            ],
            'app_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            // Pengaturan Tema (CSS Variables)
            'theme_primary' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => '#1abc9c', // Hijau Toska
            ],
            'theme_secondary' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => '#16a085',
            ],
            'theme_font' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'default'    => "'Inter', sans-serif",
            ],
            'theme_radius' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => '8px',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('app_settings');

        // Insert Default Settings
        $this->db->table('app_settings')->insert([
            'app_name'      => 'SuperApp Pesantren',
            'theme_primary' => '#1abc9c',
            'theme_secondary'=> '#16a085',
            'theme_font'    => "'Segoe UI', Roboto, sans-serif",
            'theme_radius'  => '12px',
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('app_settings');
    }
}
