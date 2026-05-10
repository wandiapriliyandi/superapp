<?php

namespace App\Traits;

trait Selectable
{
    /**
     * Menyediakan data untuk Select2 Autocomplete
     * 
     * @param object $model Model yang digunakan
     * @param string $searchField Kolom yang akan dicari (misal: nama_lengkap)
     * @param string $idField Kolom ID (misal: id)
     */
    protected function renderSelection($model, $searchField = 'nama_lengkap', $filters = [], $idField = 'id')
    {
        $request = service('request');
        $searchTerm = $request->getGet('q'); 

        $query = $model->select("$idField as id, $searchField as text");

        if (!empty($filters)) {
            $query->where($filters);
        }

        if ($searchTerm) {
            $query->like($searchField, $searchTerm);
        }

        $results = $query->limit(20)->findAll();

        return $this->response->setJSON($results);
    }
}
