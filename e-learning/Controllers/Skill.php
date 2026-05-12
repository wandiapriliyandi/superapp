<?php

namespace ELearning\Controllers;

use App\Controllers\BaseController;
use ELearning\Models\SkillScoreModel;
use ELearning\Models\SkillMateriModel;
use ELearning\Models\SkillSoalModel;

class Skill extends BaseController
{
    protected $scoreModel;
    protected $materiModel;
    protected $soalModel;

    public function __construct()
    {
        $this->scoreModel  = new SkillScoreModel();
        $this->materiModel = new SkillMateriModel();
        $this->soalModel   = new SkillSoalModel();
    }

    /**
     * Halaman Utama Pelatihan Mandiri & TOEFL
     */
    public function index()
    {
        log_activity('Melihat Halaman Utama Pelatihan Skill & TOEFL', 'E-Learning');

        // Mengambil semua materi dari database
        $semuaMateri = $this->materiModel->findAll();

        // Mengelompokkan materi berdasarkan kategori untuk tampilan yang rapi
        $materiPerKategori = [];
        foreach ($semuaMateri as $m) {
            $materiPerKategori[$m['kategori']][] = $m;
        }

        return view('ELearning\Views\skill\index', [
            'title'             => '🌟 Pelatihan Skill Mandiri & TOEFL',
            'materiPerKategori' => $materiPerKategori
        ]);
    }

    /**
     * Halaman Rincian Materi Per Bab
     */
    public function materi($slug)
    {
        $materi = $this->materiModel->where('slug_bab', $slug)->first();
        if (!$materi) {
            return redirect()->to('e-learning/skill')->with('error', 'Bab materi tidak ditemukan.');
        }

        log_activity('Membaca Materi Pelatihan: ' . $materi['judul'], 'E-Learning');

        return view('ELearning\Views\skill\materi', [
            'title'  => $materi['judul'],
            'bab_id' => $slug,
            'materi' => $materi
        ]);
    }

    /**
     * Halaman Pengerjaan Kuis Per Bab
     */
    public function kuis($slug)
    {
        $materi = $this->materiModel->where('slug_bab', $slug)->first();
        $soal   = $this->soalModel->where('slug_bab', $slug)->findAll();

        if (!$materi) {
            return redirect()->to('e-learning/skill')->with('error', 'Kuis bab tidak ditemukan.');
        }

        log_activity('Mulai Mengerjakan Kuis Pelatihan: ' . $materi['judul'], 'E-Learning');

        // Format data kuis agar sesuai dengan view lama (jika perlu)
        $materi['kuis'] = array_map(function($s) {
            return [
                'id'         => $s['id'],
                'pertanyaan' => $s['pertanyaan'],
                'pilihan'    => [
                    'A' => $s['pilihan_a'],
                    'B' => $s['pilihan_b'],
                    'C' => $s['pilihan_c'],
                    'D' => $s['pilihan_d'],
                ],
                'jawaban'    => $s['kunci_jawaban']
            ];
        }, $soal);

        return view('ELearning\Views\skill\kuis', [
            'title'  => 'Kuis Evaluasi - ' . $materi['judul'],
            'bab_id' => $slug,
            'materi' => $materi
        ]);
    }

    /**
     * Proses pengumpulan dan penilaian Kuis Per Bab
     */
    public function submitKuis()
    {
        $slug           = $this->request->getPost('bab_id');
        $nama           = $this->request->getPost('nama_santri');
        $kelas          = $this->request->getPost('kelas');
        $jawabanPeserta = $this->request->getPost('jawaban') ?? [];

        $materi = $this->materiModel->where('slug_bab', $slug)->first();
        $soalKuis = $this->soalModel->where('slug_bab', $slug)->findAll();

        if (!$materi) {
            return redirect()->to('e-learning/skill')->with('error', 'Data pengumpulan kuis tidak valid.');
        }

        $benar = 0;
        $total = count($soalKuis);

        foreach ($soalKuis as $soal) {
            $id = $soal['id'];
            if (isset($jawabanPeserta[$id]) && $jawabanPeserta[$id] === $soal['kunci_jawaban']) {
                $benar++;
            }
        }

        // Persentase Skor 0 - 100
        $skorAkhir = ($total > 0) ? ($benar / $total) * 100 : 0;

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
            'kategori_skill' => $materi['kategori'],
            'jenis_tes'      => 'Kuis ' . $materi['judul'],
            'skor_benar'     => $benar,
            'total_soal'     => $total,
            'skor_akhir'     => $skorAkhir,
            'catatan'        => $catatan,
            'created_at'     => date('Y-m-d H:i:s')
        ]);

        log_activity('Menyelesaikan Kuis Pelatihan: ' . $materi['judul'], 'E-Learning', "Skor: $skorAkhir ($benar/$total)");

        return view('ELearning\Views\skill\hasil_kuis', [
            'title'      => 'Hasil Kuis Evaluasi',
            'judul_tes'  => $materi['judul'],
            'nama'       => $nama,
            'kelas'      => $kelas,
            'benar'      => $benar,
            'total'      => $total,
            'skor_akhir' => $skorAkhir,
            'catatan'    => $catatan,
            'bab_id'     => $slug
        ]);
    }

    /**
     * Halaman Simulasi Ujian Akhir TOEFL Keseluruhan
     */
    public function simulasi()
    {
        // Simulasi khusus kategori 'TOEFL Preparation'
        $materiToefl = $this->materiModel->where('kategori', 'TOEFL Preparation')->findAll();
        $soalSimulasi = [];
        $nomorUrut = 1;

        foreach ($materiToefl as $m) {
            $kategoriSingkat = explode(':', $m['judul'])[0];
            $daftarSoal = $this->soalModel->where('slug_bab', $m['slug_bab'])->findAll();
            
            foreach ($daftarSoal as $s) {
                $soalSimulasi[] = [
                    'nomor_simulasi' => $nomorUrut++,
                    'kategori_bab'   => $kategoriSingkat,
                    'pertanyaan'     => $s['pertanyaan'],
                    'pilihan'        => [
                        'A' => $s['pilihan_a'],
                        'B' => $s['pilihan_b'],
                        'C' => $s['pilihan_c'],
                        'D' => $s['pilihan_d'],
                    ],
                    'jawaban'        => $s['kunci_jawaban']
                ];
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

        // Ambil ulang soal secara linier untuk pencocokan kunci
        $materiToefl = $this->materiModel->where('kategori', 'TOEFL Preparation')->findAll();
        $soalLinier = [];
        $idx = 1;
        foreach ($materiToefl as $m) {
            $daftarSoal = $this->soalModel->where('slug_bab', $m['slug_bab'])->findAll();
            foreach ($daftarSoal as $s) {
                $soalLinier[$idx++] = $s['kunci_jawaban'];
            }
        }

        $benar = 0;
        $total = count($soalLinier);

        foreach ($soalLinier as $nomor => $kunci) {
            if (isset($jawabanPeserta[$nomor]) && $jawabanPeserta[$nomor] === $kunci) {
                $benar++;
            }
        }

        // Rumus Konversi Prediksi Skor TOEFL PBT (Rentang 310 - 677)
        // Disesuaikan dengan total soal yang ada di database
        $skorToefl = ($total > 0) ? 310 + round($benar * (367 / $total)) : 310;
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
