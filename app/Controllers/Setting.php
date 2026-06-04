<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Setting extends Controller
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        helper('activity');
        $this->userModel = new \App\Models\UserModel();
        $this->roleModel = new \App\Models\RoleModel();
    }
    public function index()
    {
        $db = \Config\Database::connect();
        $data['setting'] = $db->table('app_settings')->get()->getRowArray();
        $data['title'] = 'Profil Pesantren';

        return view('setting/index', $data);
    }

    public function theme()
    {
        $db = \Config\Database::connect();
        $data['setting'] = $db->table('app_settings')->get()->getRowArray();
        $data['title'] = 'Pengaturan Tema Aplikasi';

        return view('setting/theme', $data);
    }

    public function update()
    {
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();
        
        $data = [];
        $fields = ['app_name', 'pesantren_name', 'alamat', 'telepon', 'email', 'theme_mode', 'theme_primary'];
        
        foreach ($fields as $field) {
            $val = $this->request->getPost($field);
            if ($val !== null) {
                $data[$field] = $val;
            }
        }
        
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Proses Upload Logo
        $file = $this->request->getFile('app_logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Buat folder jika belum ada
            if (!is_dir(FCPATH . 'uploads/img/')) {
                mkdir(FCPATH . 'uploads/img/', 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/img/', $newName);
            
            // Hapus logo lama
            if ($setting && $setting['app_logo'] && file_exists(FCPATH . 'uploads/img/' . $setting['app_logo'])) {
                unlink(FCPATH . 'uploads/img/' . $setting['app_logo']);
            }
            
            $data['app_logo'] = $newName;
        }

        if ($setting) {
            $db->table('app_settings')->where('id', $setting['id'])->update($data);
        } else {
            $db->table('app_settings')->insert($data);
        }

        log_activity('Memperbarui Pengaturan Sistem', 'Sistem');
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }

    // ==========================================
    // MANAJEMEN PENGGUNA (USERS)
    // ==========================================
    
    public function users()
    {
        $data = [
            'title' => 'Manajemen Pengguna',
            'users' => $this->userModel->select('users.*, roles.nama_role')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->findAll(),
        ];
        return view('setting/users/index', $data);
    }

    public function users_tambah()
    {
        $data = [
            'title' => 'Tambah Pengguna Baru',
            'roles' => $this->roleModel->findAll(),
        ];
        return view('setting/users/form', $data);
    }

    public function users_simpan()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $namaLengkap = $this->request->getPost('nama_lengkap');
        $roleId = $this->request->getPost('role_id');

        if (empty($username) || empty($password) || empty($namaLengkap)) {
            return redirect()->back()->with('error', 'Semua kolom wajib diisi.')->withInput();
        }

        $exists = $this->userModel->where('username', $username)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Username sudah digunakan oleh user lain.')->withInput();
        }

        $this->userModel->insert([
            'username'     => $username,
            'password'     => password_hash($password, PASSWORD_DEFAULT),
            'nama_lengkap' => $namaLengkap,
            'role_id'      => $roleId ? (int) $roleId : null,
        ]);

        log_activity('Menambah Pengguna Baru', 'Sistem', 'Username: ' . $username);
        return redirect()->to(base_url('setting/users'))->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function users_edit($id)
    {
        $data = [
            'title' => 'Edit Data Pengguna',
            'user'  => $this->userModel->find($id),
            'roles' => $this->roleModel->findAll(),
        ];
        if (empty($data['user'])) {
            return redirect()->to(base_url('setting/users'))->with('error', 'Pengguna tidak ditemukan.');
        }
        return view('setting/users/form', $data);
    }

    public function users_update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('setting/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $namaLengkap = $this->request->getPost('nama_lengkap');
        $roleId = $this->request->getPost('role_id');

        if (empty($username) || empty($namaLengkap)) {
            return redirect()->back()->with('error', 'Kolom username dan nama lengkap wajib diisi.')->withInput();
        }

        $exists = $this->userModel->where('username', $username)->where('id !=', $id)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Username sudah digunakan oleh user lain.')->withInput();
        }

        $updateData = [
            'username'     => $username,
            'nama_lengkap' => $namaLengkap,
            'role_id'      => $roleId ? (int) $roleId : null,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);

        log_activity('Memperbarui Data Pengguna', 'Sistem', 'Username: ' . $username);
        return redirect()->to(base_url('setting/users'))->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function users_hapus($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('setting/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($id == session()->get('user_id')) {
            return redirect()->to(base_url('setting/users'))->with('error', 'Anda tidak diperbolehkan menghapus akun Anda sendiri.');
        }

        $this->userModel->delete($id);

        log_activity('Menghapus Pengguna', 'Sistem', 'Username: ' . $user['username']);
        return redirect()->to(base_url('setting/users'))->with('success', 'Pengguna berhasil dihapus.');
    }

    // ==========================================
    // MANAJEMEN HAK AKSES (ROLES)
    // ==========================================

    public function roles()
    {
        $data = [
            'title' => 'Manajemen Hak Akses',
            'roles' => $this->roleModel->findAll(),
        ];
        return view('setting/roles/index', $data);
    }

    public function roles_tambah()
    {
        $data = [
            'title' => 'Tambah Hak Akses Baru',
            'modules' => [
                '*'            => 'Akses Superadmin (Semua Modul)',
                'perijinan'    => 'Modul Perizinan Santri',
                'poskestren'   => 'Modul Poskestren & Kesehatan',
                'monitoring'   => 'Modul Monitoring Pimpinan',
                'akademik'     => 'Modul Siakad & Akademik',
                'spp'          => 'Modul Manajemen SPP',
                'keuangan'     => 'Modul Keuangan Akuntansi',
                'kepegawaian'  => 'Modul HR & Kepegawaian',
                'e-learning'   => 'Modul E-Learning',
                'perpustakaan' => 'Modul Perpustakaan',
                'ppdb'         => 'Modul PPDB (Pendaftaran)',
                'setting'      => 'Modul System Settings',
                'activity'     => 'Modul Log Aktivitas Audit',
            ]
        ];
        return view('setting/roles/form', $data);
    }

    public function roles_simpan()
    {
        $namaRole = $this->request->getPost('nama_role');
        $permissions = $this->request->getPost('permissions') ?: [];

        if (empty($namaRole)) {
            return redirect()->back()->with('error', 'Nama role wajib diisi.')->withInput();
        }

        $exists = $this->roleModel->where('nama_role', $namaRole)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Nama hak akses sudah digunakan.')->withInput();
        }

        $this->roleModel->insert([
            'nama_role'   => $namaRole,
            'permissions' => json_encode($permissions),
        ]);

        log_activity('Menambah Hak Akses Baru', 'Sistem', 'Role: ' . $namaRole);
        return redirect()->to(base_url('setting/roles'))->with('success', 'Hak akses berhasil ditambahkan.');
    }

    public function roles_edit($id)
    {
        $data = [
            'title' => 'Edit Hak Akses',
            'role'  => $this->roleModel->find($id),
            'modules' => [
                '*'            => 'Akses Superadmin (Semua Modul)',
                'perijinan'    => 'Modul Perizinan Santri',
                'poskestren'   => 'Modul Poskestren & Kesehatan',
                'monitoring'   => 'Modul Monitoring Pimpinan',
                'akademik'     => 'Modul Siakad & Akademik',
                'spp'          => 'Modul Manajemen SPP',
                'keuangan'     => 'Modul Keuangan Akuntansi',
                'kepegawaian'  => 'Modul HR & Kepegawaian',
                'e-learning'   => 'Modul E-Learning',
                'perpustakaan' => 'Modul Perpustakaan',
                'ppdb'         => 'Modul PPDB (Pendaftaran)',
                'setting'      => 'Modul System Settings',
                'activity'     => 'Modul Log Aktivitas Audit',
            ]
        ];
        if (empty($data['role'])) {
            return redirect()->to(base_url('setting/roles'))->with('error', 'Hak akses tidak ditemukan.');
        }
        return view('setting/roles/form', $data);
    }

    public function roles_update($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to(base_url('setting/roles'))->with('error', 'Hak akses tidak ditemukan.');
        }

        $namaRole = $this->request->getPost('nama_role');
        $permissions = $this->request->getPost('permissions') ?: [];

        if (empty($namaRole)) {
            return redirect()->back()->with('error', 'Nama role wajib diisi.')->withInput();
        }

        $exists = $this->roleModel->where('nama_role', $namaRole)->where('id !=', $id)->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Nama hak akses sudah digunakan.')->withInput();
        }

        $this->roleModel->update($id, [
            'nama_role'   => $namaRole,
            'permissions' => json_encode($permissions),
        ]);

        log_activity('Memperbarui Hak Akses', 'Sistem', 'Role: ' . $namaRole);
        return redirect()->to(base_url('setting/roles'))->with('success', 'Hak akses berhasil diperbarui.');
    }

    public function roles_hapus($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to(base_url('setting/roles'))->with('error', 'Hak akses tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $userCount = $db->table('users')->where('role_id', $id)->countAllResults();
        if ($userCount > 0) {
            return redirect()->to(base_url('setting/roles'))->with('error', 'Hak akses ini tidak bisa dihapus karena masih digunakan oleh ' . $userCount . ' pengguna.');
        }

        $this->roleModel->delete($id);

        log_activity('Menghapus Hak Akses', 'Sistem', 'Role: ' . $role['nama_role']);
        return redirect()->to(base_url('setting/roles'))->with('success', 'Hak akses berhasil dihapus.');
    }
}
