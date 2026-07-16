<?php
$title = 'Katalog Buku';
use App\Core\Auth;
?>
<div class="mb-5 px-4 py-3 rounded-lg bg-violet-500/10 border border-violet-500/30 text-violet-300 text-sm">
    Anda sedang meminjam <strong><?= (int) $sedangDipinjam ?></strong> buku aktif saat ini.
</div>

<form method="GET" id="formFilterKatalog" class="flex flex-col md:flex-row gap-3 mb-6">
    <input type="text" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari judul / penulis..."
        class="flex-1 px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">

    <!-- Dropdown Kategori Custom -->
    <div class="relative w-full md:w-56" id="dropdownKategoriWrap">
        <button type="button" onclick="toggleDropdownKategori()"
            id="dropdownKategoriBtn"
            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm text-white text-left"
            style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.12);">
            <span id="dropdownKategoriLabel">
                <?php
                    $labelAwal = 'Semua Kategori';
                    foreach ($kategori as $k) {
                        if ((string) $k['id'] === (string) ($kategoriId ?? '')) {
                            $labelAwal = $k['nama'];
                            break;
                        }
                    }
                    echo e($labelAwal);
                ?>
            </span>
            <i class="ti ti-chevron-down text-xs text-slate-400 transition-transform" id="dropdownKategoriIcon"></i>
        </button>

        <div id="dropdownKategoriList"
            class="hidden absolute left-0 right-0 mt-2 rounded-xl overflow-hidden z-40 max-h-72 overflow-y-auto"
            style="background: linear-gradient(180deg, #1c1233 0%, #120c22 100%); border: 1px solid rgba(168,85,247,0.3); box-shadow: 0 20px 50px rgba(0,0,0,0.5);">

            <button type="button" onclick="pilihKategori(this, '', 'Semua Kategori')"
                class="kategori-opsi w-full text-left px-4 py-2.5 text-sm transition-colors <?= empty($kategoriId) ? 'text-white font-medium' : 'text-slate-300 hover:bg-white/5' ?>"
                <?= empty($kategoriId) ? 'style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);"' : '' ?>>
                Semua Kategori
            </button>

            <?php foreach ($kategori as $k): $aktif = (string) $k['id'] === (string) ($kategoriId ?? ''); ?>
            <button type="button" onclick="pilihKategori(this, '<?= (int) $k['id'] ?>', '<?= e(addslashes($k['nama'])) ?>')"
                class="kategori-opsi w-full text-left px-4 py-2.5 text-sm transition-colors <?= $aktif ? 'text-white font-medium' : 'text-slate-300 hover:bg-white/5' ?>"
                <?= $aktif ? 'style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);"' : '' ?>>
                <?= e($k['nama']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <input type="hidden" name="kategori_id" id="dropdownKategoriValue" value="<?= e($kategoriId ?? '') ?>">

    <button class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm">Cari</button>
</form>

<?php if (empty($buku)): ?>
    <div class="card p-10 text-center text-sm text-slate-500">Tidak ada buku yang cocok dengan pencarian.</div>
<?php else: ?>
<div class="grid grid-cols-2 md:grid-cols-4 gap-5">
    <?php foreach ($buku as $b): ?>
        <div class="card overflow-hidden flex flex-col">
            <div class="h-40 bg-gradient-to-br from-violet-900/40 to-pink-900/20 flex items-center justify-center">
                <?php if (!empty($b['cover'])): ?>
                    <img src="<?= asset('uploads/covers/' . e($b['cover'])) ?>" class="h-full w-full object-cover">
                <?php else: ?><span class="text-3xl">📖</span><?php endif; ?>
            </div>
            <div class="p-3 flex-1 flex flex-col">
                <span class="text-[10px] px-2 py-0.5 rounded bg-violet-500/20 text-violet-300 w-fit"><?= e($b['kategori_nama'] ?? 'Umum') ?></span>
                <a href="<?= url('/katalog/' . $b['id']) ?>" class="text-sm font-medium text-white mt-1.5 truncate hover:text-violet-300">
                    <?= e($b['judul']) ?>
                </a>
                <p class="text-xs text-slate-500 truncate"><?= e($b['penulis']) ?></p>
                <p class="text-xs text-slate-500 mt-1">Stok: <?= (int) $b['stok'] ?></p>

                <form method="POST" action="<?= url('/pinjam/' . $b['id']) ?>" class="mt-3">
                    <?= csrf_field() ?>
                    <button type="submit" <?= $b['stok'] < 1 ? 'disabled' : '' ?>
                        class="w-full text-xs py-2 rounded-lg <?= $b['stok'] < 1 ? 'bg-white/5 text-slate-600 cursor-not-allowed' : 'btn-primary text-white' ?>">
                        <?= $b['stok'] < 1 ? 'Stok Habis' : 'Pinjam Buku' ?>
                    </button>
                </form>
                <?php if ($b['ebook_price'] > 0): ?>
                    <a href="<?= url('/ebook') ?>" class="mt-2 block text-center text-xs py-2 rounded-lg border border-white/10 hover:bg-white/5">
                        eBook — <?= rupiah($b['ebook_price']) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
function toggleDropdownKategori() {
    const list = document.getElementById('dropdownKategoriList');
    const icon = document.getElementById('dropdownKategoriIcon');
    list.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

function pilihKategori(el, id, nama) {
    document.getElementById('dropdownKategoriLabel').textContent = nama;
    document.getElementById('dropdownKategoriValue').value = id;

    // Auto-submit, sama seperti onchange="this.form.submit()" di select aslinya
    document.getElementById('formFilterKatalog').submit();
}

// Tutup dropdown kalau klik di luar area
document.addEventListener('click', function (e) {
    const wrap = document.getElementById('dropdownKategoriWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('dropdownKategoriList').classList.add('hidden');
        document.getElementById('dropdownKategoriIcon').classList.remove('rotate-180');
    }
});
</script>