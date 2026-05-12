<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSkillMateriTables extends Migration
{
    public function up()
    {
        // Drop existing tables if any to ensure clean install
        $this->forge->dropTable('skill_soal', true);
        $this->forge->dropTable('skill_materi', true);

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

        // --- DATA SEEDER MATERI (DETAIL & LENGKAP) ---
        $materiData = [
            [
                'slug_bab'       => 'struct-1',
                'judul'          => 'Structure 1: Subject & Verb Agreement',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-check-circle',
                'color'          => 'primary',
                'ringkasan'      => 'Prinsip dasar kesesuaian jumlah antara subjek dan kata kerja, serta jebakan preposisi.',
                'materi_lengkap' => "
<h4 class='fw-bold text-primary mb-3'>Menguasai Subject-Verb Agreement</h4>
<p>Aturan emas dalam TOEFL adalah: <strong>Subjek Tunggal + Kata Kerja Tunggal (V-s/es)</strong> dan <strong>Subjek Jamak + Kata Kerja Jamak (V-tanpa s)</strong>.</p>

<h5 class='fw-bold mt-4'>1. Jebakan Frasa Preposisi (Prepositional Phrases)</h5>
<p>Banyak soal TOEFL meletakkan kata-kata di antara subjek dan kata kerja untuk membingungkan Anda. Abaikan frasa yang diawali dengan <em>of, in, at, with, between, etc.</em></p>
<div class='p-3 bg-light rounded-3 mb-3 border-start border-primary border-4'>
    <p class='mb-1'>❌ <em>The <strong>box</strong> of chocolates <strong>are</strong> on the table.</em> (Salah, karena 'chocolates' bukan subjeknya)</p>
    <p class='mb-0 fw-bold text-success'>✔️ The <strong>box</strong> (singular) of chocolates <strong>is</strong> (singular) on the table.</p>
</div>

<h5 class='fw-bold mt-4'>2. Indefinite Pronouns (Selalu Tunggal)</h5>
<p>Kata-kata berikut dianggap tunggal meskipun maknanya terdengar jamak: <em>Each, Every, Everyone, Someone, Anyone, Nobody, Neither, Either.</em></p>
<div class='p-3 bg-light rounded-3 mb-3'>
    <p class='mb-0 italic'>Example: <strong>Each</strong> of the students <strong>has</strong> (bukan have) a textbook.</p>
</div>

<h5 class='fw-bold mt-4'>3. Ekspresi Kuantitas (Berdasarkan Kata Benda Sesudahnya)</h5>
<p>Khusus untuk <em>All of, Some of, Most of, Half of,</em> kata kerja mengikuti kata benda setelah 'of'.</p>
<ul>
    <li>Most of the <strong>money is</strong> gone. (Money = Uncountable/Singular)</li>
    <li>Most of the <strong>books are</strong> gone. (Books = Plural)</li>
</ul>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-2',
                'judul'          => 'Structure 2: Verb Tenses & Forms',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-clock',
                'color'          => 'primary',
                'ringkasan'      => 'Panduan mendalam 12 Tenses: Logika penggunaan Simple, Continuous, Perfect, dan Perfect Continuous.',
                'materi_lengkap' => "
<h4 class='fw-bold text-primary mb-4'>The Ultimate Guide to 12 English Tenses</h4>
<p>Dalam TOEFL, Anda harus menguasai dua bentuk kalimat: <strong>Verbal</strong> (menggunakan Kata Kerja) dan <strong>Nominal</strong> (menggunakan Kata Benda/Sifat/Keterangan).</p>

<h5 class='fw-bold mt-5 text-primary'><i class='bi bi-calendar-check me-2'></i>A. PRESENT TENSES (Masa Kini)</h5>
<div class='row g-3 mb-4'>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>1. Simple Present</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + V1(s/es)</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + am/is/are + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: She <strong>is</strong> a teacher.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>2. Present Continuous</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + am/is/are + V-ing</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + am/is/are + being + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: He <strong>is being</strong> helpful today.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>3. Present Perfect</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + have/has + V3</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + have/has + been + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: I <strong>have been</strong> a member since 2010.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>4. Present Perfect Continuous</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + have/has + been + V-ing</p>
            <p class='small mb-0 text-success'><em>Ex: It <strong>has been raining</strong> all day.</em></p>
        </div>
    </div>
</div>

<h5 class='fw-bold mt-5 text-danger'><i class='bi bi-clock-history me-2'></i>B. PAST TENSES (Masa Lampau)</h5>
<div class='row g-3 mb-4'>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>5. Simple Past</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + V2</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + was/were + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: They <strong>were</strong> students ten years ago.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>6. Past Continuous</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + was/were + V-ing</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + was/were + being + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: You <strong>were being</strong> very loud last night.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>7. Past Perfect</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + had + V3</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + had + been + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: She <strong>had been</strong> a manager before she quit.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>8. Past Perfect Continuous</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + had + been + V-ing</p>
            <p class='small mb-0 text-success'><em>Ex: We <strong>had been waiting</strong> for an hour.</em></p>
        </div>
    </div>
</div>

<h5 class='fw-bold mt-5 text-success'><i class='bi bi-arrow-right-circle me-2'></i>C. FUTURE TENSES (Masa Depan)</h5>
<div class='row g-3 mb-4'>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>9. Simple Future</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + will + V1</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + will be + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: He <strong>will be</strong> a doctor someday.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>10. Future Continuous</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + will be + V-ing</p>
            <p class='small mb-0 text-success'><em>Ex: I <strong>will be working</strong> at 8 PM.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>11. Future Perfect</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + will have + V3</p>
            <p class='small text-muted mb-2'><strong>Nominal:</strong> S + will have been + Noun/Adj</p>
            <p class='small mb-0 text-success'><em>Ex: By next month, she <strong>will have been</strong> here for a year.</em></p>
        </div>
    </div>
    <div class='col-md-6'>
        <div class='p-3 border rounded shadow-sm bg-white h-100'>
            <h6 class='fw-bold text-dark'>12. Future Perfect Continuous</h6>
            <p class='small text-muted mb-1'><strong>Verbal:</strong> S + will have been + V-ing</p>
            <p class='small mb-0 text-success'><em>Ex: I <strong>will have been studying</strong> for three hours.</em></p>
        </div>
    </div>
</div>

<div class='mt-5 alert alert-warning border-0 rounded-4 shadow-sm'>
    <h6 class='fw-bold text-dark'><i class='bi bi-lightbulb-fill me-2'></i>Kunci Perbedaan:</h6>
    <p class='small mb-0'>Gunakan <strong>Verbal</strong> jika ada <strong>Aksi/Tindakan</strong> (eat, run, study). <br>Gunakan <strong>Nominal</strong> jika menyatakan <strong>Status/Kondisi/Profesi</strong> (doctor, happy, here).</p>
</div>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-4',
                'judul'          => 'Structure 4: Passive Voice',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-arrow-left-right',
                'color'          => 'primary',
                'ringkasan'      => 'Cara mengenali dan menggunakan kalimat pasif dalam konteks akademis.',
                'materi_lengkap' => "
<h4 class='fw-bold text-primary mb-3'>Strategi Kalimat Pasif (Passive Voice)</h4>
<p>Dalam TOEFL, kalimat pasif sering digunakan dalam bacaan ilmiah untuk menekankan objek atau proses daripada pelaku aksi.</p>

<h5 class='fw-bold mt-4'>1. Pola Dasar</h5>
<p>Rumus mutlak kalimat pasif adalah: <code>Subject + BE + PAST PARTICIPLE (V3)</code>.</p>
<ul>
    <li><strong>Simple Present:</strong> It <em>is made</em>.</li>
    <li><strong>Simple Past:</strong> It <em>was made</em>.</li>
    <li><strong>Present Perfect:</strong> It <em>has been made</em>.</li>
    <li><strong>Future:</strong> It <em>will be made</em>.</li>
</ul>

<h5 class='fw-bold mt-4'>2. Kapan Menggunakan Pasif?</h5>
<ol>
    <li>Pelaku tidak diketahui (<em>My wallet was stolen</em>).</li>
    <li>Pelaku tidak penting (<em>The pyramid was built in 2560 BC</em>).</li>
    <li>Menekankan hasil penelitian (<em>The sample was heated to 100 degrees</em>).</li>
</ol>

<h5 class='fw-bold mt-4 text-danger'>⚠️ Jebakan TOEFL: Intransitive Verbs</h5>
<p>Kata kerja yang tidak punya objek (intransitif) <strong>TIDAK BISA</strong> dipasifkan. Contoh: <em>Happen, Occur, Arrive, Die, Exist.</em></p>
<div class='alert alert-warning'>
    ❌ <em>The accident was happened.</em> (SALAH)<br>
    ✔️ <strong>The accident happened.</strong> (BENAR)
</div>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'struct-5',
                'judul'          => 'Structure 5: Clauses & Connectors',
                'kategori'       => 'Structure & Written Expression',
                'icon'           => 'bi-link-45deg',
                'color'          => 'primary',
                'ringkasan'      => 'Membedah Noun Clause, Adjective Clause, dan Adverb Clause serta penghubungnya.',
                'materi_lengkap' => "
<h4 class='fw-bold text-primary mb-3'>Memahami Klausa & Penghubung</h4>
<p>Sebuah kalimat kompleks terdiri dari Klausa Utama dan Klausa Pendukung. Anda butuh <strong>Connector</strong> untuk menghubungkannya.</p>

<h5 class='fw-bold mt-4'>1. Noun Clause (Klausa Kata Benda)</h5>
<p>Berfungsi sebagai subjek atau objek. Connector: <em>What, When, Why, How, That, Whether.</em></p>
<p>Contoh: <strong>What he said</strong> was true. (Klausa 'What he said' adalah subjek).</p>

<h5 class='fw-bold mt-4'>2. Adjective Clause (Klausa Kata Sifat)</h5>
<p>Menerangkan kata benda sebelumnya. Connector: <em>Who, Whom, Which, That, Whose.</em></p>
<p>Contoh: The man <strong>who is standing there</strong> is my uncle.</p>

<h5 class='fw-bold mt-4'>3. Adverb Clause (Klausa Kata Keterangan)</h5>
<p>Menjelaskan waktu, sebab, atau kondisi. Connector: <em>Because, Although, If, While, Since.</em></p>
<p>Contoh: <strong>Because it was raining</strong>, we stayed home. (Gunakan koma jika connector di awal).</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'slug_bab'       => 'read-1',
                'judul'          => 'Reading: Master Strategy',
                'kategori'       => 'Reading Comprehension',
                'icon'           => 'bi-book-half',
                'color'          => 'indigo',
                'ringkasan'      => 'Strategi menjawab Main Idea, Inference, Vocabulary, dan Pronoun Referent.',
                'materi_lengkap' => "
<h4 class='fw-bold text-indigo mb-3'>Panduan Strategis Reading TOEFL</h4>
<p>Waktu Reading sangat terbatas (55 menit untuk 50 soal). Anda tidak punya waktu untuk membaca setiap kata!</p>

<h5 class='fw-bold mt-4'>1. Main Idea (Gagasan Utama)</h5>
<p>Jangan baca seluruh paragraf. Bacalah <strong>kalimat pertama</strong> dari setiap paragraf. Ide pokok biasanya terletak di sana.</p>

<h5 class='fw-bold mt-4'>2. Inference (Kesimpulan Tersirat)</h5>
<p>Cari jawaban yang <em>logis</em> berdasarkan bukti di teks, tapi tidak tertulis langsung. Hindari pilihan yang menggunakan kata-kata ekstrem seperti 'Always' atau 'Never'.</p>

<h5 class='fw-bold mt-4'>3. Vocabulary in Context</h5>
<p>Gunakan teknik substitusi. Masukkan pilihan jawaban ke dalam kalimat, mana yang paling masuk akal? Lihat juga kata-kata di sekitar (context clues) seperti antonim atau sinonim yang disediakan penulis.</p>

<h5 class='fw-bold mt-4'>4. Pronoun Referent (Rujukan Kata Ganti)</h5>
<p>Kata ganti (it, they, them) hampir selalu merujuk pada <strong>Kata Benda yang muncul tepat sebelumnya</strong>. Pastikan kesesuaian jumlah (Singular/Plural).</p>",
                'created_at'     => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('skill_materi')->insertBatch($materiData);

        // --- DATA SEEDER SOAL (DIPERBANYAK & BERKUALITAS) ---
        $soalData = [
            // Soal Struct-1 (Subject-Verb)
            [
                'slug_bab'      => 'struct-1',
                'pertanyaan'    => "The results of the study on environmental pollution __________ expected to be published next month.",
                'pilihan_a'     => "is", 'pilihan_b' => "are", 'pilihan_c' => "was", 'pilihan_d' => "has been",
                'kunci_jawaban' => 'B', 'pembahasan' => "Subjek utama adalah 'The results' (plural). Frasa 'of the study...' diabaikan."
            ],
            [
                'slug_bab'      => 'struct-1',
                'pertanyaan'    => "Each of the candidates __________ given ten minutes to present their program.",
                'pilihan_a'     => "are", 'pilihan_b' => "is", 'pilihan_c' => "were", 'pilihan_d' => "have been",
                'kunci_jawaban' => 'B', 'pembahasan' => "'Each' selalu dianggap tunggal (singular)."
            ],
            // Soal Struct-4 (Passive)
            [
                'slug_bab'      => 'struct-4',
                'pertanyaan'    => "Rare artifacts from the sunken ship __________ by archaeologists for over a year.",
                'pilihan_a'     => "have studied", 'pilihan_b' => "have been studied", 'pilihan_c' => "were studying", 'pilihan_d' => "studying",
                'kunci_jawaban' => 'B', 'pembahasan' => "Kalimat pasif present perfect: have been + V3."
            ],
            [
                'slug_bab'      => 'struct-4',
                'pertanyaan'    => "Which of the following sentences is grammatically <strong>INCORRECT</strong>?",
                'pilihan_a'     => "The news was reported by the media.", 
                'pilihan_b'     => "The sun was risen at 6 AM.", 
                'pilihan_c'     => "The letters were sent yesterday.", 
                'pilihan_d'     => "The problem has been solved.",
                'kunci_jawaban' => 'B', 'pembahasan' => "'Rise' adalah kata kerja intransitif, tidak bisa dipasifkan."
            ],
            // Soal Read-1 (Main Idea)
            [
                'slug_bab'      => 'read-1',
                'pertanyaan'    => "<strong>Passage:</strong> 'The Industrial Revolution marked a major turning point in history. Almost every aspect of daily life was influenced in some way. In particular, average income and population began to exhibit unprecedented sustained growth.' <br><br> What is the main idea of this passage?",
                'pilihan_a'     => "Daily life was very hard before the revolution.", 
                'pilihan_b'     => "The Industrial Revolution caused significant historical changes.", 
                'pilihan_c'     => "Population growth is a bad sign for history.", 
                'pilihan_d'     => "Income levels are the only way to measure success.",
                'kunci_jawaban' => 'B', 'pembahasan' => "Kalimat pertama menyatakan Revolusi Industri adalah titik balik sejarah (major turning point)."
            ],
        ];
        $this->db->table('skill_soal')->insertBatch($soalData);
    }

    public function down()
    {
        $this->forge->dropTable('skill_soal', true);
        $this->forge->dropTable('skill_materi', true);
    }
}
