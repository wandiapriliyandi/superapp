<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePpdbTables extends Migration
{
    public function up()
    {
        // Tabel Pengaturan PPDB (Gelombang/Kuota)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'gelombang' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kuota' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'tgl_buka' => [
                'type' => 'DATE',
            ],
            'tgl_tutup' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Buka', 'Tutup'],
                'default'    => 'Tutup',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppdb_pengaturan');

        // Tabel Pendaftar PPDB
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_pendaftaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'tempat_lahir' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'no_hp_ortu' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'status_seleksi' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Lulus', 'Tidak Lulus'],
                'default'    => 'Pending',
            ],
            'id_tahun_ajaran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppdb_pendaftar');
    }

    public function down()
    {
        $this->forge->dropTable('ppdb_pendaftar');
        $this->forge->dropTable('ppdb_pengaturan');
    }
}
