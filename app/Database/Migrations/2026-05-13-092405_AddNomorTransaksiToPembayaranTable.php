<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNomorTransaksiToPembayaranTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('spp_pembayaran', [
            'nomor_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'tagihan_id'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('spp_pembayaran', 'nomor_transaksi');
    }
}
