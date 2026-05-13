<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameSppTahunAkademikToTahunAkademik extends Migration
{
    public function up()
    {
        $this->forge->renameTable('spp_tahun_akademik', 'tahun_akademik');
    }

    public function down()
    {
        $this->forge->renameTable('tahun_akademik', 'spp_tahun_akademik');
    }
}
