<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateElearningTables extends Migration
{
    public function up()
    {
        // 1. Tabel Materi Pembelajaran
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'mata_pelajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'link_video' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'file_materi' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'guru_pengampu' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
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
        $this->forge->createTable('materi_pembelajaran', true);

        // 2. Tabel Ujian Online
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'mata_pelajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'durasi_menit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 60,
            ],
            'tgl_mulai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'tgl_selesai' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Draf', 'Selesai'],
                'default'    => 'Aktif',
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
        $this->forge->createTable('ujian_online', true);

        // Insert Data Awal (Seeder) Materi Pembelajaran
        $materiData = [
            [
                'judul'          => 'Pengantar Fiqih Ibadah & Thaharah',
                'deskripsi'      => 'Pembahasan mendalam mengenai rukun-rukun wudhu, tayamum, dan mandi wajib berdasarkan kitab Safinatun Najah.',
                'mata_pelajaran' => 'Fiqih',
                'kelas'          => 'Kelas 10',
                'link_video'     => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'file_materi'    => 'Materi_Fiqih_Thaharah.pdf',
                'guru_pengampu'  => 'Ust. H. Ahmad Muzakki, Lc.',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'judul'          => 'Nahwu Shorof Dasar: Pengenalan Isim, Fiil, dan Huruf',
                'deskripsi'      => 'Materi dasar tata bahasa Arab menggunakan metode ringkas Al-Ajurrumiyyah dilengkapi contoh kalimat aplikatif.',
                'mata_pelajaran' => 'Bahasa Arab',
                'kelas'          => 'Kelas 10',
                'link_video'     => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'file_materi'    => 'Modul_Nahwu_Dasar.pdf',
                'guru_pengampu'  => 'Ustazah Siti Fatimah, S.Pd.I',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'judul'          => 'Sejarah Kebudayaan Islam: Fase Mekkah',
                'deskripsi'      => 'Kondisi masyarakat Jazirah Arab sebelum kenabian dan perjuangan dakwah Rasulullah SAW pada periode awal di Mekkah.',
                'mata_pelajaran' => 'Sejarah Islam',
                'kelas'          => 'Kelas 11',
                'link_video'     => '',
                'file_materi'    => 'SKI_Fase_Mekkah.pdf',
                'guru_pengampu'  => 'Ust. Dr. Budi Santoso, M.Ag',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('materi_pembelajaran')->insertBatch($materiData);

        // Insert Data Awal (Seeder) Ujian Online
        $ujianData = [
            [
                'judul'          => 'Ujian Tengah Semester: Fiqih Ibadah',
                'deskripsi'      => 'Kerjakan dengan jujur dan teliti. Soal mencakup bab Thaharah dan Shalat berjamaah.',
                'mata_pelajaran' => 'Fiqih',
                'kelas'          => 'Kelas 10',
                'durasi_menit'   => 90,
                'tgl_mulai'      => date('Y-m-d H:i:s', strtotime('+1 day')),
                'tgl_selesai'    => date('Y-m-d H:i:s', strtotime('+3 days')),
                'status'         => 'Aktif',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'judul'          => 'Kuis Mingguan Nahwu Shorof',
                'deskripsi'      => 'Kuis singkat mengidentifikasi tanda-tanda I’rab pada petikan ayat suci Al-Qur’an.',
                'mata_pelajaran' => 'Bahasa Arab',
                'kelas'          => 'Kelas 10',
                'durasi_menit'   => 30,
                'tgl_mulai'      => date('Y-m-d H:i:s', strtotime('-1 day')),
                'tgl_selesai'    => date('Y-m-d H:i:s', strtotime('+1 day')),
                'status'         => 'Aktif',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'judul'          => 'Tugas Mandiri Akhir Semester SKI',
                'deskripsi'      => 'Analisis essay mengenai faktor-faktor keberhasilan hijrah kaum muslimin ke Habasyah.',
                'mata_pelajaran' => 'Sejarah Islam',
                'kelas'          => 'Kelas 11',
                'durasi_menit'   => 120,
                'tgl_mulai'      => date('Y-m-d H:i:s', strtotime('+5 days')),
                'tgl_selesai'    => date('Y-m-d H:i:s', strtotime('+10 days')),
                'status'         => 'Draf',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('ujian_online')->insertBatch($ujianData);
    }

    public function down()
    {
        $this->forge->dropTable('materi_pembelajaran', true);
        $this->forge->dropTable('ujian_online', true);
    }
}
