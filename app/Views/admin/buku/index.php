<?php
$title = 'Kelola Buku';

// Kelompokkan buku berdasarkan kategori untuk ditampilkan per-seksi
$grouped = [];
foreach ($buku as $b) {
    $namaKategori = $b['kategori_nama'] ?: 'Tanpa Kategori';
    $grouped[$namaKategori][] = $b;
}
ksort($grouped);

// Helper render bintang rating (dipakai di kartu & modal)
if (!function_exists('render_bintang')) {
    function render_bintang(float $rating): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($rating >= $i - 0.5) {
                $html .= '<span class="text-amber-400">&#9733;</span>'; // bintang penuh (dibulatkan)
            } else {
                $html .= '<span class="text-slate-700">&#9733;</span>'; // bintang kosong
            }
        }
        return $html;
    }
}
?>
<div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
    <form method="GET" class="flex-1 max-w-sm">
        <input type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari buku atau penulis..."
            class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
    </form>
    <a href="<?= url('/admin/buku/create') ?>" class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm font-medium whitespace-nowrap">+ Buku Baru</a>
</div>

<?php if (empty($buku)): ?>
    <div class="card p-10 text-center text-sm text-slate-500">Belum ada data buku.</div>
<?php else: ?>

<?php foreach ($grouped as $namaKategori => $daftarBuku): ?>
    <div class="mb-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-gradient-to-br from-violet-400 to-pink-400"></span>
                <?= e($namaKategori) ?>
                <span class="text-xs font-normal text-slate-500">(<?= count($daftarBuku) ?> buku)</span>
            </h3>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <?php foreach ($daftarBuku as $b): ?>
                <div class="card overflow-hidden">
                    <div onclick="document.getElementById('modalDetail<?= $b['id'] ?>').classList.remove('hidden')"
                        class="cursor-pointer">
                        <div class="h-40 bg-gradient-to-br from-violet-900/40 to-pink-900/20 flex items-center justify-center">
                            <?php if (!empty($b['cover'])): ?>
                                <img src="<?= asset('uploads/covers/' . e($b['cover'])) ?>" class="h-full w-full object-cover">
                            <?php else: ?><span class="text-3xl">📖</span><?php endif; ?>
                        </div>
                        <div class="px-3 pt-3">
                            <p class="text-sm font-medium text-white truncate hover:text-violet-300"><?= e($b['judul']) ?></p>
                            <p class="text-xs text-slate-500 truncate"><?= e($b['penulis']) ?></p>
                            <div class="text-xs mt-1"><?= render_bintang((float) $b['rating']) ?> <span class="text-slate-500 ml-1"><?= number_format((float) $b['rating'], 1) ?></span></div>
                            <div class="flex justify-between items-center mt-2 text-xs">
                                <span class="text-slate-400">Stok: <span class="text-slate-300 font-medium"><?= (int) $b['stok'] ?></span></span>
                                <span class="text-slate-400"><?= $b['ebook_price'] > 0 ? rupiah($b['ebook_price']) : '-' ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 pt-2">
                        <div class="flex gap-2">
                            <a href="<?= url('/admin/buku/' . $b['id'] . '/edit') ?>" class="flex-1 text-center text-xs py-1.5 rounded-lg border border-white/10 hover:bg-white/5">Edit</a>
                            <form method="POST" action="<?= url('/admin/buku/' . $b['id'] . '/hapus') ?>" onsubmit="return confirm('Hapus buku ini?')" class="flex-1">
                                <?= csrf_field() ?>
                                <button class="w-full text-xs py-1.5 rounded-lg border border-rose-500/30 text-rose-400 hover:bg-rose-500/10">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

<?php endif; ?>

<?php foreach ($buku as $b): ?>
    <div id="modalDetail<?= $b['id'] ?>" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
        <div class="card p-6 w-full max-w-lg">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <span class="text-[11px] px-2 py-0.5 rounded bg-violet-500/20 text-violet-300"><?= e($b['kategori_nama'] ?? 'Tanpa Kategori') ?></span>
                    <h3 class="text-white font-semibold mt-1.5"><?= e($b['judul']) ?></h3>
                    <p class="text-xs text-slate-500">oleh <?= e($b['penulis']) ?></p>
                </div>
                <button onclick="document.getElementById('modalDetail<?= $b['id'] ?>').classList.add('hidden')"
                    class="text-slate-500 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <div class="flex items-center gap-2 mb-4">
                <div class="text-sm"><?= render_bintang((float) $b['rating']) ?></div>
                <span class="text-sm text-slate-400"><?= number_format((float) $b['rating'], 1) ?> / 5</span>
            </div>

            <p class="text-sm font-medium text-white mb-1">Sinopsis</p>
            <p class="text-sm text-slate-400 leading-relaxed">
                <?= nl2br(e($b['deskripsi'] ?: 'Belum ada deskripsi untuk buku ini.')) ?>
            </p>

            <div class="flex gap-4 text-xs text-slate-500 mt-4 pt-4 border-t border-white/5">
                <span>Stok: <strong class="text-slate-300"><?= (int) $b['stok'] ?></strong></span>
                <span>Harga eBook: <strong class="text-slate-300"><?= $b['ebook_price'] > 0 ? rupiah($b['ebook_price']) : 'RP 80.000' ?></strong></span>
            </div>
        </div>
    </div>
<?php endforeach; ?>