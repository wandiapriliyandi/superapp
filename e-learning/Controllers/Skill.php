<?php

namespace ELearning\Controllers;

use App\Controllers\BaseController;
use ELearning\Models\SkillScoreModel;

class Skill extends BaseController
{
    protected $scoreModel;

    public function __construct()
    {
        $this->scoreModel = new SkillScoreModel();
    }

    /**
     * Halaman Utama Pelatihan Mandiri & TOEFL
     */
    public function index()
    {
        log_activity('Melihat Halaman Utama Pelatihan Skill & TOEFL', 'E-Learning');

        return view('ELearning\Views\skill\index', [
            'title' => '🌟 Pelatihan Skill Mandiri & TOEFL'
        ]);
    }

    /**
     * Mengambil struktur data lengkap silabus dan materi TOEFL
     */
    private function getToeflData()
    {
        return [
            'bab-1' => [
                'judul' => 'Bab 1: Pemahaman Mendengarkan (Listening Comprehension)',
                'icon'  => 'bi-headphones',
                'color' => 'primary',
                'ringkasan' => 'Strategi memahami dialog singkat (Part A), percakapan panjang (Part B), dan kuliah singkat (Part C).',
                'materi_lengkap' => "
<h4 class='fw-bold text-primary mb-3'>Strategi Sukses TOEFL Listening Comprehension</h4>
<p>Tes Listening TOEFL dirancang untuk menguji kemampuan Anda dalam memahami percakapan bahasa Inggris lisan di lingkungan akademis. Tes ini umumnya terbagi menjadi 3 bagian utama:</p>
<ul>
    <li><strong>Part A (Short Dialogues):</strong> Percakapan singkat antara dua orang, diikuti oleh satu pertanyaan dari narator.</li>
    <li><strong>Part B (Extended Conversations):</strong> Percakapan yang lebih panjang tentang topik santai atau kemahasiswaan.</li>
    <li><strong>Part C (Short Talks):</strong> Monolog atau kuliah singkat oleh satu pembicara.</li>
</ul>

<h5 class='fw-bold mt-4 text-secondary'>Kiat & Trik Kunci Part A:</h5>
<ol>
    <li class='mb-2'><strong>Fokus pada Pembicara Kedua:</strong> Kunci jawaban dari pertanyaan narator hampir selalu berada pada pernyataan yang diucapkan oleh pembicara kedua.</li>
    <li class='mb-2'><strong>Pilih Sinonim (Persamaan Kata):</strong> Jawaban yang benar sering kali merupakan penyataan ulang (*restatement*) menggunakan kata-kata yang berbeda (sinonim) dari ucapan pembicara kedua.</li>
    <li class='mb-2'><strong>Hindari Bunyi yang Serupa (*Similar Sounds*):</strong> Jebakan yang paling sering muncul adalah pilihan jawaban yang menggunakan kata-kata dengan bunyi mirip seperti yang Anda dengar, namun memiliki arti yang sama sekali berbeda.</li>
    <li class='mb-2'><strong>Pahami Struktur Pasif:</strong> Jika pembicara menggunakan kalimat aktif, jawaban yang benar mungkin disajikan dalam bentuk kalimat pasif, dan sebaliknya.</li>
</ol>

<div class='alert alert-info mt-4'>
    <strong>💡 Contoh Analisis Soal:</strong><br>
    <em>Man:</em> Did you see the manager about the accounting job?<br>
    <em>Woman:</em> Yes, and I was offered the position.<br>
    <em>Narrator:</em> What does the woman mean?<br>
    <br>
    <strong>Pilihan Jawaban:</strong><br>
    A. She considers the job offer.<br>
    B. <strong>She got the job.</strong> (Jawaban Benar: 'offered the position' bersinonim dengan 'got the job').<br>
    C. She offered to manage the accounting department.
</div>",
                'kuis' => [
                    [
                        'id' => 1,
                        'pertanyaan' => "<em>(Transkrip Audio Simulasi)</em><br><strong>Man:</strong> Is the final report ready to be submitted?<br><strong>Woman:</strong> It still needs a few finishing touches.<br><strong>Narrator:</strong> What does the woman imply?",
                        'pilihan' => [
                            'A' => 'Laporannya sudah selesai sepenuhnya.',
                            'B' => 'Laporannya masih memerlukan sedikit perbaikan akhir.',
                            'C' => 'Dia tidak ingin menyentuh laporan tersebut.',
                            'D' => 'Narator harus menyelesaikan laporannya.'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 2,
                        'pertanyaan' => "<em>(Transkrip Audio Simulasi)</em><br><strong>Woman:</strong> How was your history exam?<br><strong>Man:</strong> I couldn't have done better!<br><strong>Narrator:</strong> What does the man mean?",
                        'pilihan' => [
                            'A' => 'Dia mendapatkan hasil yang sangat memuaskan/sempurna.',
                            'B' => 'Dia gagal dalam ujian sejarah.',
                            'C' => 'Dia tidak bisa mengerjakan ujian tersebut.',
                            'D' => 'Ujiannya lebih baik dibatalkan.'
                        ],
                        'jawaban' => 'A'
                    ],
                    [
                        'id' => 3,
                        'pertanyaan' => "<em>(Transkrip Audio Simulasi)</em><br><strong>Man:</strong> Are you going to the concert tonight?<br><strong>Woman:</strong> I have to prepare for tomorrow's seminar.<br><strong>Narrator:</strong> What does the woman mean?",
                        'pilihan' => [
                            'A' => 'Dia akan pergi ke konser setelah seminar.',
                            'B' => 'Dia tidak bisa hadir karena harus mempersiapkan seminar.',
                            'C' => 'Dia mengundang pria tersebut ke seminar.',
                            'D' => 'Konsernya ditunda besok.'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 4,
                        'pertanyaan' => "Strategi utama saat mendengarkan percakapan pendek (Part A) pada tes TOEFL adalah...",
                        'pilihan' => [
                            'A' => 'Mencatat semua kata yang diucapkan pembicara pertama.',
                            'B' => 'Memfokuskan perhatian pada inti ucapan pembicara kedua.',
                            'C' => 'Memilih jawaban yang suaranya paling mirip dengan kaset.',
                            'D' => 'Menunggu bagian bacaan dimulai.'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 5,
                        'pertanyaan' => "Apabila pembicara kedua menggunakan idiom <em>'hit the books'</em>, maka sinonim yang tepat pada pilihan jawaban adalah...",
                        'pilihan' => [
                            'A' => 'Menyusun buku di rak.',
                            'B' => 'Memukul tumpukan kertas.',
                            'C' => 'Belajar dengan sangat giat.',
                            'D' => 'Membeli buku pelajaran baru.'
                        ],
                        'jawaban' => 'C'
                    ]
                ]
            ],
            'bab-2' => [
                'judul' => 'Bab 2: Struktur & Ekspresi Tertulis (Structure & Written Expression)',
                'icon'  => 'bi-vector-pen',
                'color' => 'success',
                'ringkasan' => 'Aturan kesesuaian Subjek dan Kata Kerja (Subject-Verb Agreement), klausa preposisi, dan susunan frasa.',
                'materi_lengkap' => "
<h4 class='fw-bold text-success mb-3'>Kaidah Emas Struktur Kalimat Bahasa Inggris</h4>
<p>Bagian kedua dari tes TOEFL menguji penguasaan Anda terhadap tata bahasa formal (*Standard Written English*). Bagian ini terdiri dari dua tipe soal: melengkapi kalimat yang rumpang (*Structure*) dan mencari bagian kata yang salah (*Written Expression*).</p>

<h5 class='fw-bold mt-4 text-secondary'>Prinsip Dasar: Pastikan Ada Satu Subjek dan Satu Kata Kerja</h5>
<p>Sebuah kalimat tunggal dalam bahasa Inggris mutlak harus memiliki minimal satu Subjek (*Subject*) dan tepat satu Kata Kerja Utama (*Verb*).</p>

<h6 class='fw-bold mt-3'>1. Jebakan Objek Preposisi (*Object of Preposition*)</h6>
<p>Objek preposisi adalah kata benda atau kata ganti yang datang setelah kata depan (*preposition*) seperti *in, at, of, to, by, behind, on*, dsb. Kata benda ini <strong>bukanlah subjek kalimat</strong>.</p>
<div class='p-3 bg-light rounded-3 mb-3'>
    ❌ <em>To Mike</em> was a big surprise. (Salah, karena 'Mike' adalah objek preposisi dari 'To', sehingga kalimat kehilangan Subjek).<br>
    ✔️ <strong>The gift</strong> <em>to Mike</em> was a big surprise. (Benar, subjeknya adalah 'The gift').
</div>

<h6 class='fw-bold mt-3'>2. Jebakan Appositive (Keterangan Tambahan)</h6>
<p>*Appositive* adalah kata benda sisipan yang menjelaskan kata benda lain, biasanya diapit oleh tanda koma. *Appositive* tidak boleh dianggap sebagai subjek utama.</p>
<div class='p-3 bg-light rounded-3 mb-3'>
    ✔️ <strong>George</strong>, <em>the best student in the class</em>, got an A on the exam.<br>
    (Subjek utamanya adalah 'George', sedangkan frasa di dalam koma hanyalah *appositive*).
</div>

<h6 class='fw-bold mt-3'>3. Present Participle (-ing) vs Past Participle (-ed)</h6>
<p>Kata kerja berakhiran *-ing* atau *-ed* dapat berfungsi ganda sebagai Kata Kerja (*Verb*) atau sekadar Kata Sifat (*Adjective*).</p>
<ul>
    <li>Berfungsi sebagai <strong>Verb</strong> jika didahului oleh *to be* (*is, am, are, was, were* atau *have/has/had*).</li>
    <li>Berfungsi sebagai <strong>Adjective</strong> jika berdiri sendiri tanpa *to be*.</li>
</ul>",
                'kuis' => [
                    [
                        'id' => 1,
                        'pertanyaan' => "The traffic on the highway __________ moving very slowly due to the heavy rain.",
                        'pilihan' => [
                            'A' => 'are',
                            'B' => 'is',
                            'C' => 'to be',
                            'D' => 'were'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 2,
                        'pertanyaan' => "__________ , the highest mountain in the world, attracts hundreds of climbers every year.",
                        'pilihan' => [
                            'A' => 'Mount Everest is',
                            'B' => 'It is Mount Everest',
                            'C' => 'Mount Everest',
                            'D' => 'That Mount Everest'
                        ],
                        'jawaban' => 'C'
                    ],
                    [
                        'id' => 3,
                        'pertanyaan' => "The child __________ playing in the yard is my nephew.",
                        'pilihan' => [
                            'A' => 'now',
                            'B' => 'is',
                            'C' => 'he is',
                            'D' => 'was'
                        ],
                        'jawaban' => 'A'
                    ],
                    [
                        'id' => 4,
                        'pertanyaan' => "Manakah petikan kalimat di bawah ini yang memiliki struktur tata bahasa yang <strong>SALAH</strong>?",
                        'pilihan' => [
                            'A' => 'The books on the top shelf belong to the professor.',
                            'B' => 'During the meeting, the manager presented the new strategy.',
                            'C' => 'Yesterday found a beautiful rare ancient coin.',
                            'D' => 'My sister, an experienced doctor, works at the central hospital.'
                        ],
                        'jawaban' => 'C'
                    ],
                    [
                        'id' => 5,
                        'pertanyaan' => "The packages __________ at the post office will be delivered tomorrow morning.",
                        'pilihan' => [
                            'A' => 'have left',
                            'B' => 'left',
                            'C' => 'were left',
                            'D' => 'them left'
                        ],
                        'jawaban' => 'B'
                    ]
                ]
            ],
            'bab-3' => [
                'judul' => 'Bab 3: Pemahaman Bacaan (Reading Comprehension)',
                'icon'  => 'bi-book',
                'color' => 'indigo',
                'ringkasan' => 'Teknik Skimming dan Scanning, menemukan gagasan utama (Main Idea), serta menebak arti kosakata dari petunjuk konteks.',
                'materi_lengkap' => "
<h4 class='fw-bold text-indigo mb-3'>Strategi Efektif Membaca Teks Panjang Akademis</h4>
<p>Bagian ketiga dari TOEFL menguji kemampuan Anda membaca dan memahami teks bernuansa ilmiah, sejarah, atau biografi. Waktu yang disediakan sangat terbatas, sehingga Anda tidak disarankan membaca kata per kata secara lambat.</p>

<h5 class='fw-bold mt-4 text-secondary'>1. Pertanyaan Gagasan Utama (*Main Idea / Topic*)</h5>
<p>Hampir setiap bacaan akan ditanyakan ide pokoknya. Pola paragraf akademis bahasa Inggris umumnya bersifat deduktif (inti berada di awal).</p>
<div class='alert alert-warning'>
    <strong>🎯 Strategi:</strong> Bacalah dengan cermat <strong>kalimat pertama dari setiap paragraf</strong>. Intisari dari kalimat-kalimat pertama tersebut biasanya merupakan jawaban yang tepat untuk pertanyaan bertipe *Main Idea, Topic*, atau *Title*.
</div>

<h5 class='fw-bold mt-4 text-secondary'>2. Pertanyaan Informasi Rinci (*Stated Detail Questions*)</h5>
<p>Pertanyaan ini menanyakan fakta spesifik yang tertulis langsung di dalam teks. Gunakan teknik <strong>Scanning</strong> (memindai kata kunci pertanyaan di dalam teks) lalu temukan kalimat yang memuat kata kunci tersebut. Jawaban yang benar adalah parafrase dari kalimat aslinya.</p>

<h5 class='fw-bold mt-4 text-secondary'>3. Menebak Kosakata (*Vocabulary in Context*)</h5>
<p>Anda tidak perlu membuka kamus untuk menjawab arti kata yang sulit. Carilah petunjuk tanda baca seperti tanda kurung, garis ganda (*dash*), atau kata penghubung seperti *or, that is, in other words* yang langsung mendefinisikan kata sulit tersebut di kalimat yang sama.</p>",
                'kuis' => [
                    [
                        'id' => 1,
                        'pertanyaan' => "<strong>Bacalah kutipan teks berikut:</strong><br><em>Photosynthesis is the process by which green plants create their own food. Using sunlight, water, and carbon dioxide, plants produce glucose and oxygen. This vital process sustains not only the plants themselves but also almost all living organisms on Earth by providing the primary base of the global food web.</em><br><br>What is the main idea of the passage?",
                        'pilihan' => [
                            'A' => 'Tanaman hijau membutuhkan air untuk bertahan hidup.',
                            'B' => 'Fotosintesis adalah proses esensial pembuatan makanan oleh tanaman yang menopang kehidupan di Bumi.',
                            'C' => 'Glukosa adalah satu-satunya hasil dari proses kimia tanaman.',
                            'D' => 'Sinar matahari sangat berbahaya bagi organisme hidup.'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 2,
                        'pertanyaan' => "Berdasarkan teks pada soal nomor 1, zat apa saja yang diserap atau dibutuhkan oleh tanaman untuk melakukan fotosintesis?",
                        'pilihan' => [
                            'A' => 'Glukosa dan oksigen.',
                            'B' => 'Sinar matahari, air, dan karbon dioksida.',
                            'C' => 'Hanya air murni.',
                            'D' => 'Oksigen dan tanah liat.'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 3,
                        'pertanyaan' => "Kata <strong>'sustains'</strong> pada kalimat terakhir teks di atas memiliki arti yang paling berdekatan dengan...",
                        'pilihan' => [
                            'A' => 'Destroys (Menghancurkan)',
                            'B' => 'Supports / Maintains (Mendukung / Menopang)',
                            'C' => 'Hides (Menyembunyikan)',
                            'D' => 'Ignores (Mengabaikan)'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 4,
                        'pertanyaan' => "Apabila dalam soal bertipe <em>Main Idea</em> Anda kekurangan waktu, langkah tercepat yang paling direkomendasikan adalah...",
                        'pilihan' => [
                            'A' => 'Membaca secara melompat dari tengah teks.',
                            'B' => 'Membaca kalimat pertama dari setiap paragraf secara saksama.',
                            'C' => 'Menghitung jumlah kata pada teks.',
                            'D' => 'Memilih opsi jawaban yang paling pendek.'
                        ],
                        'jawaban' => 'B'
                    ],
                    [
                        'id' => 5,
                        'pertanyaan' => "Teknik mencari kata kunci spesifik (misalnya angka tahun atau nama tokoh) dengan menyapu halaman secara cepat tanpa membaca keseluruhan teks disebut...",
                        'pilihan' => [
                            'A' => 'Translating',
                            'B' => 'Writing',
                            'C' => 'Scanning',
                            'D' => 'Listening'
                        ],
                        'jawaban' => 'C'
                    ]
                ]
            ]
        ];
    }

    /**
     * Halaman Rincian Materi Per Bab
     */
    public function materi($bab)
    {
        $data = $this->getToeflData();
        if (!isset($data[$bab])) {
            return redirect()->to('e-learning/skill')->with('error', 'Bab materi tidak ditemukan.');
        }

        log_activity('Membaca Materi TOEFL: ' . $data[$bab]['judul'], 'E-Learning');

        return view('ELearning\Views\skill\materi', [
            'title'  => $data[$bab]['judul'],
            'bab_id' => $bab,
            'materi' => $data[$bab]
        ]);
    }

    /**
     * Halaman Pengerjaan Kuis Per Bab
     */
    public function kuis($bab)
    {
        $data = $this->getToeflData();
        if (!isset($data[$bab])) {
            return redirect()->to('e-learning/skill')->with('error', 'Kuis bab tidak ditemukan.');
        }

        log_activity('Mulai Mengerjakan Kuis TOEFL: ' . $data[$bab]['judul'], 'E-Learning');

        return view('ELearning\Views\skill\kuis', [
            'title'  => 'Kuis Evaluasi - ' . $data[$bab]['judul'],
            'bab_id' => $bab,
            'materi' => $data[$bab]
        ]);
    }

    /**
     * Proses pengumpulan dan penilaian Kuis Per Bab
     */
    public function submitKuis()
    {
        $bab = $this->request->getPost('bab_id');
        $nama = $this->request->getPost('nama_santri');
        $kelas = $this->request->getPost('kelas');
        $jawabanPeserta = $this->request->getPost('jawaban') ?? [];

        $data = $this->getToeflData();
        if (!isset($data[$bab])) {
            return redirect()->to('e-learning/skill')->with('error', 'Data pengumpulan kuis tidak valid.');
        }

        $soalKuis = $data[$bab]['kuis'];
        $benar = 0;
        $total = count($soalKuis);

        foreach ($soalKuis as $idx => $soal) {
            $nomorId = $soal['id'];
            if (isset($jawabanPeserta[$nomorId]) && $jawabanPeserta[$nomorId] === $soal['jawaban']) {
                $benar++;
            }
        }

        // Persentase Skor 0 - 100
        $skorAkhir = ($benar / $total) * 100;

        // Catatan Otomatis
        $catatan = 'Perlu ditingkatkan lagi. Pelajari kembali ringkasan kiat & trik pada modul.';
        if ($skorAkhir >= 80) {
            $catatan = 'Luar biasa! Pemahaman Anda terhadap bab ini sudah sangat matang.';
        } elseif ($skorAkhir >= 60) {
            $catatan = 'Cukup baik. Perhatikan kembali beberapa jebakan frasa atau kosakata.';
        }

        // Simpan ke database riwayat skor
        $this->scoreModel->save([
            'nama_santri'    => esc($nama),
            'kelas'          => esc($kelas),
            'kategori_skill' => 'TOEFL Preparation',
            'jenis_tes'      => 'Kuis ' . $data[$bab]['judul'],
            'skor_benar'     => $benar,
            'total_soal'     => $total,
            'skor_akhir'     => $skorAkhir,
            'catatan'        => $catatan,
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        log_activity('Menyelesaikan Kuis TOEFL: ' . $data[$bab]['judul'], 'E-Learning', "Skor: $skorAkhir ($benar/$total)");

        return view('ELearning\Views\skill\hasil_kuis', [
            'title'      => 'Hasil Kuis Evaluasi',
            'judul_tes'  => $data[$bab]['judul'],
            'nama'       => $nama,
            'kelas'      => $kelas,
            'benar'      => $benar,
            'total'      => $total,
            'skor_akhir' => $skorAkhir,
            'catatan'    => $catatan,
            'bab_id'     => $bab
        ]);
    }

    /**
     * Halaman Simulasi Ujian Akhir TOEFL Keseluruhan (15 Soal Terpadu)
     */
    public function simulasi()
    {
        $semuaData = $this->getToeflData();
        $soalSimulasi = [];

        // Menggabungkan seluruh soal dari Bab 1, 2, dan 3 menjadi satu simulasi besar
        $nomorUrut = 1;
        foreach ($semuaData as $babKey => $babContent) {
            $kategori = explode(':', $babContent['judul'])[0];
            foreach ($babContent['kuis'] as $item) {
                $item['nomor_simulasi'] = $nomorUrut++;
                $item['kategori_bab']   = $kategori;
                $soalSimulasi[] = $item;
            }
        }

        log_activity('Memulai Simulasi Ujian Akhir Terpadu TOEFL', 'E-Learning');

        return view('ELearning\Views\skill\simulasi', [
            'title' => '🎓 Simulasi Ujian Akhir Terpadu TOEFL PBT',
            'soal'  => $soalSimulasi
        ]);
    }

    /**
     * Proses perhitungan dan konversi Skor Simulasi Akhir TOEFL
     */
    public function submitSimulasi()
    {
        $nama = $this->request->getPost('nama_santri');
        $kelas = $this->request->getPost('kelas');
        $jawabanPeserta = $this->request->getPost('jawaban') ?? [];

        $semuaData = $this->getToeflData();
        $soalSimulasi = [];
        foreach ($semuaData as $babContent) {
            foreach ($babContent['kuis'] as $item) {
                $soalSimulasi[$item['id'] . '_' . md5($item['pertanyaan'])] = $item;
            }
        }

        // Untuk kesederhanaan, mari kita cocokkan berdasarkan urutan array yang dikirim
        // Di view, kita beri input name='jawaban[1]', 'jawaban[2]' dst sesuai nomor simulasi
        // Mari kita buat ulang daftar linier soal simulasi
        $soalLinier = [];
        $idx = 1;
        foreach ($semuaData as $babContent) {
            foreach ($babContent['kuis'] as $item) {
                $soalLinier[$idx++] = $item;
            }
        }

        $benar = 0;
        $total = count($soalLinier);

        foreach ($soalLinier as $nomor => $soal) {
            if (isset($jawabanPeserta[$nomor]) && $jawabanPeserta[$nomor] === $soal['jawaban']) {
                $benar++;
            }
        }

        // Rumus Konversi Prediksi Skor TOEFL PBT (Rentang 310 - 677)
        // Jika Benar 0 -> Skor 310. Jika Benar semua (15) -> Skor 677.
        // Penambahan per jawaban benar = (677 - 310) / 15 = 367 / 15 = 24.46
        $skorToefl = 310 + round($benar * 24.46);
        if ($skorToefl > 677) $skorToefl = 677;

        $catatan = 'Tingkat Dasar (Elementary). Perbanyak perbendaharaan kosakata dan latih kepekaan telinga.';
        if ($skorToefl >= 550) {
            $catatan = 'Tingkat Mahir (Advanced). Memenuhi syarat untuk pertukaran pelajar internasional atau beasiswa luar negeri!';
        } elseif ($skorToefl >= 500) {
            $catatan = 'Tingkat Menengah Atas (Upper-Intermediate). Lulus standar kecakapan bahasa Inggris pondok pesantren.';
        } elseif ($skorToefl >= 430) {
            $catatan = 'Tingkat Menengah (Intermediate). Cukup untuk percakapan dasar, namun butuh penajaman struktur tata bahasa.';
        }

        // Simpan ke database riwayat skor
        $this->scoreModel->save([
            'nama_santri'    => esc($nama),
            'kelas'          => esc($kelas),
            'kategori_skill' => 'TOEFL Preparation',
            'jenis_tes'      => 'Simulasi Akhir Terpadu TOEFL',
            'skor_benar'     => $benar,
            'total_soal'     => $total,
            'skor_akhir'     => $skorToefl,
            'catatan'        => $catatan,
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        log_activity('Menyelesaikan Simulasi Akhir TOEFL', 'E-Learning', "Skor Prediksi: $skorToefl");

        return view('ELearning\Views\skill\hasil_simulasi', [
            'title'      => 'Sertifikat & Hasil Simulasi TOEFL',
            'nama'       => $nama,
            'kelas'      => $kelas,
            'benar'      => $benar,
            'total'      => $total,
            'skor_toefl' => $skorToefl,
            'catatan'    => $catatan
        ]);
    }

    /**
     * Halaman Riwayat & Peringkat Skor Pelatihan
     */
    public function riwayat()
    {
        $riwayat = $this->scoreModel->orderBy('id', 'DESC')->findAll();

        log_activity('Melihat Riwayat Skor Pelatihan Skill', 'E-Learning');

        return view('ELearning\Views\skill\riwayat', [
            'title'   => '🏆 Riwayat & Peringkat Hasil Ujian Pelatihan',
            'riwayat' => $riwayat
        ]);
    }
}
