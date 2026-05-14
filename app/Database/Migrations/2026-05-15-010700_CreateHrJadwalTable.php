<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHrJadwalTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pegawai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'hari' => ['type' => 'ENUM', 'constraint' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']],
            'jam_mulai' => ['type' => 'TIME'],
            'jam_selesai' => ['type' => 'TIME'],
            'kegiatan' => ['type' => 'VARCHAR', 'constraint' => 255],
            'lokasi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pegawai_id', 'hr_pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('hr_jadwal');
    }

    public function down()
    {
        $this->forge->dropTable('hr_jadwal');
    }
}
