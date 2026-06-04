<?php
 
namespace App\Database\Migrations;
 
use CodeIgniter\Database\Migration;
 
class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
 
        // Buat Tabel Users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users', true);
 
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
 
        // Jalankan seeder secara otomatis untuk mengisi role dan user default
        $seeder = \Config\Database::seeder();
        $seeder->call('UserSeeder');
    }
 
    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $this->forge->dropTable('users', true);
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
