CREATE TABLE IF NOT EXISTS hr_departemen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_departemen VARCHAR(100) NOT NULL,
    keterangan TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hr_jabatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_jabatan VARCHAR(100) NOT NULL,
    gaji_pokok DECIMAL(15, 2) DEFAULT 0,
    tunjangan_makan DECIMAL(15, 2) DEFAULT 0,
    tunjangan_transport DECIMAL(15, 2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS hr_pegawai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(150) NOT NULL,
    tempat_lahir VARCHAR(50),
    tanggal_lahir DATE,
    jenis_kelamin ENUM('L', 'P'),
    alamat TEXT,
    no_hp VARCHAR(20),
    email VARCHAR(100),
    departemen_id INT,
    jabatan_id INT,
    status_pegawai ENUM('Tetap', 'Kontrak', 'Probation', 'Resign') DEFAULT 'Probation',
    tanggal_masuk DATE,
    foto VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (departemen_id) REFERENCES hr_departemen(id) ON DELETE SET NULL,
    FOREIGN KEY (jabatan_id) REFERENCES hr_jabatan(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS hr_absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME,
    jam_pulang TIME,
    status ENUM('Hadir', 'Izin', 'Sakit', 'Alpha', 'Cuti') DEFAULT 'Hadir',
    keterangan TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pegawai_id) REFERENCES hr_pegawai(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS hr_cuti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT NOT NULL,
    jenis_cuti VARCHAR(50),
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    alasan TEXT,
    status ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
    disetujui_oleh INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pegawai_id) REFERENCES hr_pegawai(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS hr_payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT NOT NULL,
    bulan INT NOT NULL,
    tahun INT NOT NULL,
    gaji_pokok DECIMAL(15, 2),
    total_tunjangan DECIMAL(15, 2),
    potongan DECIMAL(15, 2) DEFAULT 0,
    gaji_bersih DECIMAL(15, 2),
    status_bayar ENUM('Belum Dibayar', 'Sudah Dibayar') DEFAULT 'Belum Dibayar',
    tanggal_bayar DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pegawai_id) REFERENCES hr_pegawai(id) ON DELETE CASCADE
);
