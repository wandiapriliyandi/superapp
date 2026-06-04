<?php
 
namespace App\Database\Migrations;
 
use CodeIgniter\Database\Migration;
 
class CreateUsersTable extends Migration
{
    public function up()
    {
        // Disable foreign key checks sementara untuk menghindari error
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');

        // 1. Buat Tabel Roles
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

        // 2. Buat Tabel Users
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

        // Enable kembali foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // Jalankan seeder secara otomatis untuk mengisi role dan user default
        $seeder = \Config\Database::seeder();
        $seeder->call('UserSeeder');
    }
 
    public function down()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('roles', true);
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
