<?php

namespace App\Traits;

trait Indexable
{
    /**
     * Render halaman index standar dengan filter otomatis
     * 
     * @param object $model Model CI4
     * @param array $filters Daftar field yang boleh difilter (GET)
     * @param string $viewPath Path ke file view
     * @param string $title Judul halaman
     * @param array $extraData Data tambahan untuk dikirim ke view
     */
    protected function renderIndex($model, $filters = [], $viewPath = '', $title = 'Daftar Data', $dataName = 'data', $extraData = [], $hardFilters = [])
    {
        $request = service('request');
        $query = $model;

        // Log Aktivitas Otomatis
        log_activity('Melihat Halaman ' . $title, $extraData['module'] ?? 'Sistem');

        // 1. Terapkan Filter Permanen (Hardcoded)
        if (!empty($hardFilters)) {
            $query->where($hardFilters);
        }

        // 2. Terapkan Filter Dinamis (Dari URL/GET)
        foreach ($filters as $key => $val) {
            $dbField = is_numeric($key) ? $val : $key;
            $getParam = $val;

            if ($value = $request->getGet($getParam)) {
                $query->where($dbField, $value);
            }
        }

        // 3. Terapkan Pencarian Global (Keyword 'q')
        if ($q = $request->getGet('q')) {
            $allowedFields = $model->allowedFields;
            $query->groupStart();
            foreach ($allowedFields as $field) {
                $query->orLike($field, $q);
            }
            $query->groupEnd();
        }

        // 2. Ambil data
        $result = $query->orderBy($model->primaryKey, 'DESC')->findAll();

        // 3. Deteksi API Request (JSON)
        if ($request->is('json') || $request->getHeaderLine('Accept') === 'application/json') {
            return $this->respond([
                'status' => 200,
                'message' => 'Data Berhasil Diambil',
                'data' => $result
            ]);
        }

        // 4. Render View Web dengan nama variabel kustom
        $viewData = array_merge([
            'title'    => $title,
            $dataName  => $result, // Nama variabel dinamis (santri, ta, pendaftar, dll)
        ], $extraData);

        return view($viewPath, $viewData);
    }
}
