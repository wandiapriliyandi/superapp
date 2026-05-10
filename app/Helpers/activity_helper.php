<?php

if (!function_exists('log_activity')) {
    /**
     * Mencatat aktivitas pengguna ke database
     */
    function log_activity($activity, $module, $details = '')
    {
        $activityModel = new \App\Models\ActivityModel();
        $request = \Config\Services::request();

        $data = [
            'user'       => 'Admin', // Nanti ganti dengan session user jika sudah ada login
            'activity'   => $activity,
            'module'     => $module,
            'details'    => $details,
            'ip_address' => $request->getIPAddress(),
        ];

        return $activityModel->insert($data);
    }
}
