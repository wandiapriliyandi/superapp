<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function getStats()
    {
        $db = \Config\Database::connect();

        $totalSantri = 0;
        if ($db->tableExists('santri')) {
            $totalSantri = $db->table('santri')->countAllResults();
        }

        $totalKaryawan = 0;
        if ($db->tableExists('karyawan')) {
            $totalKaryawan = $db->table('karyawan')->countAllResults();
        }

        $totalKelas = 0;
        if ($db->tableExists('kelas')) {
            $totalKelas = $db->table('kelas')->countAllResults();
        }

        $totalUsers = 0;
        if ($db->tableExists('users')) {
            $totalUsers = $db->table('users')->countAllResults();
        }

        // 5 log aktivitas terbaru
        $latestLogs = [];
        if ($db->tableExists('activity_logs')) {
            $latestLogs = $db->table('activity_logs')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
        }

        // PPDB Pendaftar
        $totalPpdb = 0;
        if ($db->tableExists('ppdb_pendaftar')) {
            $totalPpdb = $db->table('ppdb_pendaftar')->countAllResults();
        }

        return $this->response->setJSON([
            'status' => 200,
            'data'   => [
                'total_santri'   => $totalSantri,
                'total_karyawan' => $totalKaryawan,
                'total_kelas'    => $totalKelas,
                'total_users'    => $totalUsers,
                'total_ppdb'     => $totalPpdb,
                'latest_logs'    => $latestLogs
            ]
        ]);
    }
}
