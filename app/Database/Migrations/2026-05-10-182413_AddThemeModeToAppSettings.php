<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddThemeModeToAppSettings extends Migration
{
    public function up()
    {
        $fields = [
            'theme_mode' => [
                'type'       => 'ENUM',
                'constraint' => ['light', 'dark', 'midnight'],
                'default'    => 'light',
                'after'      => 'app_name'
            ],
        ];
        $this->forge->addColumn('app_settings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('app_settings', 'theme_mode');
    }
}
