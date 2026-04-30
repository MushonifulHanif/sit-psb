<?php ob_start(); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Arsip & Reset Tahun Ajaran</h1>
    <p class="text-gray-600 mt-1">Lakukan reset data pendaftar untuk tahun ajaran baru dan lihat kembali data lama.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-2 space-y-6">
        <!-- List Arsip -->
        <h3 class="text-xl font-bold text-gray-800 border-b pb-2">Riwayat Arsip Tahun Ajaran Tersimpan</h3>
        
        <?php foreach ($arsip as $row): ?>
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="bg-gray-50 border-b px-6 py-3 flex justify-between items-center">
                <h4 class="font-bold text-gray-900">Tahun Ajaran: <?= htmlspecialchars($row['tahun_ajaran']) ?></h4>
                <span class="text-xs text-gray-500">Diarsipkan: <?= date('d M Y H:i', strtotime($row['created_at'])) ?></span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                    <div class="bg-blue-50 p-3 rounded">
                        <p class="text-xs text-blue-800 font-bold uppercase">Pendaftar</p>
                        <p class="text-xl font-black text-blue-600"><?= $row['total_pendaftar'] ?></p>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded">
                        <p class="text-xs text-emerald-800 font-bold uppercase">Diterima</p>
                        <p class="text-xl font-black text-emerald-600"><?= $row['total_lulus'] ?></p>
                    </div>
                    <div class="bg-red-50 p-3 rounded">
                        <p class="text-xs text-red-800 font-bold uppercase">Gagal</p>
                        <p class="text-xl font-black text-red-600"><?= $row['total_gagal'] ?></p>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded border border-indigo-100">
                        <p class="text-xs text-indigo-800 font-bold uppercase">Daftar Ulang</p>
                        <p class="text-xl font-black text-indigo-600"><?= $row['total_daftar_ulang'] ?></p>
                    </div>
                </div>
                
                <div class="text-sm">
                    <p class="text-gray-600">Total Pemasukan: <span class="font-bold text-emerald-600">Rp <?= number_format($row['total_pemasukan_keseluruhan'], 0, ',', '.') ?></span></p>
                    <p class="text-gray-500 text-xs mt-1">(Reg: Rp <?= number_format($row['total_pemasukan_registrasi'], 0, ',', '.') ?> | DU: Rp <?= number_format($row['total_pemasukan_daftar_ulang'], 0, ',', '.') ?> | Infaq: Rp <?= number_format($row['total_pemasukan_infaq'], 0, ',', '.') ?>)</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($arsip)): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-6 rounded-lg text-center">
                Belum ada arsip tahun ajaran yang tersimpan.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="md:col-span-1">
        <!-- Area Berbahaya: Reset -->
        <div class="bg-red-50 border border-red-200 rounded-lg shadow-sm overflow-hidden sticky top-8">
            <div class="bg-red-600 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    ZONA BERBAHAYA
                </h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-red-800 mb-4 font-medium">Fitur ini digunakan saat pergantian tahun ajaran baru.</p>
                <ul class="text-sm text-red-700 list-disc pl-5 mb-6 space-y-1">
                    <li>Sistem akan menyalin rekap & data json ke tabel arsip.</li>
                    <li>Sistem akan memformat tabel: Biodata, Users (Santri saja), Pembayaran, Seragam, Ujian CBT, Ujian Lisan, Gelombang.</li>
                    <li>Akun Admin, Panitia, Bank Soal, Item Seragam dan Pengaturan akan dibiarkan UTUH.</li>
                    <li>Data santri yg dihapus bersifat permanen. Tida bisa dikembalikan.</li>
                </ul>
                
                <form action="<?= url('admin/proses-reset') ?>" method="POST" onsubmit="return confirmSubmit(this, 'Reset Tahun Ajaran?', 'Data santri akan dihapus permanen dan diarsipkan. Lanjutkan?')">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    
                    <div class="mb-4 text-left">
                        <label class="block text-sm font-bold text-red-900 mb-1">Mulai Tahun Ajaran Baru:</label>
                        <input type="text" name="tahun_ajaran_baru" required placeholder="Misal: 2026/2027" class="w-full border-red-300 rounded focus:ring-red-500 focus:border-red-500 px-3 py-2 border shadow-sm">
                        <p class="text-xs text-red-600 mt-1">Tahun ajaran sekarang: <b><?= htmlspecialchars(get_pengaturan('tahun_ajaran')) ?></b></p>
                    </div>
                    
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 text-white font-bold py-3 px-4 rounded shadow-lg transition">
                        Reset & Mulai Tahun Baru
                    </button>
                    <!-- Force confirm logic by checkbox could be added to slow down user -->
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Arsip & Reset Tahun Ajaran - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
