<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePoskestrenTables extends Migration
{
    public function up()
    {
        // Tabel Obat
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_obat' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'deskripsi' => [
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
        $this->forge->createTable('pos_obat');

        // Tabel Kunjungan / Rekam Medis
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
            'tgl_kunjungan' => [
                'type' => 'DATETIME',
            ],
            'keluhan' => [
                'type' => 'TEXT',
            ],
            'diagnosa' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'tindakan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'petugas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Observasi', 'Sembuh', 'Rujuk'],
                'default'    => 'Sembuh',
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
        $this->forge->createTable('pos_kunjungan');

        // Tabel Pemberian Obat
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kunjungan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'obat_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'dosis' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pos_pemberian_obat');
    }

    public function down()
    {
        $this->forge->dropTable('pos_pemberian_obat');
        $this->forge->dropTable('pos_kunjungan');
        $this->forge->dropTable('pos_obat');
    }
}
