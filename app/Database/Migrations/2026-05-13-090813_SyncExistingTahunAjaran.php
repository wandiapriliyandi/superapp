<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncExistingTahunAjaran extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil tahun ajaran unik dari tabel lama
        $existingYears = $db->table('tahun_ajaran')->select('tahun_ajaran')->groupBy('tahun_ajaran')->get()->getResultArray();
        
        foreach ($existingYears as $row) {
            $tahunStr = $row['tahun_ajaran'];
            
            // Cek apakah sudah ada di Master Akademik baru
            $exists = $db->table('spp_tahun_akademik')->where('nama_tahun', $tahunStr)->get()->getRow();
            
            if (!$exists) {
                $db->table('spp_tahun_akademik')->insert([
                    'nama_tahun' => $tahunStr,
                    'status'     => 'Aktif',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // 2. Karena spp_tarif baru saja dikosongkan kolom tahunnya/diubah ke ID, 
        // kita tidak bisa otomatis sync tarif LAMA yang sudah terlanjur dihapus kolomnya di migrasi sebelumnya.
        // TAPI, kita pastikan ke depannya sinkron.
    }

    public function down()
    {
        // No down needed
    }
}
