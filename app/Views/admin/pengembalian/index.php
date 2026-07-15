<?php $title = 'Pengembalian Buku'; ?>

<div class="mb-5 px-4 py-3 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-300 text-sm">
    <strong>Sistem Denda:</strong> Denda keterlambatan (<?= rupiah(DENDA_PER_HARI) ?>/hari) otomatis dicatat saat klik "Terima Buku".
    Untuk buku rusak atau hilang, klik tombol "Catat Denda" secara manual.
</div>

<div class="card overflow-hidden">
    <table class="w-full data text-sm">
        <thead>
            <tr class="border-b border-white/5">
                <th class="text-left px-5 py-3">Peminjam</th>
                <th class="text-left px-5 py-3">Buku</th>
                <th class="text-left px-5 py-3">Tanggal</th>
                <th class="text-left px-5 py-3">Status</th>
                <th class="text-right px-5 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($dipinjam)): ?>
            <tr><td colspan="5" class="text-center py-10 text-slate-500">Belum ada data pengembalian.</td></tr>
        <?php else: foreach ($dipinjam as $p):
            $telat = $p['tanggal_jatuh_tempo'] < date('Y-m-d');
        ?>
            <tr class="border-b border-white/5">
                <td class="px-5 py-3 text-white"><?= e($p['anggota_nama']) ?></td>
                <td class="px-5 py-3 text-slate-400"><?= e($p['buku_judul']) ?></td>
                <td class="px-5 py-3 text-slate-400">
                    Pinjam: <?= tanggal_indo($p['tanggal_pinjam']) ?><br>
                    Jatuh tempo: <?= tanggal_indo($p['tanggal_jatuh_tempo']) ?>
                </td>
                <td class="px-5 py-3">
                    <?php if ($telat): ?>
                        <span class="text-xs px-2 py-1 rounded bg-rose-500/20 text-rose-400">Terlambat</span>
                    <?php else: ?>
                        <span class="text-xs px-2 py-1 rounded bg-emerald-500/20 text-emerald-400">Tepat Waktu</span>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                    <button type="button"
                        onclick="terimaPengembalian(<?= (int) $p['id'] ?>, '<?= e(addslashes($p['buku_judul'])) ?>')"
                        class="text-xs px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 hover:bg-emerald-500/20">Terima Buku</button>
                    <button onclick="document.getElementById('modalDenda<?= $p['id'] ?>').classList.remove('hidden')"
                        class="text-xs px-3 py-1.5 rounded-lg border border-amber-500/30 text-amber-400 hover:bg-amber-500/10">Catat Denda</button>
                </td>
            </tr>

            <div id="modalDenda<?= $p['id'] ?>" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
                <div class="card p-6 w-full max-w-sm">
                    <p class="text-white font-medium mb-4">Catat Denda &mdash; <?= e($p['buku_judul']) ?></p>
                    <form method="POST" action="<?= url('/admin/pengembalian/' . $p['id'] . '/denda') ?>" class="space-y-3">
                        <?= csrf_field() ?>
                        <select name="jenis" class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none">
                            <option value="rusak">Buku Rusak</option>
                            <option value="hilang">Buku Hilang</option>
                        </select>
                        <input type="number" name="jumlah" required min="1" placeholder="Jumlah denda (Rp)"
                            class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none">
                        <input type="text" name="keterangan" placeholder="Keterangan (opsional)"
                            class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none">
                        <div class="flex gap-2 pt-1">
                            <button class="flex-1 py-2 rounded-lg btn-primary text-white text-sm">Simpan</button>
                            <button type="button" onclick="document.getElementById('modalDenda<?= $p['id'] ?>').classList.add('hidden')"
                                class="flex-1 py-2 rounded-lg border border-white/10 text-sm">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Terima Pengembalian (satu modal dipakai ulang utk semua baris) -->
<div id="modalTerima" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
    <div class="w-full max-w-sm rounded-2xl border border-emerald-500/30 p-6"
        style="background: linear-gradient(180deg, #1c1233 0%, #120c22 100%); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">

        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
            style="background: linear-gradient(135deg, #22c55e 0%, #14b8a6 100%); box-shadow: 0 8px 24px rgba(34,197,94,0.35);">
            <i class="ti ti-book-2 text-2xl text-white"></i>
        </div>

        <p class="text-white font-medium text-center mb-2">Terima pengembalian buku ini?</p>
        <p class="text-sm text-center leading-relaxed mb-5" style="color:#b9aed6;">
            Buku <span id="terimaJudulBuku" class="font-semibold" style="color:#5eead4;"></span>
            akan ditandai sudah dikembalikan. Jika terlambat, denda akan otomatis dihitung.
        </p>

        <div class="flex gap-2">
            <button type="button" onclick="document.getElementById('modalTerima').classList.add('hidden')"
                class="flex-1 py-2.5 rounded-lg border border-white/10 text-sm text-slate-300 hover:bg-white/5">Batal</button>
            <form method="POST" id="formTerima" class="flex-1">
                <?= csrf_field() ?>
                <button class="w-full py-2.5 rounded-lg text-white text-sm font-semibold flex items-center justify-center gap-1.5"
                    style="background: linear-gradient(135deg, #22c55e 0%, #14b8a6 100%); box-shadow: 0 6px 18px rgba(34,197,94,0.35);">
                    <i class="ti ti-check text-sm"></i> Ya, Terima
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function terimaPengembalian(id, judulBuku) {
    document.getElementById('formTerima').action = '<?= url('/admin/pengembalian') ?>' + '/' + id + '/terima';
    document.getElementById('terimaJudulBuku').textContent = '"' + judulBuku + '"';
    document.getElementById('modalTerima').classList.remove('hidden');
}
</script>