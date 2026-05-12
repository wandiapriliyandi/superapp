<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSkillScoresTable extends Migration
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
            'nama_santri' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'kategori_skill' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'jenis_tes' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', // e.g. 'Kuis Bab 1', 'Simulasi Akhir TOEFL'
            ],
            'skor_benar' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'total_soal' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'skor_akhir' => [
                'type'       => 'INT',
                'constraint' => 11, // e.g. TOEFL Score 310-677 or Percentage 0-100
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('elearning_skill_scores', true);

        // Seeder Riwayat Tes
        $data = [
            [
                'nama_santri'    => 'Ahmad Fauzi',
                'kelas'          => 'Kelas 11',
                'kategori_skill' => 'TOEFL Preparation',
                'jenis_tes'      => 'Kuis Bab 1: Listening',
                'skor_benar'     => 4,
                'total_soal'     => 5,
                'skor_akhir'     => 80,
                'catatan'        => 'Sangat baik dalam mengidentifikasi pembicara kedua.',
                'created_at'     => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'nama_santri'    => 'Ahmad Fauzi',
                'kelas'          => 'Kelas 11',
                'kategori_skill' => 'TOEFL Preparation',
                'jenis_tes'      => 'Simulasi Akhir TOEFL',
                'skor_benar'     => 12,
                'total_soal'     => 15,
                'skor_akhir'     => 550,
                'catatan'        => 'Lulus standar minimal pesantren (Skor TOEFL > 500). Setara tingkat Advanced.',
                'created_at'     => date('Y-m-d H:i:s', strtotime('-1 day')),
            ]
        ];
        $this->db->table('elearning_skill_scores')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('elearning_skill_scores', true);
    }
}
