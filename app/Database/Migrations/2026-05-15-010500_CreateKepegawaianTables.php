<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKepegawaianTables extends Migration
{
    public function up()
    {
        // 1. Departemen
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_departemen' => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('hr_departemen');

        // 2. Jabatan
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_jabatan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'gaji_pokok' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tunjangan_makan' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'tunjangan_transport' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('hr_jabatan');

        // 3. Pegawai
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nik' => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'nama_lengkap' => ['type' => 'VARCHAR', 'constraint' => 150],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'null' => true],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'no_hp' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'departemen_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'jabatan_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status_pegawai' => ['type' => 'ENUM', 'constraint' => ['Tetap', 'Kontrak', 'Probation', 'Resign'], 'default' => 'Probation'],
            'tanggal_masuk' => ['type' => 'DATE', 'null' => true],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('departemen_id', 'hr_departemen', 'id', 'SET NULL', 'SET NULL');
        $this->forge->addForeignKey('jabatan_id', 'hr_jabatan', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('hr_pegawai');

        // 4. Absensi
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pegawai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal' => ['type' => 'DATE'],
            'jam_masuk' => ['type' => 'TIME', 'null' => true],
            'jam_pulang' => ['type' => 'TIME', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Cuti'], 'default' => 'Hadir'],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pegawai_id', 'hr_pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('hr_absensi');

        // 5. Cuti
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pegawai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_cuti' => ['type' => 'VARCHAR', 'constraint' => 50],
            'tanggal_mulai' => ['type' => 'DATE'],
            'tanggal_selesai' => ['type' => 'DATE'],
            'alasan' => ['type' => 'TEXT', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['Pending', 'Disetujui', 'Ditolak'], 'default' => 'Pending'],
            'disetujui_oleh' => ['type' => 'INT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pegawai_id', 'hr_pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('hr_cuti');

        // 6. Payroll
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'pegawai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'bulan' => ['type' => 'INT', 'constraint' => 2],
            'tahun' => ['type' => 'INT', 'constraint' => 4],
            'gaji_pokok' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'total_tunjangan' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'potongan' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0],
            'gaji_bersih' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'status_bayar' => ['type' => 'ENUM', 'constraint' => ['Belum Dibayar', 'Sudah Dibayar'], 'default' => 'Belum Dibayar'],
            'tanggal_bayar' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pegawai_id', 'hr_pegawai', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('hr_payroll');
    }

    public function down()
    {
        $this->forge->dropTable('hr_payroll');
        $this->forge->dropTable('hr_cuti');
        $this->forge->dropTable('hr_absensi');
        $this->forge->dropTable('hr_pegawai');
        $this->forge->dropTable('hr_jabatan');
        $this->forge->dropTable('hr_departemen');
    }
}
