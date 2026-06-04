<?php
 
namespace App\Database\Migrations;
 
use CodeIgniter\Database\Migration;
 
class CreateRolesTable extends Migration
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
            'nama_role' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'permissions' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('roles', true);
    }
 
    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $this->forge->dropTable('roles', true);
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
