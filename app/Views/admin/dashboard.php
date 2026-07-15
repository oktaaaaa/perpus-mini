<?php
$title = 'Dashboard';
$max = max(1, max(array_column($tren, 'jumlah')));
$w = 700; $h = 160; $step = $w / (count($tren) - 1);
$points = [];
foreach ($tren as $i => $t) {
    $x = $i * $step;
    $y = $h - ($t['jumlah'] / $max) * ($h - 20) - 10;
    $points[] = "$x,$y";
}
$polyline = implode(' ', $points);
?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-6">
    <div class="card p-5">
        <p class="text-xs text-slate-500">Total Buku</p>
        <p class="text-2xl font-bold text-white mt-1"><?= (int) $totalBuku ?></p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-slate-500">Total Anggota</p>
        <p class="text-2xl font-bold text-white mt-1"><?= (int) $totalAnggota ?></p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-slate-500">Sedang Dipinjam</p>
        <p class="text-2xl font-bold text-white mt-1"><?= (int) $sedangDipinjam ?></p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-slate-500">Terlambat</p>
        <p class="text-2xl font-bold text-rose-400 mt-1"><?= (int) $terlambat ?></p>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-5 mb-6">
    <div class="card p-5 md:col-span-2">
        <p class="text-sm font-medium text-white mb-4">Tren Peminjaman (7 Hari Terakhir)</p>
        <svg viewBox="0 0 <?= $w ?> <?= $h ?>" class="w-full h-40">
            <polyline fill="none" stroke="url(#grad)" stroke-width="3" points="<?= $polyline ?>" />
            <polygon fill="url(#gradFill)" points="0,<?= $h ?> <?= $polyline ?> <?= $w ?>,<?= $h ?>" opacity="0.25" />
            <defs>
                <linearGradient id="grad" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0%" stop-color="#7c3aed" />
                    <stop offset="100%" stop-color="#ec4899" />
                </linearGradient>
                <linearGradient id="gradFill" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#7c3aed" />
                    <stop offset="100%" stop-color="#ec4899" stop-opacity="0" />
                </linearGradient>
            </defs>
        </svg>
        <div class="flex justify-between text-[10px] text-slate-600 mt-1">
            <?php foreach ($tren as $t): ?><span><?= e($t['tanggal']) ?></span><?php endforeach; ?>
        </div>
    </div>

    <div class="card p-5">
        <p class="text-sm font-medium text-white mb-4">Ringkasan</p>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between"><span class="text-slate-400">Menunggu Persetujuan</span><span class="text-white font-medium"><?= (int) $menunggu ?></span></div>
            <div class="flex justify-between"><span class="text-slate-400">Denda Belum Lunas</span><span class="text-rose-400 font-medium"><?= rupiah($totalDendaBelumLunas) ?></span></div>
        </div>
        <a href="<?= url('/admin/peminjaman') ?>" class="block mt-4 text-center text-xs px-3 py-2 rounded-lg border border-white/10 hover:bg-white/5">Kelola Pengajuan &rarr;</a>
    </div>
</div>

<div class="card p-5">
    <div class="flex justify-between items-center mb-4">
        <p class="text-sm font-medium text-white">Buku Populer</p>
        <a href="<?= url('/admin/buku') ?>" class="text-xs text-violet-400 hover:underline">Lihat Semua</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($bukuPopuler as $b): ?>
            <div class="rounded-lg overflow-hidden bg-white/5">
                <div class="h-28 bg-gradient-to-br from-violet-900/40 to-pink-900/20 flex items-center justify-center">
                    <?php if (!empty($b['cover'])): ?>
                        <img src="<?= asset('uploads/covers/' . e($b['cover'])) ?>" class="h-full w-full object-cover">
                    <?php else: ?><span class="text-2xl">📖</span><?php endif; ?>
                </div>
                <div class="p-2">
                    <p class="text-xs text-white truncate"><?= e($b['judul']) ?></p>
                    <p class="text-[10px] text-slate-500"><?= (int) $b['total_pinjam'] ?>x dipinjam</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>