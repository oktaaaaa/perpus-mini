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

## 3. Panduan Instalasi Lokal (XAMPP / Laragon)

### a. Clone / Salin project

Letakkan folder ini di dalam `htdocs` (XAMPP) atau `www` (Laragon), misalnya:
`C:\xampp\htdocs\perpus-mini`

### b. Buat database

1. Buka phpMyAdmin, buat database baru **atau** langsung import
   `database/schema.sql` (query di dalamnya sudah termasuk `CREATE DATABASE`).
2. Import juga `database/seed.sql` untuk mengisi data sampel (akun login,
   kategori, dan buku contoh).

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p perpus_mini < database/seed.sql
```

### c. Konfigurasi koneksi

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

### d. Arahkan Document Root ke folder `public/`

- **XAMPP/Apache**: pastikan `mod_rewrite` aktif, lalu akses
  `http://localhost/perpus-mini/public/`
- **Laragon**: otomatis mendeteksi folder `public/` sebagai document root
  jika menggunakan Virtual Host (disarankan), akses `http://perpus-mini.test/`

### e. Pastikan folder upload bisa ditulis

Pastikan folder berikut memiliki izin tulis (write permission):

```
public/assets/uploads/covers/
public/assets/uploads/bukti/
```

### f. Selesai! Buka di browser

```
http://localhost/perpus-mini/public/
```

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

## 6. Pembagian Tugas Anggota Kelompok

> Isi bagian ini sesuai kontribusi nyata masing-masing anggota (silakan sunting):

| Nama Anggota | NIM | Tugas / Modul yang Dikerjakan |
|---|---|---|
| _(isi nama)_ | _(isi NIM)_ | Arsitektur MVC, Auth & RBAC, modul Peminjaman-Pengembalian-Denda |
| _(isi nama)_ | _(isi NIM)_ | Modul Buku, Kategori, Anggota, eBook Store, UI/Styling |

Riwayat commit di GitHub digunakan sebagai bukti kontribusi masing-masing anggota.

## 7. Aturan Bisnis Default (bisa diubah di `config/config.php`)

- Maksimal peminjaman bersamaan per anggota: **3 buku**
- Lama pinjam default: **7 hari** (admin bisa ubah saat approve)
- Tarif denda keterlambatan: **Rp 1.000/hari**
- Maksimal ukuran file upload: **2MB**
