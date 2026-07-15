<?php $title = 'Manajemen eBook'; ?>

<div class="grid grid-cols-3 gap-5 mb-6">
    <div class="card p-5">
        <p class="text-xs text-slate-500">Total Pembelian eBook</p>
        <p class="text-xl font-bold text-white mt-1"><?= (int) $totalPembelian ?></p>
    </div>
    <div class="card p-5">
        <p class="text-xs text-slate-500">Total Pendapatan eBook</p>
        <p class="text-xl font-bold text-violet-400 mt-1"><?= rupiah($totalPendapatan) ?></p>
    </div>
    <div class="card p-5 flex flex-col justify-center">
        <p class="text-xs text-slate-500 mb-1">Atur Harga & Link eBook</p>
        <a href="<?= url('/admin/buku') ?>" class="text-sm text-violet-400 hover:underline">Edit di Halaman Buku &rarr;</a>
    </div>
</div>

<div class="card overflow-hidden">
    <p class="px-5 pt-4 text-sm font-medium text-white">Riwayat Pembelian eBook</p>
    <table class="w-full data text-sm mt-2">
        <thead>
            <tr class="border-b border-white/5">
                <th class="text-left px-5 py-3">Anggota</th>
                <th class="text-left px-5 py-3">eBook</th>
                <th class="text-left px-5 py-3">Harga</th>
                <th class="text-left px-5 py-3">Tanggal Beli</th>
                <th class="text-left px-5 py-3">Bukti Bayar</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($pembelian)): ?>
            <tr><td colspan="5" class="text-center py-10 text-slate-500">Belum ada pembelian eBook.</td></tr>
        <?php else: foreach ($pembelian as $p): ?>
            <tr class="border-b border-white/5">
                <td class="px-5 py-3 text-white"><?= e($p['anggota_nama']) ?></td>
                <td class="px-5 py-3 text-slate-400"><?= e($p['buku_judul']) ?></td>
                <td class="px-5 py-3 text-white"><?= rupiah($p['harga']) ?></td>
                <td class="px-5 py-3 text-slate-400"><?= tanggal_indo($p['created_at']) ?></td>
                <td class="px-5 py-3">
                    <?php if (!empty($p['bukti_bayar'])): ?>
                        <a href="<?= url('/admin/ebook/bukti/' . $p['id']) ?>"
   class="text-xs text-violet-400 hover:underline">
    Lihat Bukti
</a>
                    <?php else: ?>-<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
