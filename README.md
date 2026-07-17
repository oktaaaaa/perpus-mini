# Perpus Mini — Sistem Informasi Perpustakaan Mini

Aplikasi web dinamis untuk mengelola perpustakaan: koleksi buku, kategori,
anggota, peminjaman/pengembalian, denda, dan penjualan eBook.

Dibangun **100% PHP Native** dengan arsitektur **MVC mandiri** (tanpa
framework seperti Laravel/CodeIgniter), routing clean URL lewat satu gerbang
(`public/index.php`), dan koneksi database menggunakan **PDO Prepared
Statements**.

---

## 1. Teknologi

- PHP 8.1+ (native, tanpa Composer/vendor)
- MySQL / MariaDB (PDO)
- Tailwind CSS (via CDN) untuk tampilan
- Arsitektur MVC buatan sendiri: `Router`, `Controller`, `Model`, `Auth`, `Validator`, `Upload`

## 2. Struktur Folder

```
perpus-mini/
├─ app/
│  ├─ Core/            -> Router, Controller, Model, Database, Auth, Validator, Upload (kerangka MVC)
│  ├─ Controllers/      -> Logic tiap fitur (Buku, Kategori, Anggota, Peminjaman, dst.)
│  ├─ Models/           -> Representasi tabel database
│  └─ Views/            -> Tampilan (admin/, member/, auth/, home/, layouts/)
├─ config/
│  ├─ config.php        -> Konfigurasi umum & load .env
│  └─ routes.php        -> Daftar seluruh routing aplikasi
├─ database/
│  ├─ schema.sql        -> Struktur tabel (import pertama kali)
│  └─ seed.sql          -> Data sampel siap uji
├─ public/               -> DOCUMENT ROOT (arahkan web server ke folder ini)
│  ├─ index.php          -> Gerbang tunggal (front controller)
│  ├─ .htaccess          -> Rewrite rule clean URL
│  └─ assets/            -> css & folder upload
├─ .env.example
└─ README.md
```

### 3. Konfigurasi Koneksi

Salin `.env.example` menjadi `.env`, lalu sesuaikan:

```
APP_URL=
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=perpus_mini
DB_USER=root
DB_PASS=
```

> Jika project diakses lewat sub-folder (misal `http://localhost/perpus-mini/public`),
> biarkan `APP_URL` kosong — router otomatis mendeteksi sub-folder.

---

## 4. Akun Login (Data Sampel)

| Role   | Email                | Password      |
|--------|-----------------------|----------------|
| Admin  | admin@perpus.test     | password123    |
| Member | member@perpus.test    | password123    |

Anggota baru juga bisa mendaftar mandiri lewat halaman **Daftar Gratis**
(otomatis menjadi role Member).

## 5. Ringkasan Fitur

**Admin**
- Dashboard (statistik, grafik tren peminjaman, buku populer)
- CRUD Koleksi Buku (dengan upload cover, validasi ukuran & tipe file)
- CRUD Kategori Buku
- Kelola Anggota (tambah/hapus)
- Kelola Peminjaman (setujui/tolak pengajuan, atur lama pinjam)
- Kelola Pengembalian (terima buku, denda keterlambatan otomatis, catat denda rusak/hilang manual)
- Kelola Denda (tandai lunas)
- Kelola eBook (riwayat pembelian & pendapatan)

**Member**
- Katalog Buku (cari & filter kategori, ajukan pinjam)
- Riwayat
- eBook Store (beli eBook dengan upload bukti pembayaran)

**Keamanan & Validasi**
- Password di-hash dengan `password_hash()` / `password_verify()`
- RBAC: proteksi backend-side — Member yang mencoba mengakses URL Admin
  otomatis ditolak & diarahkan kembali
- Semua query database memakai PDO Prepared Statement (anti SQL Injection)
- Validasi server-side di semua form (wajib isi, format email, tipe angka, dll)
- Try-catch di seluruh operasi database — error tidak pernah tampil mentah ke user
- Upload file divalidasi ketat: maksimal 2MB, ekstensi & MIME type diperiksa

## 6. Aturan Bisnis Default (bisa diubah di `config/config.php`)

- Maksimal peminjaman bersamaan per anggota: **3 buku**
- Lama pinjam default: **7 hari** (admin bisa ubah saat approve)
- Tarif denda keterlambatan: **Rp 1.000/hari**
- Maksimal ukuran file upload: **2MB**

## 7. Tujuan Project

Project ini dibuat sebagai UAS mata kuliah **Pemrograman Web Dinamis**, dengan tujuan menerapkan konsep **MVC** pada PHP Native serta membangun sistem perpustakaan mini secara digital — mulai dari pendataan buku, anggota, peminjaman, pengembalian, hingga perhitungan denda secara otomatis.

## 8. Developer / Tim Pengembang

| Nama | GitHub | Email |
|------|--------|-------|
| Okta Ramadani | [@oktaaaaa](https://github.com/oktaaaaa) | oktaramadany27@gmail.com |
| Tika Aftasari SS | [@tikaaftasariss-coder](https://github.com/tikaaftasariss-coder) | tikaaftasari@gmail.com |

**Program Studi Sistem Informasi — Universitas Merangin**
