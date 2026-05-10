<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePpdbJadwalTesTables extends Migration
{
    public function up()
    {
        // Master Jadwal Tes
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_tes' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jam' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'tempat' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'kuota' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppdb_jadwal_tes');

        // Peserta Tes (Mapping)
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
            'id_jadwal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kehadiran' => [
                'type'       => 'ENUM',
                'constraint' => ['Hadir', 'Tidak Hadir', 'Pending'],
                'default'    => 'Pending',
            ],
            'nilai' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppdb_peserta_tes');
    }

    public function down()
    {
        $this->forge->dropTable('ppdb_peserta_tes');
        $this->forge->dropTable('ppdb_jadwal_tes');
    }
}
