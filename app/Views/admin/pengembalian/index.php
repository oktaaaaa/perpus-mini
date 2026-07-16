<?php $title = 'Pengembalian Buku'; ?>

<div class="mb-5 px-4 py-4 rounded-2xl border border-fuchsia-500/30 bg-gradient-to-r from-[#1a1028] via-[#251437] to-[#120b1f] text-fuchsia-100 shadow-lg shadow-fuchsia-950/20 backdrop-blur-md">
    <div class="flex items-start gap-3">
        <div class="mt-1 h-10 w-10 shrink-0 rounded-xl bg-gradient-to-br from-fuchsia-500 to-pink-500 flex items-center justify-center shadow-md shadow-fuchsia-500/30">
            <span class="text-white text-lg">!</span>
        </div>

        <div class="text-sm leading-relaxed">
            <strong class="block text-base text-pink-200 mb-1">Sistem Denda</strong>
            <p class="text-fuchsia-100/90">
                Denda keterlambatan (<?= rupiah(DENDA_PER_HARI) ?>/hari) otomatis dicatat saat klik
                <span class="font-semibold text-pink-300">"Terima Buku"</span>.
                Untuk buku rusak atau hilang, klik tombol
                <span class="font-semibold text-violet-300">"Catat Denda"</span>
                secara manual.
            </p>
        </div>
    </div>
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
                    <button type="button"
                        onclick="catatDenda(<?= (int) $p['id'] ?>, '<?= e(addslashes($p['buku_judul'])) ?>')"
                        class="text-xs px-3 py-1.5 rounded-lg border border-amber-500/30 text-amber-400 hover:bg-amber-500/10">Catat Denda</button>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Terima Pengembalian -->
<div id="modalTerima" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 py-8 z-50 overflow-y-auto">
    <div class="w-full max-w-sm rounded-2xl border border-emerald-500/30 p-6 max-h-[85vh] overflow-y-auto"
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

<!-- Modal Catat Denda -->
<div id="modalDenda" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 py-8 z-50 overflow-y-auto">
    <div class="w-full max-w-sm rounded-2xl border border-violet-500/30 p-6 max-h-[85vh] overflow-y-auto"
        style="background: linear-gradient(180deg, #1c1233 0%, #120c22 100%); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">

        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
            style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%); box-shadow: 0 8px 24px rgba(168,85,247,0.35);">
            <i class="ti ti-alert-triangle text-2xl text-white"></i>
        </div>

        <p class="text-white font-medium text-center mb-1">Catat Denda</p>
        <p class="text-xs text-center mb-5" style="color:#b9aed6;" id="dendaJudulBuku"></p>

        <form method="POST" id="formDenda" class="space-y-3">
            <?= csrf_field() ?>

            <!-- Dropdown Jenis Denda Custom -->
            <div class="relative" id="dropdownJenisWrap">
                <button type="button" onclick="toggleDropdownJenis()"
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm text-white text-left"
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12);">
                    <span id="dropdownJenisLabel">Buku Rusak</span>
                    <i class="ti ti-chevron-down text-xs text-slate-400 transition-transform" id="dropdownJenisIcon"></i>
                </button>

                <div id="dropdownJenisList"
                    class="hidden absolute left-0 right-0 mt-2 rounded-xl overflow-hidden z-40"
                    style="background: linear-gradient(180deg, #1c1233 0%, #120c22 100%); border: 1px solid rgba(168,85,247,0.3); box-shadow: 0 20px 50px rgba(0,0,0,0.5);">

                    <button type="button" onclick="pilihJenis('rusak', 'Buku Rusak')"
                        id="opsiRusak"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-white font-medium"
                        style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);">
                        <span>Buku Rusak</span>
                        <i class="ti ti-check text-sm" id="checkRusak"></i>
                    </button>
                    <button type="button" onclick="pilihJenis('hilang', 'Buku Hilang')"
                        id="opsiHilang"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm text-slate-300 hover:bg-white/5 transition-colors">
                        <span>Buku Hilang</span>
                        <i class="ti ti-check text-sm hidden" id="checkHilang"></i>
                    </button>
                </div>
            </div>
            <input type="hidden" name="jenis" id="dendaJenisValue" value="rusak">

            <input type="number" name="jumlah" required min="1" placeholder="Jumlah denda (Rp)"
                class="w-full px-4 py-2.5 rounded-lg text-sm outline-none text-white placeholder-slate-500"
                style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12);">

            <input type="text" name="keterangan" placeholder="Keterangan (opsional)"
                class="w-full px-4 py-2.5 rounded-lg text-sm outline-none text-white placeholder-slate-500"
                style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12);">

            <div class="flex gap-2 pt-1">
                <button type="submit"
                    class="flex-1 py-2.5 rounded-lg text-white text-sm font-semibold"
                    style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%); box-shadow: 0 6px 18px rgba(168,85,247,0.35);">
                    Simpan
                </button>
                <button type="button" onclick="document.getElementById('modalDenda').classList.add('hidden')"
                    class="flex-1 py-2.5 rounded-lg border border-white/10 text-sm text-slate-300 hover:bg-white/5">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function terimaPengembalian(id, judulBuku) {
    document.getElementById('formTerima').action = '<?= url('/admin/pengembalian') ?>' + '/' + id + '/terima';
    document.getElementById('terimaJudulBuku').textContent = '"' + judulBuku + '"';
    document.getElementById('modalTerima').classList.remove('hidden');
}

