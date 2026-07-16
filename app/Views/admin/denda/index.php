<?php $title = 'Pengembalian Buku'; ?>

<div class="mb-5 rounded-2xl border border-fuchsia-500/30 bg-gradient-to-r from-[#1a1028] via-[#241435] to-[#120b1f] px-5 py-4 text-sm text-fuchsia-100 shadow-lg shadow-fuchsia-950/20 backdrop-blur-md">
    <div class="flex items-start gap-3">
        <div class="mt-0.5 flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-500 to-pink-500 text-white shadow-md shadow-pink-500/30">
            <span class="text-base font-bold">!</span>
        </div>

        <div class="leading-relaxed">
            <strong class="block text-base text-pink-200 mb-1">Sistem Denda</strong>
            <span class="text-fuchsia-100/90">
                Denda keterlambatan (<?= rupiah(DENDA_PER_HARI) ?>/hari) otomatis dicatat saat klik
                <span class="font-semibold text-pink-300">"Terima Buku"</span>.
                Untuk buku rusak atau hilang, klik tombol
                <span class="font-semibold text-violet-300">"Catat Denda"</span>
                secara manual.
            </span>
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

<!-- Modal Terima Pengembalian (1 modal dipakai ulang utk semua baris, di luar card) -->
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
<!-- Modal Catat Denda -->
<div id="modalDenda" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 py-8 z-50 overflow-y-auto">
    <div class="w-full max-w-sm rounded-2xl border border-amber-500/30 p-6 max-h-[85vh] overflow-y-auto"
        style="background: linear-gradient(180deg, #1c1233 0%, #120c22 100%); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">

        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
            style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); box-shadow: 0 8px 24px rgba(245,158,11,0.35);">
            <i class="ti ti-alert-triangle text-2xl text-white"></i>
        </div>

        <p class="text-white font-medium text-center mb-1">Catat Denda</p>
        <p class="text-xs text-center mb-5" style="color:#b9aed6;" id="dendaJudulBuku"></p>

        <form method="POST" id="formDenda" class="space-y-3">
            <?= csrf_field() ?>

            <input type="hidden" name="jenis" id="dendaJenis" value="rusak">

            <div class="grid grid-cols-2 gap-3">
                <button type="button" id="optRusak" onclick="setDendaJenis('rusak')"
                    class="relative flex items-start gap-3 p-3 rounded-lg text-left hover:shadow-md transition-transform duration-200 transform"
                    aria-pressed="false"
                    style="background: linear-gradient(135deg,#fef3c7,#f59e0b); border:1px solid rgba(245,158,11,0.12);">
                    <div class="w-10 h-10 rounded-md flex items-center justify-center bg-white/10 text-amber-700">
                        <i class="ti ti-wrench text-xl"></i>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-amber-900">Buku Rusak</div>
                        <div class="text-xs text-amber-800/80">Kerusakan ringan sampai sedang. Isi jumlah denda sesuai kondisi.</div>
                    </div>
                    <span id="checkRusak" class="absolute top-3 right-3 hidden w-6 h-6 rounded-full bg-white/90 text-amber-700 flex items-center justify-center text-[0.85rem]">✓</span>
                </button>

                <button type="button" id="optHilang" onclick="setDendaJenis('hilang')"
                    class="relative flex items-start gap-3 p-3 rounded-lg text-left hover:shadow-md transition-transform duration-200 transform"
                    aria-pressed="false"
                    style="background: linear-gradient(135deg,#fee2e2,#ef4444); border:1px solid rgba(239,68,68,0.12);">
                    <div class="w-10 h-10 rounded-md flex items-center justify-center bg-white/10 text-rose-700">
                        <i class="ti ti-package text-xl"></i>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-rose-900">Buku Hilang</div>
                        <div class="text-xs text-rose-800/80">Buku tidak kembali/kehilangan. Rekomendasi denda lebih tinggi.</div>
                    </div>
                    <span id="checkHilang" class="absolute top-3 right-3 hidden w-6 h-6 rounded-full bg-white/90 text-rose-700 flex items-center justify-center text-[0.85rem]">✓</span>
                </button>
            </div>

            <div class="pt-2">
                <input type="number" name="jumlah" id="dendaJumlah" required min="1" placeholder="Jumlah denda (Rp)"
                    class="w-full px-4 py-2.5 rounded-lg text-sm outline-none text-white placeholder-slate-500"
                    style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12);">
                <p class="text-xs text-slate-400 mt-2">Saran: <span id="saranJumlah" class="font-medium">Rp50.000</span></p>
            </div>

            <input type="text" name="keterangan" placeholder="Keterangan (opsional)"
                class="w-full px-4 py-2.5 rounded-lg text-sm outline-none text-white placeholder-slate-500"
                style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12);">

            <div class="flex gap-2 pt-1">
                <button type="submit"
                    class="flex-1 py-2.5 rounded-lg text-white text-sm font-semibold"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); box-shadow: 0 6px 18px rgba(245,158,11,0.35);">
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
    document.getElementById('modalDenda').classList.remove('hidden');
}

function setDendaJenis(type) {
    const jenis = document.getElementById('dendaJenis');
    const rusak = document.getElementById('optRusak');
    const hilang = document.getElementById('optHilang');
    const jumlah = document.getElementById('dendaJumlah');
    const saran = document.getElementById('saranJumlah');

    jenis.value = type;
    if (type === 'rusak') {
        rusak.classList.add('ring-2','ring-amber-400','scale-105','shadow-2xl');
        rusak.setAttribute('aria-pressed','true');
        hilang.classList.remove('ring-2','ring-rose-400','scale-105','shadow-2xl');
        hilang.setAttribute('aria-pressed','false');
        document.getElementById('checkRusak').classList.remove('hidden');
        document.getElementById('checkHilang').classList.add('hidden');
        jumlah.placeholder = 'Contoh: 50000';
        saran.textContent = 'Rp50.000';
    } else {
        hilang.classList.add('ring-2','ring-rose-400','scale-105','shadow-2xl');
        hilang.setAttribute('aria-pressed','true');
        rusak.classList.remove('ring-2','ring-amber-400','scale-105','shadow-2xl');
        rusak.setAttribute('aria-pressed','false');
        document.getElementById('checkHilang').classList.remove('hidden');
        document.getElementById('checkRusak').classList.add('hidden');
        jumlah.placeholder = 'Contoh: 150000';
        saran.textContent = 'Rp150.000';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('optRusak')) setDendaJenis('rusak');
});
</script>