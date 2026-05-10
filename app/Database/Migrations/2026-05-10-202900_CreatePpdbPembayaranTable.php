<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePpdbPembayaranTable extends Migration
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
            'id_pendaftar' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nomor_kwitansi' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'jumlah' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'tanggal_bayar' => [
                'type' => 'DATE',
            ],
            'metode_bayar' => [
                'type'       => 'ENUM',
                'constraint' => ['Cash', 'Transfer'],
                'default'    => 'Cash',
            ],
            'keterangan' => [
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
        $this->forge->addForeignKey('id_pendaftar', 'ppdb_pendaftar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ppdb_pembayaran');
    }

    public function down()
    {
        $this->forge->dropTable('ppdb_pembayaran');
    }
}
