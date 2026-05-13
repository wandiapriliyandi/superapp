<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKeuanganTables extends Migration
{
    public function up()
    {
        // 1. Table spp_tarif
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_tarif' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'keterangan' => [
                'type'       => 'TEXT',
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
        $this->forge->createTable('spp_tarif');

        // 2. Table spp_tagihan
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
            'tarif_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bulan' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'tahun' => [
                'type'       => 'INT',
                'constraint' => 4,
            ],
            'nominal_tagihan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'total_terbayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Belum Lunas', 'Cicilan', 'Lunas'],
                'default'    => 'Belum Lunas',
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
        $this->forge->addForeignKey('santri_id', 'santri', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tarif_id', 'spp_tarif', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('spp_tagihan');

        // 3. Table spp_pembayaran
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tagihan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_bayar' => [
                'type' => 'DATE',
            ],
            'nominal_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'metode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Cash',
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'recorded_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tagihan_id', 'spp_tagihan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('spp_pembayaran');
    }

    public function down()
    {
        $this->forge->dropTable('spp_pembayaran');
        $this->forge->dropTable('spp_tagihan');
        $this->forge->dropTable('spp_tarif');
    }
}
