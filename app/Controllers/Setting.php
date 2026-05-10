<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Setting extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        $data['setting'] = $db->table('app_settings')->get()->getRowArray();
        $data['title'] = 'Profil Pesantren';

        return view('setting/index', $data);
    }

    public function theme()
    {
        $db = \Config\Database::connect();
        $data['setting'] = $db->table('app_settings')->get()->getRowArray();
        $data['title'] = 'Pengaturan Tema Aplikasi';

        return view('setting/theme', $data);
    }

    public function update()
    {
        helper('activity');
        $db = \Config\Database::connect();
        $setting = $db->table('app_settings')->get()->getRowArray();
        
        $data = [];
        $fields = ['app_name', 'pesantren_name', 'alamat', 'telepon', 'email', 'theme_mode', 'theme_primary'];
        
        foreach ($fields as $field) {
            $val = $this->request->getPost($field);
            if ($val !== null) {
                $data[$field] = $val;
            }
        }
        
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Proses Upload Logo
        $file = $this->request->getFile('app_logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Buat folder jika belum ada
            if (!is_dir(FCPATH . 'uploads/img/')) {
                mkdir(FCPATH . 'uploads/img/', 0777, true);
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/img/', $newName);
            
            // Hapus logo lama
            if ($setting && $setting['app_logo'] && file_exists(FCPATH . 'uploads/img/' . $setting['app_logo'])) {
                unlink(FCPATH . 'uploads/img/' . $setting['app_logo']);
            }
            
            $data['app_logo'] = $newName;
        }

        if ($setting) {
            $db->table('app_settings')->where('id', $setting['id'])->update($data);
        } else {
            $db->table('app_settings')->insert($data);
        }

        log_activity('Memperbarui Pengaturan Sistem', 'Sistem');
        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
