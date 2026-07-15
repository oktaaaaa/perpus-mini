<?php $title = 'Bukti Pembayaran'; ?>

<div class="max-w-6xl mx-auto">

    <div class="mb-6">

        <a href="<?= url('/admin/ebook') ?>"
           class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-violet-600 hover:bg-violet-700 text-white">

            ← Kembali

        </a>

    </div>

    <div class="card p-6">

        <img
            src="<?= asset('uploads/bukti/' . e($data['bukti_bayar'])) ?>"
            class="w-full rounded-lg shadow-lg">

    </div>

</div>