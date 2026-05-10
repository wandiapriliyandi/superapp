<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunAjaranTable extends Migration
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
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['Ganjil', 'Genap'],
                'default'    => 'Ganjil',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Tidak Aktif'],
                'default'    => 'Tidak Aktif',
            ],
            'tgl_mulai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tgl_selesai' => [
                'type' => 'DATE',
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
        $this->forge->createTable('tahun_ajaran');

        // Insert Initial Data
        $data = [];
        for ($year = 2020; $year <= 2026; $year++) {
            $nextYear = $year + 1;
            $taLabel  = "$year/$nextYear";

            $data[] = [
                'tahun_ajaran' => $taLabel,
                'semester'     => 'Ganjil',
                'status'       => ($year == 2026) ? 'Aktif' : 'Tidak Aktif',
                'tgl_mulai'    => "$year-07-01",
                'tgl_selesai'  => "$year-12-31",
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];

            $data[] = [
                'tahun_ajaran' => $taLabel,
                'semester'     => 'Genap',
                'status'       => 'Tidak Aktif',
                'tgl_mulai'    => "$nextYear-01-01",
                'tgl_selesai'  => "$nextYear-06-30",
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('tahun_ajaran')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('tahun_ajaran');
    }
}
