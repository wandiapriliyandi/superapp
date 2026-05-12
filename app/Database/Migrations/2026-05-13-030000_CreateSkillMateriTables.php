<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSkillMateriTables extends Migration
{
    public function up()
    {
        // 1. Tabel skill_materi
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'slug_bab' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'judul' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => '100', 
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'ringkasan' => [
                'type' => 'TEXT',
            ],
            'materi_lengkap' => [
                'type' => 'LONGTEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('skill_materi', true);

        // 2. Tabel skill_soal
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'slug_bab' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'pertanyaan' => [
                'type' => 'TEXT',
            ],
            'pilihan_a' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'pilihan_b' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'pilihan_c' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'pilihan_d' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
            ],
            'kunci_jawaban' => [
                'type'       => 'CHAR',
                'constraint' => 1, 
            ],
            'pembahasan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('skill_soal', true);

        // --- DATA SEEDER MATERI ---
        $materiData = [
            // --- SECTION: STRUCTURE ---
            [
                'slug_bab'       => 'struct-1',
                'judul'          => 'Structure 1: Subject & Verb Agreement',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-check-circle',
                'color'          => 'primary',
                'ringkasan'      => 'Kesesuaian antara subjek tunggal/jamak dengan kata kerja yang digunakan.',
                'materi_lengkap' => "<h4>Subject-Verb Agreement</h4><p>Aturan dasar: Subjek tunggal menggunakan kata kerja tunggal, subjek jamak menggunakan kata kerja jamak. Perhatikan jebakan preposisi dan ekspresi kuantitas.</p><ul><li><strong>Tunggal:</strong> The boy plays.</li><li><strong>Jamak:</strong> The boys play.</li><li><strong>Jebakan:</strong> The list of items <em>is</em> (bukan are) long.</li></ul>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-2',
                'judul'          => 'Structure 2: Verb Tenses & Forms',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-clock',
                'color'          => 'primary',
                'ringkasan'      => 'Penggunaan kata kerja berdasarkan waktu: Simple, Continuous, Perfect.',
                'materi_lengkap' => "<h4>Verb Tenses</h4><p>Memahami kapan menggunakan V1, V2, V3, atau V-ing berdasarkan penanda waktu (yesterday, now, since, for).</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-3',
                'judul'          => 'Structure 3: Parts of Speech',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-list-stars',
                'color'          => 'primary',
                'ringkasan'      => 'Fungsi dan posisi Noun, Pronoun, Verb, Adjective, dan Adverb dalam kalimat.',
                'materi_lengkap' => "<h4>Parts of Speech</h4><p>Mengenali perbedaan posisi antara kata sifat (adjective) yang menerangkan kata benda, dan kata keterangan (adverb) yang menerangkan kata kerja/sifat.</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-4',
                'judul'          => 'Structure 4: Passive Voice',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-arrow-left-right',
                'color'          => 'primary',
                'ringkasan'      => 'Transformasi kalimat aktif menjadi pasif (Be + V3).',
                'materi_lengkap' => "<h4>Passive Voice</h4><p>Pola: <code>Be + Past Participle (V3)</code>. Contoh: 'The book was written by him'.</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-5',
                'judul'          => 'Structure 5: Clauses & Connectors',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-link-45deg',
                'color'          => 'primary',
                'ringkasan'      => 'Penggunaan penghubung pada Noun Clause, Adjective Clause, dan Adverb Clause.',
                'materi_lengkap' => "<h4>Clauses & Connectors</h4><p>Cara menghubungkan dua klausa menggunakan who, which, that, because, although, dsb.</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-6',
                'judul'          => 'Structure 6: Parallel Structure',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-distribute-vertical',
                'color'          => 'primary',
                'ringkasan'      => 'Kesejajaran struktur kata dalam deretan atau perbandingan.',
                'materi_lengkap' => "<h4>Parallel Structure</h4><p>Jika menggunakan kata benda dalam deretan, maka semua harus kata benda. Contoh: 'I like swimming, hiking, and <em>running</em>' (bukan run).</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-7',
                'judul'          => 'Structure 7: Reduced Clauses',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-scissors',
                'color'          => 'primary',
                'ringkasan'      => 'Penyusutan klausa untuk membuat kalimat lebih efektif.',
                'materi_lengkap' => "<h4>Reduced Clauses</h4><p>Menghilangkan subjek dan 'be' pada klausa. Contoh: 'While <em>(he was)</em> walking, he found a coin'.</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-8',
                'judul'          => 'Structure 8: Word Order & Prepositions',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-sort-alpha-down',
                'color'          => 'primary',
                'ringkasan'      => 'Urutan kata yang benar dan penggunaan kata depan (in, at, on, of, to).',
                'materi_lengkap' => "<h4>Word Order & Prepositions</h4><p>Aturan penempatan kata keterangan dan penggunaan preposisi yang sering muncul di TOEFL.</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],

            // --- SECTION: LISTENING ---
            [
                'slug_bab'       => 'listen-1',
                'judul'          => 'Listening Section: Parts A, B, & C',
                'kategori'       => 'Listening Comprehension',
                'icon'           => 'bi-headphones',
                'color'          => 'success',
                'ringkasan'      => 'Strategi Short Conversations, Long Conversations, dan Lectures.',
                'materi_lengkap' => "<h4>Listening Strategies</h4><p>Fokus pada baris kedua di Part A. Pahami konteks, sinonim, dan ungkapan idiomatik.</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],

            // --- SECTION: READING ---
            [
                'slug_bab'       => 'read-1',
                'judul'          => 'Reading Section: Skills & Tips',
                'kategori'       => 'Reading Comprehension',
                'icon'           => 'bi-book-half',
                'color'          => 'indigo',
                'ringkasan'      => 'Menemukan Main Idea, Detail, Inference, dan Pronoun Referents.',
                'materi_lengkap' => "<h4>Reading Skills</h4><p>Strategi menjawab soal Ide Pokok, Informasi Tersurat/Tersirat, dan Referensi Kata Ganti.</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('skill_materi')->insertBatch($materiData);

        // --- DATA SEEDER SOAL (Contoh untuk masing-masing bab) ---
        $soalData = [
            // Soal Struct-1
            [
                'slug_bab'      => 'struct-1',
                'pertanyaan'    => "The keys to the drawer __________ on the table yesterday.",
                'pilihan_a'     => "was", 'pilihan_b' => "were", 'pilihan_c' => "is", 'pilihan_d' => "are",
                'kunci_jawaban' => 'B', 'pembahasan' => "Subjek jamak 'The keys'. 'to the drawer' adalah objek preposisi."
            ],
            // Soal Struct-4
            [
                'slug_bab'      => 'struct-4',
                'pertanyaan'    => "The new bridge __________ by the local government next year.",
                'pilihan_a'     => "will build", 'pilihan_b' => "is built", 'pilihan_c' => "will be built", 'pilihan_d' => "has built",
                'kunci_jawaban' => 'C', 'pembahasan' => "Kalimat pasif masa depan: will + be + V3."
            ],
            // Soal Struct-6
            [
                'slug_bab'      => 'struct-6',
                'pertanyaan'    => "She likes to sing, to dance, and __________.",
                'pilihan_a'     => "swimming", 'pilihan_b' => "to swim", 'pilihan_c' => "swims", 'pilihan_d' => "swim",
                'kunci_jawaban' => 'B', 'pembahasan' => "Parallel structure menggunakan to-infinitive."
            ],
            // ... Soal lainnya akan ditambahkan secara bertahap ...
        ];
        $this->db->table('skill_soal')->insertBatch($soalData);
    }

    public function down()
    {
        $this->forge->dropTable('skill_soal', true);
        $this->forge->dropTable('skill_materi', true);
    }
}
