<?php $title = 'Riwayat Saya'; ?>

<!-- Tab Switch -->
<div class="flex gap-2 mb-6 flex-wrap">
    <button id="tabBeli" onclick="gantiTab('beli')"
        class="tab-btn px-5 py-2.5 rounded-lg text-sm font-medium bg-gradient-to-r from-violet-600 to-pink-600 text-white">
        Riwayat Pembelian
    </button>
    <button id="tabBaca" onclick="gantiTab('baca')"
        class="tab-btn px-5 py-2.5 rounded-lg text-sm font-medium border border-white/10 text-slate-300 hover:bg-white/5">
        Riwayat Baca
    </button>
    <button id="tabPinjam" onclick="gantiTab('pinjam')"
        class="tab-btn px-5 py-2.5 rounded-lg text-sm font-medium border border-white/10 text-slate-300 hover:bg-white/5">
        Riwayat Peminjaman
    </button>
</div>

<!-- ==================== RIWAYAT PEMBELIAN ==================== -->
<div id="contentBeli" class="space-y-3">
    <?php if (empty($transaksi)): ?>
        <div class="card p-8 text-center text-slate-500">Belum ada riwayat pembelian.</div>
    <?php else: foreach ($transaksi as $t): ?>
        <div class="card p-5 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);">
                    <i class="ti ti-book text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-white font-medium text-sm"><?= e($t['buku_judul']) ?></p>
                    <p class="text-xs text-slate-500 mt-0.5">Dibeli <?= e($t['created_at']) ?></p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-white font-medium text-sm">Rp<?= number_format($t['harga'], 0, ',', '.') ?></p>
                    <span class="inline-block mt-1 text-xs px-2.5 py-0.5 rounded-full border text-emerald-400 border-emerald-500/30 bg-emerald-500/10">
                        Berhasil
                    </span>
                </div>

                <?php if (!empty($t['file'])): ?>
                <a href="<?= url('/ebook/' . $t['buku_id'] . '/download') ?>"
                    class="text-xs px-3 py-2 rounded-lg text-white flex items-center gap-1.5"
                    style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);">
                    <i class="ti ti-download text-sm"></i> Download
                </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<!-- ==================== RIWAYAT BACA ==================== -->
<div id="contentBaca" class="space-y-3 hidden">
    <?php if (empty($riwayat_baca)): ?>
        <div class="card p-8 text-center text-slate-500">Belum ada riwayat baca.</div>
    <?php else: foreach ($riwayat_baca as $r): ?>
        <div class="card p-5 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);">
                    <i class="ti ti-book-2 text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-white font-medium text-sm"><?= e($r['buku_judul']) ?></p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Penulis: <?= e($r['buku_penulis']) ?> · Terakhir dibaca <?= e($r['terakhir_dibaca']) ?>
                    </p>
                    <?php if (!empty($r['rating'])): ?>
                    <div class="flex items-center gap-0.5 mt-1">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="ti ti-star<?= $s <= $r['rating'] ? '-filled' : '' ?> text-xs"
                                style="color: <?= $s <= $r['rating'] ? '#f0abfc' : '#3f3a52' ?>;"></i>
                        <?php endfor; ?>
                        <?php if (!empty($r['ulasan'])): ?>
                            <span class="text-xs text-slate-500 ml-1">"<?= e($r['ulasan']) ?>"</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-32">
                    <p class="text-xs text-slate-400 mb-1"><?= (int) $r['progress'] ?>% selesai</p>
                    <div class="w-full h-1.5 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full"
                            style="width: <?= (int) $r['progress'] ?>%; background: linear-gradient(90deg, #a855f7, #ec4899);">
                        </div>
                    </div>
                </div>
                <a href="<?= url('/baca/' . $r['buku_id']) ?>"
                    class="text-xs px-3 py-2 rounded-lg text-white flex items-center gap-1.5 flex-shrink-0"
                    style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);">
                    <i class="ti ti-player-play text-sm"></i> Lanjut baca
                </a>
            </div>
        </div>
    <?php endforeach; endif; ?>
</div>

<!-- ==================== RIWAYAT PEMINJAMAN ==================== -->
<div id="contentPinjam" class="space-y-3 hidden">
    <?php if (empty($riwayat_pinjam)): ?>
        <div class="card p-8 text-center text-slate-500">Belum ada riwayat peminjaman.</div>
    <?php else: foreach ($riwayat_pinjam as $p):
        $isTerlambat = $p['status'] === 'dipinjam'
            && !empty($p['tanggal_jatuh_tempo'])
            && $p['tanggal_jatuh_tempo'] < date('Y-m-d');

        $statusPinjam = match (true) {
            $isTerlambat                    => ['Terlambat', 'text-rose-400 border-rose-500/30 bg-rose-500/10'],
            $p['status'] === 'menunggu'     => ['Menunggu persetujuan', 'text-amber-400 border-amber-500/30 bg-amber-500/10'],
            $p['status'] === 'dipinjam'     => ['Sedang dipinjam', 'text-blue-400 border-blue-500/30 bg-blue-500/10'],
            $p['status'] === 'dikembalikan' => ['Dikembalikan', 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10'],
            $p['status'] === 'ditolak'      => ['Ditolak', 'text-slate-400 border-white/10 bg-white/5'],
            default                         => ['-', 'text-slate-400 border-white/10 bg-white/5'],
        };
    ?>
        <div class="card p-5 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg, #7f77dd 0%, #a855f7 100%);">
                    <i class="ti ti-clock-hour-4 text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-white font-medium text-sm"><?= e($p['buku_judul']) ?></p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <?php if (!empty($p['tanggal_pinjam'])): ?>
                            Dipinjam <?= e($p['tanggal_pinjam']) ?> · Batas kembali <?= e($p['tanggal_jatuh_tempo']) ?>
                        <?php else: ?>
                            Diajukan <?= e($p['created_at']) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($p['tanggal_kembali'])): ?>
                        <p class="text-xs text-slate-500 mt-0.5">Dikembalikan <?= e($p['tanggal_kembali']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <span class="text-xs px-2.5 py-1 rounded-full border flex-shrink-0 <?= $statusPinjam[1] ?>">
                <?= $statusPinjam[0] ?>
            </span>
        </div>
    <?php endforeach; endif; ?>
</div>

<script>
function gantiTab(tab) {
    document.getElementById('contentBeli').classList.toggle('hidden', tab !== 'beli');
    document.getElementById('contentBaca').classList.toggle('hidden', tab !== 'baca');
    document.getElementById('contentPinjam').classList.toggle('hidden', tab !== 'pinjam');

    const aktif = 'px-5 py-2.5 rounded-lg text-sm font-medium bg-gradient-to-r from-violet-600 to-pink-600 text-white';
    const nonaktif = 'px-5 py-2.5 rounded-lg text-sm font-medium border border-white/10 text-slate-300 hover:bg-white/5';

    document.getElementById('tabBeli').className   = 'tab-btn ' + (tab === 'beli'   ? aktif : nonaktif);
    document.getElementById('tabBaca').className   = 'tab-btn ' + (tab === 'baca'   ? aktif : nonaktif);
    document.getElementById('tabPinjam').className = 'tab-btn ' + (tab === 'pinjam' ? aktif : nonaktif);
}
</script>