function catatDenda(id, judulBuku) {
    document.getElementById('formDenda').action = '<?= url('/admin/pengembalian') ?>' + '/' + id + '/denda';
    document.getElementById('dendaJudulBuku').textContent = judulBuku;
    pilihJenis('rusak', 'Buku Rusak');
    document.getElementById('modalDenda').classList.remove('hidden');
}

function toggleDropdownJenis() {
    document.getElementById('dropdownJenisList').classList.toggle('hidden');
    document.getElementById('dropdownJenisIcon').classList.toggle('rotate-180');
}

function pilihJenis(value, label) {
    document.getElementById('dropdownJenisLabel').textContent = label;
    document.getElementById('dendaJenisValue').value = value;
    document.getElementById('dropdownJenisList').classList.add('hidden');
    document.getElementById('dropdownJenisIcon').classList.remove('rotate-180');

    const rusak = document.getElementById('opsiRusak');
    const hilang = document.getElementById('opsiHilang');
    const checkRusak = document.getElementById('checkRusak');
    const checkHilang = document.getElementById('checkHilang');
    const aktif = 'linear-gradient(135deg, #a855f7 0%, #ec4899 100%)';

    if (value === 'rusak') {
        rusak.style.background = aktif;
        rusak.className = 'w-full flex items-center justify-between px-4 py-2.5 text-sm text-white font-medium';
        hilang.style.background = '';
        hilang.className = 'w-full flex items-center justify-between px-4 py-2.5 text-sm text-slate-300 hover:bg-white/5 transition-colors';
        checkRusak.classList.remove('hidden');
        checkHilang.classList.add('hidden');
    } else {
        hilang.style.background = aktif;
        hilang.className = 'w-full flex items-center justify-between px-4 py-2.5 text-sm text-white font-medium';
        rusak.style.background = '';
        rusak.className = 'w-full flex items-center justify-between px-4 py-2.5 text-sm text-slate-300 hover:bg-white/5 transition-colors';
        checkHilang.classList.remove('hidden');
        checkRusak.classList.add('hidden');
    }
}

document.addEventListener('click', function (e) {
    const wrap = document.getElementById('dropdownJenisWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('dropdownJenisList').classList.add('hidden');
        document.getElementById('dropdownJenisIcon').classList.remove('rotate-180');
    }
});
</script>