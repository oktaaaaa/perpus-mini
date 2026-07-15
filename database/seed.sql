-- =========================================================
-- PERPUSMINI - Data Sampel Siap Uji
-- Password untuk semua akun contoh: "password123"
-- Hash di bawah dihasilkan dengan password_hash('password123', PASSWORD_DEFAULT)
-- =========================================================

USE perpus_mini;

-- ---------------------------------------------------------
-- Akun login contoh (Admin & Member)
-- ---------------------------------------------------------
INSERT INTO users (name, email, password, role) VALUES
('Admin Utama', 'admin@perpus.test', '$2y$10$UqvRZJ/tllPVRd1xURA4Se80XM0wPSRUC.Dsw9K20qSbOA1qNR9BG', 'admin'),
('Habib Member', 'member@perpus.test', '$2y$10$UqvRZJ/tllPVRd1xURA4Se80XM0wPSRUC.Dsw9K20qSbOA1qNR9BG', 'member');

-- ---------------------------------------------------------
-- Kategori
-- ---------------------------------------------------------
INSERT INTO kategori (nama, slug) VALUES
('Fiksi Ilmiah', 'fiksi-ilmiah'),
('Fantasi', 'fantasi'),
('Misteri & Thriller', 'misteri-thriller'),
('Romansa', 'romansa'),
('Pengembangan Diri', 'pengembangan-diri'),
('Biografi', 'biografi'),
('Kesehatan & Gaya Hidup', 'kesehatan-gaya-hidup'),
('Agama & Spiritualitas', 'agama-spiritualitas'),
('Sastra Klasik', 'sastra-klasik'),
('Komik & Manga', 'komik-manga');

-- ---------------------------------------------------------
-- Buku (relasi ke kategori "Pengembangan Diri" & "Bisnis")
-- ---------------------------------------------------------
INSERT INTO buku (judul, penulis, kategori_id, deskripsi, cover, stok, ebook_price) VALUES
('The $100 Startup', 'Chris Guillebeau', 5, 'Cara membangun bisnis kecil bermodal minim namun berpenghasilan besar.', NULL, 4, 67000),
('Atomic Habits', 'James Clear', 5, 'Perubahan kecil yang membawa hasil luar biasa lewat kebiasaan.', NULL, 6, 85000),
('Deep Work', 'Cal Newport', 5, 'Aturan fokus untuk kesuksesan di dunia yang penuh distraksi.', NULL, 3, 79000),
('The 7 Habits of Highly Effective People', 'Stephen R. Covey', 5, 'Tujuh kebiasaan dasar orang-orang yang efektif.', NULL, 5, 75000);

-- ---------------------------------------------------------
-- Contoh transaksi peminjaman
-- ---------------------------------------------------------
INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, tanggal_jatuh_tempo, status) VALUES
(2, 2, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'dipinjam');
