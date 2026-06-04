<?php

namespace Poskestren\Models;

use CodeIgniter\Model;

class StokMutasiModel extends Model
{
    protected $table            = 'pos_stok_mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'obat_id',
        'tipe',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'referensi_id',
        'referensi_tipe',
        'keterangan',
        'petugas_id',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Catat mutasi stok dan update pos_obat.stok dalam satu transaksi.
     *
     * @return array{ok: bool, message: string}
     */
    public function catat(
        int $obatId,
        int $jumlah,
        string $tipe,
        string $jenis,
        ?string $keterangan = null,
        ?int $referensiId = null,
        ?string $referensiTipe = null,
        ?int $petugasId = null,
        bool $useTransaction = true,
        ?string $createdAt = null
    ): array {
        if ($jumlah <= 0) {
            return ['ok' => false, 'message' => 'Jumlah harus lebih dari 0.'];
        }

        if (!in_array($tipe, ['masuk', 'keluar'], true)) {
            return ['ok' => false, 'message' => 'Tipe mutasi tidak valid.'];
        }

        if (!in_array($jenis, ['pengadaan', 'konsumsi', 'musnah'], true)) {
            return ['ok' => false, 'message' => 'Jenis mutasi tidak valid.'];
        }

        $db = $this->db;
        if ($useTransaction) {
            $db->transStart();
        }

        $obat = $db->table('pos_obat')->where('id', $obatId)->get()->getRowArray();
        if (!$obat) {
            if ($useTransaction) {
                $db->transRollback();
            }
            return ['ok' => false, 'message' => 'Obat tidak ditemukan.'];
        }

        $sebelum = (int) $obat['stok'];
        if ($tipe === 'keluar' && $sebelum < $jumlah) {
            if ($useTransaction) {
                $db->transRollback();
            }
            return ['ok' => false, 'message' => 'Stok tidak mencukupi. Tersedia: ' . $sebelum . ' ' . $obat['satuan']];
        }

        $sesudah = $tipe === 'masuk' ? $sebelum + $jumlah : $sebelum - $jumlah;

        $db->table('pos_obat')->where('id', $obatId)->update(['stok' => $sesudah]);

        $this->insert([
            'obat_id'         => $obatId,
            'tipe'            => $tipe,
            'jenis'           => $jenis,
            'jumlah'          => $jumlah,
            'stok_sebelum'    => $sebelum,
            'stok_sesudah'    => $sesudah,
            'referensi_id'    => $referensiId,
            'referensi_tipe'  => $referensiTipe,
            'keterangan'      => $keterangan,
            'petugas_id'      => $petugasId,
            'created_at'      => $createdAt ?: date('Y-m-d H:i:s'),
        ]);

        if ($useTransaction) {
            $db->transComplete();
            if ($db->transStatus() === false) {
                return ['ok' => false, 'message' => 'Gagal mencatat mutasi stok.'];
            }
        }

        return ['ok' => true, 'message' => 'Mutasi stok berhasil dicatat.'];
    }

    public function getRiwayat(?int $obatId = null, int $limit = 100)
    {
        $builder = $this->db->table($this->table)
            ->select('pos_stok_mutasi.*, pos_obat.nama_obat, pos_obat.satuan')
            ->join('pos_obat', 'pos_obat.id = pos_stok_mutasi.obat_id')
            ->orderBy('pos_stok_mutasi.created_at', 'DESC')
            ->orderBy('pos_stok_mutasi.id', 'DESC')
            ->limit($limit);

        if ($obatId) {
            $builder->where('pos_stok_mutasi.obat_id', $obatId);
        }

        return $builder->get()->getResultArray();
    }

    public static function labelJenis(string $jenis, string $tipe): string
    {
        $map = [
            'pengadaan' => 'Pengadaan (stok masuk)',
            'konsumsi'  => 'Konsumsi pasien',
            'musnah'    => 'Dimusnahkan',
        ];

        return $map[$jenis] ?? ($tipe === 'masuk' ? 'Stok masuk' : 'Stok keluar');
    }
}
