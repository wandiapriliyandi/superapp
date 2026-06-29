<?php
 
namespace App\Database\Seeds;
 
use CodeIgniter\Database\Seeder;
 
class UserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Bersihkan data lama jika ada (disable check foreign key dulu)
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('users')->truncate();
        $db->table('roles')->truncate();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Insert data ke tabel roles
        $roles = [
            [
                'nama_role'   => 'superadmin',
                'permissions' => json_encode(['*']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nama_role'   => 'petugas_perijinan',
                'permissions' => json_encode(['perijinan']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nama_role'   => 'petugas_kesehatan',
                'permissions' => json_encode(['poskestren']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nama_role'   => 'pimpinan',
                'permissions' => json_encode(['monitoring']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            // ── Role Wakamad & Tata Usaha ──────────────────────────────────
            [
                'nama_role'   => 'Wakamad Kesiswaan',
                'permissions' => json_encode(['perijinan', 'akademik', 'poskestren', 'spp', 'alquran']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nama_role'   => 'Wakamad Sarpras',
                'permissions' => json_encode(['sarpras']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nama_role'   => 'Wakamad Humas',
                'permissions' => json_encode(['ppdb', 'akademik']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nama_role'   => 'Wakamad Kurikulum',
                'permissions' => json_encode(['akademik', 'e-learning', 'perpustakaan', 'alquran']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
            [
                'nama_role'   => 'Tata Usaha',
                'permissions' => json_encode(['kepegawaian', 'keuangan', 'spp', 'sarpras', 'ppdb', 'setting']),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ],
        ];

        $roleIds = [];
        foreach ($roles as $role) {
            $db->table('roles')->insert($role);
            $roleIds[$role['nama_role']] = $db->insertID();
        }

        // 3. Insert data ke tabel users terikat dengan role_id
        $users = [
            [
                'username'     => 'wandi',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wandi Apriliyandi',
                'role_id'      => $roleIds['superadmin'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'ijin',
                'password'     => password_hash('ijin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Perizinan',
                'role_id'      => $roleIds['petugas_perijinan'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'sehat',
                'password'     => password_hash('sehat123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Kesehatan',
                'role_id'      => $roleIds['petugas_kesehatan'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'pimpinan',
                'password'     => password_hash('pimpin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Kepala Pesantren',
                'role_id'      => $roleIds['pimpinan'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            // ── User demo Wakamad & Tata Usaha ──────────────────────────────
            [
                'username'     => 'kesiswaan',
                'password'     => password_hash('kesiswaan123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wakamad Kesiswaan',
                'role_id'      => $roleIds['Wakamad Kesiswaan'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'sarpras',
                'password'     => password_hash('sarpras123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wakamad Sarpras',
                'role_id'      => $roleIds['Wakamad Sarpras'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'humas',
                'password'     => password_hash('humas123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wakamad Humas',
                'role_id'      => $roleIds['Wakamad Humas'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'kurikulum',
                'password'     => password_hash('kurikulum123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wakamad Kurikulum',
                'role_id'      => $roleIds['Wakamad Kurikulum'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'tu',
                'password'     => password_hash('tu123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Tata Usaha',
                'role_id'      => $roleIds['Tata Usaha'],
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($users as $user) {
            $db->table('users')->insert($user);
        }
    }
}
