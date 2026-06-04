<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Mapping kata kunci URL ke suffix aksi permission.
     * Deteksi dari SEMUA segmen URL.
     */
    private array $actionMap = [
        // CREATE
        'create'   => 'create', 'add'      => 'create', 'save'     => 'create',
        'store'    => 'create', 'tambah'   => 'create', 'simpan'   => 'create',
        'generate' => 'create', 'bayar'    => 'create', 'input'    => 'create',
        'import'   => 'create', 'daftar'   => 'create', 'register' => 'create',
        'process-generate' => 'create', 'save-massal' => 'create',
        'bayar-massal'     => 'create',

        // UPDATE
        'edit'     => 'update', 'update'   => 'update', 'ubah'     => 'update',
        'perbarui' => 'update', 'approve'  => 'update', 'status'   => 'update',
        'verify'   => 'update', 'aktivasi' => 'update', 'set-active' => 'update',
        'update-kehadiran' => 'update', 'update-status' => 'update',
        'syarat-save'      => 'update',

        // DELETE
        'delete'  => 'delete', 'remove'  => 'delete', 'destroy'  => 'delete',
        'hapus'   => 'delete', 'syarat-delete' => 'delete', 'remove-peserta' => 'delete',

        // EXPORT
        'export'       => 'export', 'print'        => 'export', 'cetak'        => 'export',
        'pdf'          => 'export', 'excel'        => 'export', 'word'         => 'export',
        'rapor'        => 'export', 'kwitansi'     => 'export', 'laporan'      => 'export',
        'rekap'        => 'export', 'download'     => 'export', 'slip'         => 'export',
        'export-excel' => 'export', 'export-word'  => 'export', 'export-pdf'   => 'export',
        'cetak-struk'  => 'export',
    ];

    /**
     * Sub-menu yang dikenali per modul.
     * Key = nama modul, Value = array sub-menu key yang valid.
     */
    private array $moduleSubMenus = [
        'spp'          => ['tarif', 'tagihan', 'pembayaran', 'mapping'],
        'akademik'     => ['santri', 'tahun-ajaran', 'kelas', 'mapel', 'jadwal', 'presensi', 'nilai'],
        'kepegawaian'  => ['pegawai', 'departemen', 'jabatan', 'jadwal', 'absensi', 'cuti', 'payroll'],
        'perpustakaan' => ['buku', 'anggota', 'peminjaman', 'pengaturan'],
        'ppdb'         => ['pendaftar', 'berkas', 'jadwal', 'pengaturan'],
        'perijinan'    => ['perijinan', 'rekap', 'pengaturan'],
        'poskestren'   => ['kunjungan', 'obat', 'stok'],
        'keuangan'     => ['akun', 'jurnal', 'buku-besar', 'laporan'],
        'monitoring'   => [],
        'e-learning'   => [],
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek login
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Parse URL
        $path     = $request->getUri()->getPath();
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        $module   = $segments[0] ?? '';
        $subMenu  = $segments[1] ?? '';

        $permissions = session()->get('permissions') ?: [];

        // 3. Superadmin bypass
        if (in_array('*', $permissions)) {
            return;
        }

        // 4. Modul yang dilindungi
        $protectedModules = [
            'perijinan', 'poskestren', 'monitoring', 'setting', 'activity',
            'akademik', 'spp', 'keuangan', 'kepegawaian', 'perpustakaan', 'ppdb', 'e-learning'
        ];

        if (!in_array($module, $protectedModules)) {
            return; // bukan modul terlindungi, lewati
        }

        // 5. Modul sistem hanya superadmin
        if (in_array($module, ['setting', 'activity'])) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki hak akses superadmin.');
        }

        // 6. Cek permission modul dasar
        if (!in_array($module, $permissions)) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki hak akses ke modul ' . ucfirst($module) . '.');
        }

        // 7. Cek permission sub-menu (jika modul punya sub-menu terdaftar)
        $knownSubMenus = $this->moduleSubMenus[$module] ?? [];

        if (!empty($knownSubMenus) && !empty($subMenu) && in_array($subMenu, $knownSubMenus)) {
            // Cek apakah role memiliki SETIDAKNYA SATU permission sub-menu untuk modul ini.
            // Jika tidak ada → backward-compat: izinkan semua sub-menu.
            $hasAnySubMenuPerm = $this->hasAnySubMenuPermission($module, $knownSubMenus, $permissions);

            if ($hasAnySubMenuPerm) {
                // Role sudah dikonfigurasi sub-menu → terapkan cek
                $subMenuPermKey = $module . '.' . $subMenu;
                if (!in_array($subMenuPermKey, $permissions)) {
                    return redirect()->to(base_url($module))->with('error', 'Anda tidak memiliki akses ke menu ' . ucfirst(str_replace('-', ' ', $subMenu)) . ' di modul ' . ucfirst($module) . '.');
                }
            }
            // else: role lama tanpa sub-menu perm → lewati, izinkan
        }

        // 8. Deteksi aksi CRUD dari segmen URL & cek permission granular
        $detectedAction = $this->detectAction($segments);

        if ($detectedAction !== null) {
            // Tentukan prefix permission: pakai sub-menu jika relevan, else modul saja
            $permPrefix = (!empty($subMenu) && in_array($subMenu, $knownSubMenus))
                ? $module . '.' . $subMenu
                : $module;

            $requiredPermission = $permPrefix . '_' . $detectedAction;

            if (!in_array($requiredPermission, $permissions)) {
                $actionLabel = [
                    'create' => 'menambah data',
                    'update' => 'mengubah data',
                    'delete' => 'menghapus data',
                    'export' => 'mengekspor/mencetak data',
                ][$detectedAction] ?? $detectedAction;

                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki izin untuk ' . $actionLabel . ' di ' . ucfirst(str_replace('-', ' ', $subMenu ?: $module)) . '.');
            }
        }
    }

    /**
     * Cek apakah di session permissions ada setidaknya satu permission sub-menu
     * untuk modul tertentu (format: 'modul.submenu').
     */
    private function hasAnySubMenuPermission(string $module, array $knownSubMenus, array $permissions): bool
    {
        foreach ($knownSubMenus as $sm) {
            if (in_array($module . '.' . $sm, $permissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Mendeteksi jenis aksi dari segmen-segmen URL.
     * Mengembalikan string aksi ('create','update','delete','export') atau null jika hanya READ.
     */
    private function detectAction(array $segments): ?string
    {
        foreach ($segments as $segment) {
            $seg = strtolower($segment);
            if (isset($this->actionMap[$seg])) {
                return $this->actionMap[$seg];
            }
        }
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request selesai
    }
}
