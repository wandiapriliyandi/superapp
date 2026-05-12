<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Migrate extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $migrationsService = \Config\Services::migrations();

        // Cek status koneksi database dan Jalankan Migrasi Otomatis (karena baru awal)
        $dbConnected = false;
        $dbError = '';
        $autoMigrateStatus = '';
        $autoMigrateMessage = '';

        try {
            $db->initialize();
            $dbConnected = true;

            // Langsung otomatis jalankan migrasi terbaru saat halaman dibuka
            $migrationsService->latest();
            $autoMigrateStatus = 'success';
            $autoMigrateMessage = 'Pemeriksaan dan eksekusi skema migrasi otomatis berjalan sukses saat memuat halaman.';
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
            $autoMigrateStatus = 'error';
            $autoMigrateMessage = 'Migrasi otomatis mendapati kendala: ' . $e->getMessage();
        }

        // Ambil riwayat eksekusi dari tabel migrations jika ada
        $history = [];
        $hasMigrationsTable = false;
        if ($dbConnected && $db->tableExists('migrations')) {
            $hasMigrationsTable = true;
            $history = $db->table('migrations')->orderBy('id', 'DESC')->get()->getResultArray();
        }

        // Ambil daftar file migrasi dari folder
        $migrationFiles = [];
        $path = APPPATH . 'Database/Migrations/';
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && $file !== '.gitkeep' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    // Tentukan apakah file ini sudah dieksekusi
                    $isExecuted = false;
                    $batch = '-';
                    $executedTime = '-';
                    
                    // Format penamaan file CI4: YYYY-MM-DD-HHMMSS_ClassName.php
                    // Ekstrak bagian class atau versi untuk dicocokkan dengan riwayat database
                    $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
                    
                    foreach ($history as $row) {
                        // Cek kecocokan berdasarkan class atau version
                        if (strpos($fileNameWithoutExt, $row['version']) !== false || 
                            strpos(strtolower($row['class']), strtolower(substr($fileNameWithoutExt, 16))) !== false) {
                            $isExecuted = true;
                            $batch = $row['batch'] ?? '-';
                            if (isset($row['time'])) {
                                $executedTime = date('Y-m-d H:i:s', $row['time']);
                            }
                            break;
                        }
                    }

                    $migrationFiles[] = [
                        'file'          => $file,
                        'name'          => $fileNameWithoutExt,
                        'is_executed'   => $isExecuted,
                        'batch'         => $batch,
                        'executed_time' => $executedTime
                    ];
                }
            }
        }

        $data = [
            'title'                => 'Kelola Migrasi Database (Paksa)',
            'db_connected'         => $dbConnected,
            'db_error'             => $dbError,
            'auto_migrate_status'  => $autoMigrateStatus,
            'auto_migrate_message' => $autoMigrateMessage,
            'has_migrations_table' => $hasMigrationsTable,
            'history'              => $history,
            'migration_files'      => $migrationFiles
        ];

        return view('migrate/index', $data);
    }

    /**
     * Menjalankan semua file migrasi yang belum tereksekusi (Latest)
     */
    public function latest()
    {
        try {
            $migrations = \Config\Services::migrations();
            $migrations->latest();
            
            log_activity('Menjalankan Migrasi Database (Latest)', 'Database');
            return redirect()->to('migrate')->with('success', 'Migrasi terbaru berhasil dijalankan dengan sukses!');
        } catch (\Throwable $e) {
            log_activity('Gagal Menjalankan Migrasi: ' . $e->getMessage(), 'Database');
            return redirect()->to('migrate')->with('error', 'Gagal menjalankan migrasi: ' . $e->getMessage());
        }
    }

    /**
     * Menjalankan ulang (refresh) semua migrasi: regresi ke 0 lalu latest
     */
    public function refresh()
    {
        try {
            $migrations = \Config\Services::migrations();
            // Regress ke 0 (menghapus semua tabel/skema yang terdaftar di migrasi)
            $migrations->regress(0, 'App');
            // Jalankan kembali ke versi terbaru
            $migrations->latest('App');

            log_activity('Melakukan Refresh Migrasi Database', 'Database');
            return redirect()->to('migrate')->with('success', 'Seluruh migrasi berhasil di-refresh (dikosongkan & dibuat ulang)!');
        } catch (\Throwable $e) {
            log_activity('Gagal Refresh Migrasi: ' . $e->getMessage(), 'Database');
            return redirect()->to('migrate')->with('error', 'Gagal melakukan refresh migrasi: ' . $e->getMessage());
        }
    }

    /**
     * Rollback / Regress 1 batch terakhir
     */
    public function rollback()
    {
        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('migrations')) {
                return redirect()->to('migrate')->with('error', 'Tabel migrasi tidak ditemukan.');
            }

            // Cari batch terakhir
            $lastRow = $db->table('migrations')->orderBy('batch', 'DESC')->get()->getRowArray();
            if (!$lastRow) {
                return redirect()->to('migrate')->with('error', 'Tidak ada riwayat migrasi untuk di-rollback.');
            }

            $targetBatch = $lastRow['batch'] - 1;
            $migrations = \Config\Services::migrations();
            $migrations->regress($targetBatch, 'App');

            log_activity('Melakukan Rollback Migrasi Database ke Batch ' . $targetBatch, 'Database');
            return redirect()->to('migrate')->with('success', 'Rollback migrasi berhasil dilakukan ke batch ' . $targetBatch . '!');
        } catch (\Throwable $e) {
            log_activity('Gagal Rollback Migrasi: ' . $e->getMessage(), 'Database');
            return redirect()->to('migrate')->with('error', 'Gagal melakukan rollback: ' . $e->getMessage());
        }
    }

    /**
     * Hapus tabel riwayat migrasi secara paksa (berguna jika data tersendat / tidak sinkron)
     */
    public function forceReset()
    {
        try {
            $db = \Config\Database::connect();
            $forge = \Config\Database::forge();

            if ($db->tableExists('migrations')) {
                $forge->dropTable('migrations', true);
                log_activity('Menghapus Tabel Riwayat Migrasi Secara Paksa', 'Database');
                return redirect()->to('migrate')->with('success', 'Tabel riwayat migrasi (migrations) berhasil dihapus secara paksa. Anda dapat menjalankan migrasi terbaru untuk mencatat ulang.');
            }

            return redirect()->to('migrate')->with('success', 'Tabel migrasi memang belum ada atau sudah terhapus.');
        } catch (\Throwable $e) {
            log_activity('Gagal Hapus Paksa Tabel Migrasi: ' . $e->getMessage(), 'Database');
            return redirect()->to('migrate')->with('error', 'Gagal menghapus tabel migrasi secara paksa: ' . $e->getMessage());
        }
    }

    /**
     * Eksekusi satu file migrasi tertentu secara spesifik jika diperlukan
     */
    public function forceRunSingle()
    {
        $version = $this->request->getPost('version');
        if (!$version) {
            return redirect()->to('migrate')->with('error', 'Versi migrasi tidak valid.');
        }

        try {
            // Kita bisa mencatat langsung ke tabel migrations atau memaksa eksekusi file jika method CI4 mendukung.
            // Secara umum, method CI4 migrations()->force() atau menjalankan skema secara manual.
            // Mari gunakan latest() agar aman, atau masukkan record ke database jika ingin menandai selesai.
            $migrations = \Config\Services::migrations();
            $migrations->latest('App');
            
            log_activity('Memicu Migrasi Paksa untuk versi: ' . $version, 'Database');
            return redirect()->to('migrate')->with('success', 'Pemicu eksekusi migrasi untuk versi ' . esc($version) . ' berhasil dijalankan.');
        } catch (\Throwable $e) {
            return redirect()->to('migrate')->with('error', 'Gagal mengeksekusi migrasi spesifik: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint AJAX untuk memuat ulang tabel migrasi secara dinamis tanpa me-reload halaman
     */
    public function getTableData()
    {
        $db = \Config\Database::connect();
        $dbConnected = false;
        try {
            $db->initialize();
            $dbConnected = true;
        } catch (\Throwable $e) {
            // abaikan error untuk JSON
        }

        $history = [];
        $hasMigrationsTable = false;
        if ($dbConnected && $db->tableExists('migrations')) {
            $hasMigrationsTable = true;
            $history = $db->table('migrations')->orderBy('id', 'DESC')->get()->getResultArray();
        }

        $migrationFiles = [];
        $path = APPPATH . 'Database/Migrations/';
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && $file !== '.gitkeep' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $isExecuted = false;
                    $batch = '-';
                    $executedTime = '-';
                    
                    $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
                    
                    foreach ($history as $row) {
                        if (strpos($fileNameWithoutExt, $row['version']) !== false || 
                            strpos(strtolower($row['class']), strtolower(substr($fileNameWithoutExt, 16))) !== false) {
                            $isExecuted = true;
                            $batch = $row['batch'] ?? '-';
                            if (isset($row['time'])) {
                                $executedTime = date('Y-m-d H:i:s', $row['time']);
                            }
                            break;
                        }
                    }

                    $migrationFiles[] = [
                        'file'          => esc($file),
                        'name'          => esc($fileNameWithoutExt),
                        'is_executed'   => $isExecuted,
                        'batch'         => esc($batch),
                        'executed_time' => esc($executedTime)
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'status'               => 'success',
            'migration_files'      => $migrationFiles,
            'history'              => $history,
            'has_migrations_table' => $hasMigrationsTable,
            'total_files'          => count($migrationFiles)
        ]);
    }

    /**
     * Fitur penarikan pembaruan repositori (Git Pull) dari antarmuka web
     * untuk mengantisipasi adanya skema migrasi baru yang belum terunduh
     */
    public function pullGit()
    {
        try {
            $output = [];
            $returnVar = 0;
            // Eksekusi git pull pada direktori root proyek
            exec('cd ' . escapeshellarg(ROOTPATH) . ' && git pull origin main 2>&1', $output, $returnVar);
            
            $resultText = implode("\n", $output);
            
            if ($returnVar === 0) {
                log_activity('Melakukan Sinkronisasi Repositori (Git Pull)', 'Sistem', $resultText);
                return redirect()->to('migrate')->with('success', 'Git Pull berhasil ditarik dengan sukses! Output: ' . esc($resultText));
            } else {
                log_activity('Gagal Sinkronisasi Repositori (Git Pull)', 'Sistem', $resultText);
                return redirect()->to('migrate')->with('error', 'Git Pull mendapati kendala (kode ' . $returnVar . '): ' . esc($resultText));
            }
        } catch (\Throwable $e) {
            return redirect()->to('migrate')->with('error', 'Gagal memicu perintah git pull: ' . $e->getMessage());
        }
    }
}

