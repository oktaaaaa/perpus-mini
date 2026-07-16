<?php $title = 'Baca — ' . $buku['judul']; ?>

<div class="card p-6 mb-5 flex items-center gap-4">
    <div class="w-16 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-violet-900/40 to-pink-900/20">
        <?php if (!empty($buku['cover'])): ?>
            <img src="<?= asset('uploads/covers/' . e($buku['cover'])) ?>" class="w-full h-full object-cover">
        <?php endif; ?>
    </div>
    <div>
        <h2 class="text-white font-semibold text-lg"><?= e($buku['judul']) ?></h2>
        <p class="text-slate-400 text-sm"><?= e($buku['penulis']) ?></p>
    </div>
</div>

<div class="card p-6">
    <?php if (!empty($buku['ebook_file'])): ?>
        <iframe src="<?= asset('uploads/ebooks/' . e($buku['ebook_file'])) ?>"
            class="w-full rounded-lg border border-white/10" style="height: 75vh;"
            onload="mulaiTrackingProgress()"></iframe>
    <?php else: ?>
        <p class="text-slate-500 text-sm text-center py-10">File eBook belum tersedia.</p>
    <?php endif; ?>
</div>

<script>
function mulaiTrackingProgress() {
    // Simpan progress 100% begitu buku dibuka & dibaca (versi sederhana)
    // Bisa dikembangkan lagi nanti misal dihitung dari scroll position PDF viewer
    setTimeout(() => simpanProgress(100), 5000); // contoh: setelah 5 detik baca, tandai 100%
}

function simpanProgress(progress) {
    fetch('<?= url('/baca/' . $buku['id'] . '/progress') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'progress=' + progress + '&csrf_token=<?= csrf_token() ?>'
    });
}
</script>