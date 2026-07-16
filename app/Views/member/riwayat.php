<?public function riwayat(): void

    Auth::requireMember();
    var_dump(Auth::id()); exit; // debug sementara
    ...
<?php $title = 'riwayat sayaa'; ?>

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
                <div class="w-14 h-16 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center"
                    style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);">
                    <?php if (!empty($t['buku_cover'])): ?>
                        <img src="<?= asset('uploads/covers/' . e($t['buku_cover'])) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="ti ti-book text-white text-xl"></i>
                    <?php endif; ?>
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
                <div class="w-14 h-16 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center"
                    style="background: linear-gradient(135deg, #ec4899 0%, #f43f5e 100%);">
                    <?php if (!empty($r['buku_cover'])): ?>
                        <img src="<?= asset('uploads/covers/' . e($r['buku_cover'])) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="ti ti-book-2 text-white text-xl"></i>
                    <?php endif; ?>
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
<div id="contentPinjam" class="space-y-4 hidden">

    <?php if (empty($riwayat_pinjam)): ?>

        <div class="card p-8 text-center text-slate-500">
            Belum ada riwayat peminjaman.
        </div>

    <?php else: ?>

        <?php foreach ($riwayat_pinjam as $p):

            $isTerlambat = $p['status'] === 'dipinjam'
                && !empty($p['tanggal_jatuh_tempo'])
                && $p['tanggal_jatuh_tempo'] < date('Y-m-d');

            $statusPinjam = match (true) {
                $isTerlambat                    => ['Terlambat', 'text-rose-400 border-rose-500/30 bg-rose-500/10'],
                $p['status'] === 'menunggu'     => ['Menunggu Persetujuan', 'text-amber-400 border-amber-500/30 bg-amber-500/10'],
                $p['status'] === 'dipinjam'     => ['Sedang Dipinjam', 'text-blue-400 border-blue-500/30 bg-blue-500/10'],
                $p['status'] === 'dikembalikan' => ['Dikembalikan', 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10'],
                $p['status'] === 'ditolak'      => ['Ditolak', 'text-slate-400 border-white/10 bg-white/5'],
                default                         => ['-', 'text-slate-400 border-white/10 bg-white/5'],
            };

        ?>

        <div class="card p-5 flex items-center justify-between gap-5 flex-wrap">

            <!-- Cover Buku -->
            <div class="flex items-center gap-4">

                <div class="w-20 h-28 rounded-xl overflow-hidden border border-slate-700 bg-slate-800 shadow-lg flex-shrink-0">

                    <?php if (!empty($p['buku_cover'])): ?>

                        <img
                            src="<?= asset('uploads/covers/' . e($p['buku_cover'])) ?>"
                            alt="<?= e($p['buku_judul']) ?>"
                            class="w-full h-full object-cover">

                    <?php else: ?>

                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-violet-500 to-fuchsia-600 text-4xl">
                            📚
                        </div>

                    <?php endif; ?>

                </div>

                <!-- Informasi Buku -->
                <div>

                    <h3 class="text-white font-semibold text-base">
                        <?= e($p['buku_judul']) ?>
                    </h3>

                    <?php if (!empty($p['tanggal_pinjam'])): ?>

                        <p class="text-sm text-slate-400 mt-1">
                            Dipinjam :
                            <?= tanggal_indo($p['tanggal_pinjam']) ?>
                        </p>

                        <p class="text-sm text-slate-400">
                            Batas Kembali :
                            <?= tanggal_indo($p['tanggal_jatuh_tempo']) ?>
                        </p>

                    <?php else: ?>

                        <p class="text-sm text-slate-400">
                            Diajukan :
                            <?= tanggal_indo($p['created_at']) ?>
                        </p>

                    <?php endif; ?>

                    <?php if (!empty($p['tanggal_kembali'])): ?>

                        <p class="text-sm text-emerald-400 mt-1">
                            Dikembalikan :
                            <?= tanggal_indo($p['tanggal_kembali']) ?>
                        </p>

                    <?php endif; ?>

                </div>

            </div>

            <!-- Status -->
            <div class="flex flex-col items-end gap-2">

                <span class="text-xs px-3 py-1 rounded-full border <?= $statusPinjam[1] ?>">
                    <?= $statusPinjam[0] ?>
                </span>

                <?php if ($p['status'] === 'dipinjam' && empty($p['diajukan_kembali'])): ?>

                  <form method="POST"
    action="<?= url('/riwayat/' . $p['id'] . '/ajukan-kembali') ?>"
    class="confirmForm inline">
                        <button
                            class="px-4 py-2 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-sm transition">
                            Ajukan Pengembalian
                        </button>

                    </form>

                <?php elseif (!empty($p['diajukan_kembali']) && $p['status'] === 'dipinjam'): ?>

                    <span class="text-xs px-3 py-1 rounded-full border border-amber-500/30 bg-amber-500/10 text-amber-400">
                        Menunggu Verifikasi
                    </span>

                <?php endif; ?>

            </div>

        </div>

        <?php endforeach; ?>

    <?php endif; ?>

</div>
<div id="confirmModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm hidden items-center justify-center z-50">

    <div class="w-[420px] rounded-3xl bg-[#171326] border border-violet-600/30 shadow-2xl p-8">

        <div class="text-center">

            <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-violet-600/20 text-3xl">
                📚
            </div>

            <h2 class="text-white text-2xl font-bold">
                Konfirmasi
            </h2>

            <p class="text-slate-400 mt-3">
                Apakah Anda yakin ingin mengajukan pengembalian buku ini?
            </p>

            <div class="mt-8 flex justify-center gap-4">

                <button type="button" id="cancelBtn"
                    class="px-6 py-3 rounded-xl bg-slate-700 hover:bg-slate-600 text-white">

                    Batal

                </button>

                <button type="button" id="confirmBtn"
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white font-semibold">

                    Ya, Kembalikan

                </button>

            </div>

        </div>

    </div>

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
<script>

let currentForm = null;

const modal = document.getElementById('confirmModal');

document.querySelectorAll('.confirmForm').forEach(form => {

    form.addEventListener('submit', function(e){

        e.preventDefault();

        currentForm = this;

        modal.classList.remove('hidden');

        modal.classList.add('flex');

    });

});

document.getElementById('cancelBtn').onclick = function(){

    modal.classList.remove('flex');

    modal.classList.add('hidden');

}

document.getElementById('confirmBtn').onclick = function(){

    currentForm.submit();

}

</script>