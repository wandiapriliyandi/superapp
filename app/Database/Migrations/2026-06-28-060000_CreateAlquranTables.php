<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlquranTables extends Migration
{
    public function up()
    {
        // 1. alquran_tahsin
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'santri_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'makharijul_huruf' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'sifat_huruf' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'tajwid' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'detail_penilaian' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_penilaian' => [
                'type' => 'DATE',
            ],
            'penguji_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->createTable('alquran_tahsin');

        // 2. alquran_tahfidz
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'santri_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipe_setoran' => [
                'type'       => 'ENUM',
                'constraint' => ['Ziyadah', 'Murajaah'],
            ],
            'juz' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'surah_mulai' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'ayat_mulai' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'surah_selesai' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'ayat_selesai' => [
                'type'       => 'INT',
                'constraint' => 5,
            ],
            'predikat' => [
                'type'       => 'ENUM',
                'constraint' => ['Lancar', 'Sedang', 'Kurang'],
            ],
            'tanggal_setor' => [
                'type' => 'DATE',
            ],
            'penguji_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('alquran_tahfidz');

        // 3. alquran_master_doa
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_doa' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addUniqueKey('nama_doa');
        $this->forge->createTable('alquran_master_doa');

        // 4. alquran_doa
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'santri_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_doa' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Lancar / Hafal',
            ],
            'tanggal_setor' => [
                'type' => 'DATE',
            ],
            'penguji_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('alquran_doa');
    }

    public function down()
    {
        $this->forge->dropTable('alquran_doa', true);
        $this->forge->dropTable('alquran_master_doa', true);
        $this->forge->dropTable('alquran_tahfidz', true);
        $this->forge->dropTable('alquran_tahsin', true);
    }
}
