<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerijinanTable extends Migration
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
            'nisn' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ],
            'jenis_izin' => [
                'type'       => 'ENUM',
                'constraint' => ['Sakit', 'Pulang', 'Keluar Lingkungan', 'Lainnya'],
                'default'    => 'Keluar Lingkungan',
            ],
            'alasan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal_mulai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tanggal_selesai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'Disetujui', 'Ditolak', 'Aktif', 'Kembali'],
                'default'    => 'Pending',
            ],
            'disetujui_oleh' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'catatan_petugas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'waktu_kembali' => [
                'type' => 'DATETIME',
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
        $this->forge->createTable('perijinan');
    }

    public function down()
    {
        $this->forge->dropTable('perijinan');
    }
}
