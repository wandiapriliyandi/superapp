<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'kode_akun'    => '1-101',
                'nama_akun'    => 'Kas Utama',
                'kategori'     => 'Aset',
                'saldo_normal' => 'Debit',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kode_akun'    => '1-102',
                'nama_akun'    => 'Bank BSI',
                'kategori'     => 'Aset',
                'saldo_normal' => 'Debit',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kode_akun'    => '4-101',
                'nama_akun'    => 'Pendapatan SPP',
                'kategori'     => 'Pendapatan',
                'saldo_normal' => 'Kredit',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kode_akun'    => '4-102',
                'nama_akun'    => 'Pendapatan Pendaftaran (PPDB)',
                'kategori'     => 'Pendapatan',
                'saldo_normal' => 'Kredit',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kode_akun'    => '5-101',
                'nama_akun'    => 'Beban Gaji',
                'kategori'     => 'Beban',
                'saldo_normal' => 'Debit',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kode_akun'    => '5-102',
                'nama_akun'    => 'Beban Listrik & Air',
                'kategori'     => 'Beban',
                'saldo_normal' => 'Debit',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder
        $this->db->table('keu_akun')->insertBatch($data);
    }
}
