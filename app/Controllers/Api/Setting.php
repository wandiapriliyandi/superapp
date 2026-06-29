<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;

class Setting extends Controller
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new \App\Models\UserModel();
        $this->roleModel = new \App\Models\RoleModel();
    }

    // ===================== PROFIL PESANTREN =====================

    public function getProfil()
    {
        $db      = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();
        return $this->response->setJSON([
            'status' => 200,
            'data'   => $setting
        ]);
    }

    public function saveProfil()
    {
        helper('activity');
        $db      = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data['app_name']) || empty($data['pesantren_name'])) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama aplikasi & nama pesantren wajib diisi']);
        }

        $payload = [
            'app_name'       => $data['app_name'],
            'pesantren_name' => $data['pesantren_name'],
            'alamat'         => $data['alamat'] ?? '',
            'telepon'        => $data['telepon'] ?? '',
            'email'          => $data['email'] ?? '',
            'theme_mode'     => $data['theme_mode'] ?? 'midnight',
            'theme_primary'  => $data['theme_primary'] ?? '#6366f1',
            'updated_at'     => date('Y-m-d H:i:s')
        ];

        if ($setting) {
            $db->table('app_settings')->where('id', $setting['id'])->update($payload);
        } else {
            $db->table('app_settings')->insert($payload);
        }

        log_activity('Memperbarui Pengaturan Sistem (API)', 'Sistem');
        return $this->response->setJSON(['status' => 200, 'message' => 'Profil pesantren berhasil diperbarui!']);
    }

    // ===================== USER MANAGEMENT =====================

    public function indexUsers()
    {
        $users = $this->userModel->select('users.*, roles.nama_role')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->findAll();

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $users
        ]);
    }

    public function saveUser()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $username    = $data['username'] ?? '';
        $password    = $data['password'] ?? '';
        $namaLengkap = $data['nama_lengkap'] ?? '';
        $roleId      = $data['role_id'] ?? '';
        $id          = !empty($data['id']) ? $data['id'] : null;

        if (empty($username) || empty($namaLengkap)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Username dan nama lengkap wajib diisi']);
        }

        if (!$id && empty($password)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Password wajib diisi untuk pengguna baru']);
        }

        // Cek username unik
        $existing = $this->userModel->where('username', $username)->first();
        if ($existing && (!$id || $existing['id'] != $id)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Username sudah digunakan oleh pengguna lain']);
        }

        $payload = [
            'id'           => $id,
            'username'     => $username,
            'nama_lengkap' => $namaLengkap,
            'role_id'      => !empty($roleId) ? (int) $roleId : null,
        ];

        if (!empty($password)) {
            $payload['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->save($payload);

        $aksi = $id ? 'Mengubah' : 'Menambah';
        log_activity($aksi . ' Pengguna via API', 'Sistem', 'Username: ' . $username);

        return $this->response->setJSON(['status' => 200, 'message' => 'Data pengguna berhasil disimpan']);
    }

    public function deleteUser($id)
    {
        helper('activity');
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Pengguna tidak ditemukan']);
        }

        $this->userModel->delete($id);
        log_activity('Menghapus Pengguna via API', 'Sistem', 'Username: ' . $user['username']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Pengguna berhasil dihapus']);
    }

    // ===================== ROLE MANAGEMENT =====================

    public function indexRoles()
    {
        $roles = $this->roleModel->findAll();
        foreach ($roles as &$r) {
            $r['permissions'] = json_decode($r['permissions'], true) ?: [];
        }

        return $this->response->setJSON([
            'status' => 200,
            'data'   => $roles
        ]);
    }

    public function saveRole()
    {
        helper('activity');
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $namaRole    = $data['nama_role'] ?? '';
        $permissions = $data['permissions'] ?? [];
        $id          = !empty($data['id']) ? $data['id'] : null;

        if (empty($namaRole)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama hak akses wajib diisi']);
        }

        $existing = $this->roleModel->where('nama_role', $namaRole)->first();
        if ($existing && (!$id || $existing['id'] != $id)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => 422, 'message' => 'Nama hak akses sudah digunakan']);
        }

        $payload = [
            'id'          => $id,
            'nama_role'   => $namaRole,
            'permissions' => json_encode($permissions),
        ];

        $this->roleModel->save($payload);

        $aksi = $id ? 'Mengubah' : 'Menambah';
        log_activity($aksi . ' Hak Akses via API', 'Sistem', 'Role: ' . $namaRole);

        return $this->response->setJSON(['status' => 200, 'message' => 'Hak akses berhasil disimpan']);
    }

    public function deleteRole($id)
    {
        helper('activity');
        $role = $this->roleModel->find($id);
        if (!$role) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'Hak akses tidak ditemukan']);
        }

        $this->roleModel->delete($id);
        log_activity('Menghapus Hak Akses via API', 'Sistem', 'Role: ' . $role['nama_role']);

        return $this->response->setJSON(['status' => 200, 'message' => 'Hak akses berhasil dihapus']);
    }

    // ===================== DATABASE MIGRATIONS =====================

    public function getMigrationStatus()
    {
        $db = \Config\Database::connect();
        $dbConnected = false;
        $dbError = '';
        try {
            $db->initialize();
            $dbConnected = true;
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
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
                        'file'          => $file,
                        'name'          => $fileNameWithoutExt,
                        'is_executed'   => $isExecuted,
                        'batch'         => $batch,
                        'executed_time' => $executedTime
                    ];
                }
            }
        }

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'db_connected'         => $dbConnected,
                'db_error'             => $dbError,
                'environment'          => ENVIRONMENT,
                'has_migrations_table' => $hasMigrationsTable,
                'migration_files'      => $migrationFiles,
                'history_count'        => count($history)
            ]
        ]);
    }

    public function runMigrationLatest()
    {
        helper('activity');
        try {
            $migrations = \Config\Services::migrations();
            $migrations->latest();
            log_activity('Menjalankan Migrasi Database (Latest via API)', 'Database');
            return $this->response->setJSON(['status' => 200, 'message' => 'Migrasi terbaru berhasil dijalankan dengan sukses!']);
        } catch (\Throwable $e) {
            log_activity('Gagal Menjalankan Migrasi via API: ' . $e->getMessage(), 'Database');
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal menjalankan migrasi: ' . $e->getMessage()]);
        }
    }

    public function runMigrationRollback()
    {
        helper('activity');
        try {
            $db = \Config\Database::connect();
            if (!$db->tableExists('migrations')) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Tabel migrasi tidak ditemukan.']);
            }

            $lastRow = $db->table('migrations')->orderBy('batch', 'DESC')->get()->getRowArray();
            if (!$lastRow) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Tidak ada riwayat migrasi untuk di-rollback.']);
            }

            $targetBatch = $lastRow['batch'] - 1;
            $migrations = \Config\Services::migrations();
            $migrations->regress($targetBatch, 'App');

            log_activity('Melakukan Rollback Migrasi Database ke Batch ' . $targetBatch . ' via API', 'Database');
            return $this->response->setJSON(['status' => 200, 'message' => 'Rollback migrasi berhasil dilakukan ke batch ' . $targetBatch . '!']);
        } catch (\Throwable $e) {
            log_activity('Gagal Rollback Migrasi via API: ' . $e->getMessage(), 'Database');
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal melakukan rollback: ' . $e->getMessage()]);
        }
    }

    public function runMigrationRefresh()
    {
        helper('activity');
        try {
            $migrations = \Config\Services::migrations();
            $migrations->regress(0, 'App');
            $migrations->latest('App');

            log_activity('Melakukan Refresh Migrasi Database via API', 'Database');
            return $this->response->setJSON(['status' => 200, 'message' => 'Seluruh migrasi berhasil di-refresh (dikosongkan & dibuat ulang)!']);
        } catch (\Throwable $e) {
            log_activity('Gagal Refresh Migrasi via API: ' . $e->getMessage(), 'Database');
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal melakukan refresh migrasi: ' . $e->getMessage()]);
        }
    }

    public function pullGit()
    {
        helper('activity');
        try {
            $command = 'cd ' . escapeshellarg(ROOTPATH) . ' && git pull origin main 2>&1';
            $resultText = '';
            $returnVar = 0;

            if (\function_exists('exec') && \is_callable('exec')) {
                $output = [];
                \exec($command, $output, $returnVar);
                $resultText = \implode("\n", $output);
            } elseif (\function_exists('shell_exec') && \is_callable('shell_exec')) {
                $resultText = \shell_exec($command) ?? '';
            } elseif (\function_exists('proc_open') && \is_callable('proc_open')) {
                $descriptors = [
                    0 => ["pipe", "r"],
                    1 => ["pipe", "w"],
                    2 => ["pipe", "w"]
                ];
                $process = \proc_open($command, $descriptors, $pipes);
                if (\is_resource($process)) {
                    $resultText = \stream_get_contents($pipes[1]) . \stream_get_contents($pipes[2]);
                    \fclose($pipes[0]);
                    \fclose($pipes[1]);
                    \fclose($pipes[2]);
                    $returnVar = \proc_close($process);
                }
            } else {
                throw new \Exception('Fungsi eksekusi sistem dinonaktifkan oleh pengaturan server.');
            }

            $isError = ($returnVar !== 0 || \strpos(\strtolower($resultText), 'fatal:') !== false || \strpos(\strtolower($resultText), 'error:') !== false);

            if (!$isError) {
                log_activity('Melakukan Sinkronisasi Repositori (Git Pull) via API', 'Sistem', $resultText);
                return $this->response->setJSON(['status' => 200, 'message' => 'Git Pull berhasil ditarik!', 'output' => $resultText]);
            } else {
                log_activity('Gagal Sinkronisasi Repositori (Git Pull) via API', 'Sistem', $resultText);
                return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Git Pull gagal: ' . $resultText, 'output' => $resultText]);
            }
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal memicu perintah git pull: ' . $e->getMessage()]);
        }
    }

    public function runSeeder()
    {
        helper('activity');
        try {
            $seeder = \Config\Database::seeder();
            $seeder->call('UserSeeder');

            log_activity('Menjalankan UserSeeder via API', 'Database');
            return $this->response->setJSON(['status' => 200, 'message' => 'UserSeeder berhasil dijalankan!']);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['status' => 500, 'message' => 'Gagal menjalankan seeder: ' . $e->getMessage()]);
        }
    }
}
