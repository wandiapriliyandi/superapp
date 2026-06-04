<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username'     => 'wandi',
                'password'     => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Wandi Apriliyandi',
                'role'         => 'superadmin',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'ijin',
                'password'     => password_hash('ijin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Perizinan',
                'role'         => 'petugas_perijinan',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'sehat',
                'password'     => password_hash('sehat123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Petugas Kesehatan Poskestren',
                'role'         => 'petugas_kesehatan',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'username'     => 'pimpinan',
                'password'     => password_hash('pimpin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Kepala Pesantren',
                'role'         => 'pimpinan',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($data as $row) {
            $this->db->table('users')->insert($row);
        }
    }
}
