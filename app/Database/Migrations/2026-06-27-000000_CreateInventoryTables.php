<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventoryTables extends Migration
{
    public function up()
    {
        // 1. Tabel Barang (inv_barang)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Pcs',
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'kondisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'Baik',
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
        $this->forge->createTable('inv_barang');

        // 2. Tabel Mutasi Stok (inv_mutasi)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'barang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'keluar', 'kondisi'],
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'stok_sebelum' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'stok_sesudah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'petugas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('barang_id', 'inv_barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inv_mutasi');

        // 3. Tabel Peminjaman Barang (inv_peminjaman)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'barang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'peminjam_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'peminjam_tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['Santri', 'Karyawan', 'Luar'],
                'default'    => 'Santri',
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'tgl_pinjam' => [
                'type' => 'DATETIME',
            ],
            'tgl_kembali_rencana' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tgl_kembali_realisasi' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Dipinjam', 'Kembali', 'Terlambat'],
                'default'    => 'Dipinjam',
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'petugas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->addForeignKey('barang_id', 'inv_barang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('inv_peminjaman');
    }

    public function down()
    {
        $this->forge->dropTable('inv_peminjaman');
        $this->forge->dropTable('inv_mutasi');
        $this->forge->dropTable('inv_barang');
    }
}
