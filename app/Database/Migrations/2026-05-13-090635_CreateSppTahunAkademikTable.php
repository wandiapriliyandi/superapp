<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSppTahunAkademikTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_tahun' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Aktif', 'Tidak Aktif'],
                'default'    => 'Aktif',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('spp_tahun_akademik', true);

        // Update spp_tarif to use the new ID
        $this->db->resetDataCache();
        if ($this->db->fieldExists('tahun_ajaran', 'spp_tarif')) {
            $this->forge->dropColumn('spp_tarif', 'tahun_ajaran');
        }
        
        if (!$this->db->fieldExists('id_tahun_akademik', 'spp_tarif')) {
            $this->forge->addColumn('spp_tarif', [
                'id_tahun_akademik' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id'
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('spp_tahun_akademik');

        $this->db->resetDataCache();
        if ($this->db->fieldExists('id_tahun_akademik', 'spp_tarif')) {
            $this->forge->dropColumn('spp_tarif', 'id_tahun_akademik');
        }

        if (!$this->db->fieldExists('tahun_ajaran', 'spp_tarif')) {
            $this->forge->addColumn('spp_tarif', [
                'tahun_ajaran' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                    'after'      => 'id'
                ],
            ]);
        }
    }
}
