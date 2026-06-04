<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosStokMutasiTable extends Migration
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
            'obat_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['masuk', 'keluar'],
            ],
            'jenis' => [
                'type'       => 'ENUM',
                'constraint' => ['pengadaan', 'konsumsi', 'musnah'],
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
            'referensi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'referensi_tipe' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'petugas_id' => [
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
        $this->forge->addKey(['obat_id', 'created_at']);
        $this->forge->createTable('pos_stok_mutasi');
    }

    public function down()
    {
        $this->forge->dropTable('pos_stok_mutasi');
    }
}
