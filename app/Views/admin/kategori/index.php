<?php $title = 'Kategori Buku'; ?>

<div class="flex justify-end mb-5">
    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
        class="px-5 py-2.5 rounded-lg btn-primary text-white text-sm font-medium">+ Kategori Baru</button>
</div>

<div class="card overflow-hidden">
    <table class="w-full data text-sm">
        <thead>
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">   
            <tr class="border-b border-white/5">
                <th class="text-left px-5 py-3">No</th>
                <th class="text-left px-5 py-3">Nama Kategori</th>
                <th class="text-left px-5 py-3">Slug URL</th>
                <th class="text-right px-5 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($kategori)): ?>
            <tr><td colspan="4" class="text-center py-8 text-slate-500">Belum ada kategori.</td></tr>
        <?php else: foreach ($kategori as $i => $k): ?>
            <tr class="border-b border-white/5">
                <td class="px-5 py-3 text-slate-400"><?= $i + 1 ?></td>
                <td class="px-5 py-3 text-white font-medium"><?= e($k['nama']) ?></td>
                <td class="px-5 py-3 text-slate-500"><?= e($k['slug']) ?></td>
                <td class="px-5 py-3 text-right space-x-2">
                    <button onclick="editKategori(<?= (int) $k['id'] ?>, '<?= e(addslashes($k['nama'])) ?>')"
                        class="text-xs px-3 py-1.5 rounded-lg border border-white/10 hover:bg-white/5">Edit</button>
                    <button type="button"
                        onclick="hapusKategori(<?= (int) $k['id'] ?>, '<?= e(addslashes($k['nama'])) ?>')"
                        class="text-xs px-3 py-1.5 rounded-lg border border-rose-500/30 text-rose-400 hover:bg-rose-500/10">Hapus</button>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
    <div class="card p-6 w-full max-w-sm">
        <p class="text-white font-medium mb-4">Tambah Kategori</p>
        <form method="POST" action="<?= url('/admin/kategori') ?>">
            <?= csrf_field() ?>
            <input type="text" name="nama" required placeholder="Nama kategori"
                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            <div class="flex gap-2 mt-4">
                <button class="flex-1 py-2 rounded-lg btn-primary text-white text-sm">Simpan</button>
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                    class="flex-1 py-2 rounded-lg border border-white/10 text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEdit" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
    <div class="card p-6 w-full max-w-sm">
        <p class="text-white font-medium mb-4">Edit Kategori</p>
        <form method="POST" id="formEdit">
            <?= csrf_field() ?>
            <input type="text" name="nama" id="editNama" required
                class="w-full px-4 py-2.5 rounded-lg bg-white/5 border border-white/10 text-sm outline-none focus:border-violet-500">
            <div class="flex gap-2 mt-4">
                <button class="flex-1 py-2 rounded-lg btn-primary text-white text-sm">Simpan</button>
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')"
                    class="flex-1 py-2 rounded-lg border border-white/10 text-sm">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus -->
<div id="modalHapus" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
    <div class="w-full max-w-sm rounded-2xl border border-violet-500/30 p-6"
        style="background: linear-gradient(180deg, #1c1233 0%, #120c22 100%); box-shadow: 0 20px 60px rgba(0,0,0,0.5);">

        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"
            style="background: linear-gradient(135deg, #f43f5e 0%, #ec4899 100%); box-shadow: 0 8px 24px rgba(236,72,153,0.35);">
            <i class="ti ti-trash text-2xl text-white"></i>
        </div>

        <p class="text-white font-medium text-center mb-2">Hapus kategori ini?</p>
        <p class="text-sm text-center leading-relaxed mb-5" style="color:#b9aed6;">
            Kategori <span id="hapusNamaKategori" class="font-semibold" style="color:#f0abfc;"></span>
            akan dihapus permanen dan tidak dapat dikembalikan.
        </p>

        <div class="flex items-start gap-2 rounded-lg px-3 py-2.5 mb-5"
            style="background: rgba(244,63,94,0.08); border: 1px solid rgba(244,63,94,0.25);">
            <i class="ti ti-alert-triangle text-sm mt-0.5" style="color:#fb7185;"></i>
            <span class="text-xs leading-relaxed" style="color:#fca5a5;">
                Buku yang terkait dengan kategori ini mungkin akan kehilangan kategorinya.
            </span>
        </div>

        <div class="flex gap-2">
            <button type="button" onclick="document.getElementById('modalHapus').classList.add('hidden')"
                class="flex-1 py-2.5 rounded-lg border border-white/10 text-sm text-slate-300 hover:bg-white/5">Batal</button>
            <form method="POST" id="formHapus" class="flex-1">
                <?= csrf_field() ?>
                <button class="w-full py-2.5 rounded-lg text-white text-sm font-semibold flex items-center justify-center gap-1.5"
                    style="background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%); box-shadow: 0 6px 18px rgba(236,72,153,0.35);">
                    <i class="ti ti-trash text-sm"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function editKategori(id, nama) {
    document.getElementById('formEdit').action = '<?= url('/admin/kategori') ?>' + '/' + id;
    document.getElementById('editNama').value = nama;
    document.getElementById('modalEdit').classList.remove('hidden');
}

function hapusKategori(id, nama) {
    document.getElementById('formHapus').action = '<?= url('/admin/kategori') ?>' + '/' + id + '/hapus';
    document.getElementById('hapusNamaKategori').textContent = '"' + nama + '"';
    document.getElementById('modalHapus').classList.remove('hidden');
}
</script>