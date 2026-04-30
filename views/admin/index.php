<?php ob_start(); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Administrator</h1>
    <p class="text-gray-600 mt-1">Ringkasan statistik penerimaan santri baru tahun ajaran <?= htmlspecialchars(get_pengaturan('tahun_ajaran')) ?>.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat Pendaftar (Primary Dark Style) -->
    <div class="bg-[#1f5f44] text-white rounded-[1.5rem] p-6 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 text-white text-opacity-10 opacity-10 scale-150 transform">
            <svg class="h-24 w-24" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <p class="text-sm font-medium text-emerald-100 uppercase tracking-wider">Total Pendaftar</p>
            <div class="p-1 rounded-full border border-emerald-400 bg-emerald-500 bg-opacity-20 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div class="relative z-10">
            <p class="text-4xl font-bold"><?= number_format($pendaftar_count) ?></p>
            <p class="text-xs text-emerald-200 mt-2">Seluruh pendaftar di DB</p>
        </div>
    </div>
    
    <!-- Stat Lulus (Light Style) -->
    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 relative group">
        <div class="flex justify-between items-start mb-4">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Santri Diterima</p>
            <div class="p-1 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div>
            <p class="text-4xl font-bold text-gray-900"><?= number_format($lulus_count) ?></p>
            <p class="text-xs text-emerald-500 mt-2 flex items-center"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Lulus Tes</p>
        </div>
    </div>

    <!-- Stat Reg (Light Style) -->
    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 relative group">
        <div class="flex justify-between items-start mb-4">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Kas Pendaftaran</p>
            <div class="p-1 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 mt-3"><?= format_rupiah($total_reg) ?></p>
            <p class="text-xs text-emerald-600 mt-2 font-bold bg-emerald-50 inline-block px-2 py-1 rounded-full">+ Valid</p>
        </div>
    </div>

    <!-- Stat DU (Light Style) -->
    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 relative group">
        <div class="flex justify-between items-start mb-4">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Kas Daftar Ulang</p>
            <div class="p-1 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 mt-3"><?= format_rupiah($total_du) ?></p>
            <p class="text-xs text-blue-600 mt-2 font-bold bg-blue-50 inline-block px-2 py-1 rounded-full">+ Lunas Semua</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow border border-gray-200 p-8 text-center max-w-2xl mx-auto mt-12 group hover:shadow-lg transition">
    <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
        <span class="text-4xl text-emerald-600">⚙️</span>
    </div>
    <h2 class="text-xl font-bold text-gray-800 mb-2">Panel Administrator Utama</h2>
    <p class="text-gray-600 mb-6">Kelola seluruh konfigurasi sistem, konten landing page, bank soal, jadwal ujian, hingga manajemen tahun ajaran dan user panitia melalui sidebar di sebelah kiri.</p>
</div>

<?php
$content = ob_get_clean();
$title = "Admin Dashboard - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
