<?php

if (!function_exists('log_activity')) {
    /**
     * Mencatat aktivitas pengguna ke database secara dinamis berdasarkan user login
     */
    function log_activity($activity, $module, $details = '')
    {
        $activityModel = new \App\Models\ActivityModel();
        $request = \Config\Services::request();
        $session = session();

        $user = $session->get('nama_lengkap') ?: 'Guest';

        $data = [
            'user'       => $user,
            'activity'   => $activity,
            'module'     => $module,
            'details'    => $details,
            'ip_address' => $request->getIPAddress(),
        ];

        return $activityModel->insert($data);
    }
}
