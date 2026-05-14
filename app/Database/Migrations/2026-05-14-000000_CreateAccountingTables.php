<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountingTables extends Migration
{
    public function up()
    {
        // 1. Table akun (Chart of Accounts)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_akun' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'nama_akun' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'],
            ],
            'parent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'saldo_normal' => [
                'type'       => 'ENUM',
                'constraint' => ['Debit', 'Kredit'],
                'default'    => 'Debit',
            ],
            'is_aktif' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->createTable('keu_akun');

        // 2. Table jurnal (Header)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_jurnal' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'referensi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'jenis_jurnal' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'Umum', // Umum, Kas Masuk, Kas Keluar, dsb
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
        $this->forge->createTable('keu_jurnal');

        // 3. Table jurnal_detail (Line Items)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jurnal_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'akun_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'debit' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'kredit' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'keterangan_item' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jurnal_id', 'keu_jurnal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('akun_id', 'keu_akun', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('keu_jurnal_detail');
    }

    public function down()
    {
        $this->forge->dropTable('keu_jurnal_detail');
        $this->forge->dropTable('keu_jurnal');
        $this->forge->dropTable('keu_akun');
    }
}
