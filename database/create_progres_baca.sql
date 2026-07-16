CREATE TABLE IF NOT EXISTS progres_baca (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    buku_id INT NOT NULL,
    progress INT NOT NULL DEFAULT 0,
    rating TINYINT NULL,
    ulasan VARCHAR(255) NULL,
    terakhir_dibaca DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE CASCADE,
    UNIQUE KEY user_buku_unique (user_id, buku_id)
) ENGINE=InnoDB;