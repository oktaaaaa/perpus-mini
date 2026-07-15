-- =========================================================
-- PERPUSMINI - Sistem Informasi Perpustakaan Mini
-- Struktur Database (MySQL / MariaDB)
-- =========================================================

CREATE DATABASE IF NOT EXISTS perpus_mini CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpus_mini;

-- ---------------------------------------------------------
-- Tabel: users (Admin & Member / RBAC)
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','member') NOT NULL DEFAULT 'member',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: kategori
-- ---------------------------------------------------------
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: buku (relasi ke kategori)
-- ---------------------------------------------------------
CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    penulis VARCHAR(150) NOT NULL,
    kategori_id INT NULL,
    deskripsi TEXT NULL,
    cover VARCHAR(255) NULL,
    stok INT NOT NULL DEFAULT 0,
    ebook_price INT NOT NULL DEFAULT 0,
    ebook_file VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: peminjaman (relasi ke users & buku)
-- ---------------------------------------------------------
CREATE TABLE peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    tanggal_pinjam DATE NULL,
    tanggal_jatuh_tempo DATE NULL,
    tanggal_kembali DATE NULL,
    status ENUM('menunggu','dipinjam','dikembalikan','ditolak') NOT NULL DEFAULT 'menunggu',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: denda (relasi ke peminjaman)
-- ---------------------------------------------------------
CREATE TABLE denda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    peminjaman_id INT NOT NULL,
    jenis ENUM('terlambat','rusak','hilang') NOT NULL DEFAULT 'terlambat',
    jumlah INT NOT NULL DEFAULT 0,
    keterangan VARCHAR(255) NULL,
    status ENUM('belum_lunas','lunas') NOT NULL DEFAULT 'belum_lunas',
    bukti_bayar VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (peminjaman_id) REFERENCES peminjaman(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel: pembelian_ebook (relasi ke users & buku)
-- ---------------------------------------------------------
CREATE TABLE pembelian_ebook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    harga INT NOT NULL DEFAULT 0,
    bukti_bayar VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB;
