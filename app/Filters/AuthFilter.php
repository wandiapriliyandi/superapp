<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Mapping kata kunci URL ke suffix aksi permission.
     * Misalnya: URL mengandung 'add' → cek permission '{modul}_create'.
     */
    private array $actionMap = [
        // CREATE
        'create'   => 'create',
        'add'      => 'create',
        'save'     => 'create',
        'store'    => 'create',
        'tambah'   => 'create',
        'simpan'   => 'create',
        'generate' => 'create',
        'bayar'    => 'create',
        'input'    => 'create',
        'import'   => 'create',
        'daftar'   => 'create',
        'register' => 'create',

        // UPDATE
        'edit'     => 'update',
        'update'   => 'update',
        'ubah'     => 'update',
        'perbarui' => 'update',
        'approve'  => 'update',
        'status'   => 'update',
        'verify'   => 'update',
        'aktivasi' => 'update',

        // DELETE
        'delete'  => 'delete',
        'remove'  => 'delete',
        'destroy' => 'delete',
        'hapus'   => 'delete',

        // EXPORT
        'export'    => 'export',
        'print'     => 'export',
        'cetak'     => 'export',
        'pdf'       => 'export',
        'excel'     => 'export',
        'word'      => 'export',
        'rapor'     => 'export',
        'kwitansi'  => 'export',
        'laporan'   => 'export',
        'rekap'     => 'export',
        'download'  => 'export',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Ambil path URL dan segmen-segmennya
        $uri      = $request->getUri();
        $path     = $uri->getPath();
        $segments = explode('/', trim($path, '/'));
        $module   = $segments[0] ?? '';

        $permissions = session()->get('permissions') ?: [];

        // 3. Superadmin (*) dilewati untuk semua modul
        if (in_array('*', $permissions)) {
            return;
        }

        // 4. Modul yang dilindungi
        $protectedModules = [
            'perijinan', 'poskestren', 'monitoring', 'setting', 'activity',
            'akademik', 'spp', 'keuangan', 'kepegawaian', 'perpustakaan', 'ppdb', 'e-learning'
        ];

        if (!in_array($module, $protectedModules)) {
            return; // Modul tidak terdaftar, lewati cek
        }

        // 5. Modul sistem hanya bisa diakses superadmin (sudah ditangani di atas)
        $systemModules = ['setting', 'activity'];
        if (in_array($module, $systemModules)) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki hak akses superadmin.');
        }

        // 6. Cek permission akses modul dasar
        if (!in_array($module, $permissions)) {
            return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki hak akses ke modul ' . ucfirst($module) . '.');
        }

        // 7. Deteksi aksi CRUD dari segmen URL & cek permission granular
        $detectedAction = $this->detectAction($segments);

        if ($detectedAction !== null) {
            $requiredPermission = $module . '_' . $detectedAction;

            if (!in_array($requiredPermission, $permissions)) {
                $actionLabel = [
                    'create' => 'menambah data',
                    'update' => 'mengubah data',
                    'delete' => 'menghapus data',
                    'export' => 'mengekspor/mencetak data',
                ][$detectedAction] ?? $detectedAction;

                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki izin untuk ' . $actionLabel . ' di modul ' . ucfirst($module) . '.');
            }
        }
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
        return null; // Aksi READ — tidak perlu permission tambahan
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request selesai
    }
}
