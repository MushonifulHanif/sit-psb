<?php ob_start(); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Sekretaris</h1>
    <p class="text-gray-600 mt-1">Ringkasan pendaftaran santri baru.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card Total Pendaftar (Primary Dark Style) -->
    <div class="bg-[#1f5f44] text-white rounded-[1.5rem] p-6 shadow-sm relative overflow-hidden group">
        <!-- Decoration graphic -->
        <div class="absolute -right-6 -top-6 text-white text-opacity-10 opacity-10 scale-150 transform">
            <svg class="h-24 w-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
        </div>
        <div class="flex justify-between items-start mb-4 relative z-10">
            <p class="text-sm font-medium text-emerald-100">Total Pendaftar</p>
            <div class="p-1 rounded-full border border-emerald-400 bg-emerald-500 bg-opacity-20 flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div class="relative z-10">
            <p class="text-4xl font-bold"><?= $total_pendaftar ?></p>
            <p class="text-xs text-emerald-200 mt-2 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> Total seluruh akun santri
            </p>
        </div>
    </div>
    
    <!-- Card Laki-laki (Light Style) -->
    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 relative group">
        <div class="flex justify-between items-start mb-4">
            <p class="text-sm font-medium text-gray-500">Pendaftar Putra</p>
            <div class="p-1 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div>
            <p class="text-4xl font-bold text-gray-900"><?= $total_l ?></p>
            <p class="text-xs text-gray-400 mt-2 flex items-center">
                <span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Santri Putra
            </p>
        </div>
    </div>
    
    <!-- Card Perempuan (Light Style) -->
    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 relative group">
        <div class="flex justify-between items-start mb-4">
            <p class="text-sm font-medium text-gray-500">Pendaftar Putri</p>
            <div class="p-1 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div>
            <p class="text-4xl font-bold text-gray-900"><?= $total_p ?></p>
            <p class="text-xs text-gray-400 mt-2 flex items-center">
                <span class="inline-block w-2 h-2 rounded-full bg-pink-500 mr-2"></span> Santri Putri
            </p>
        </div>
    </div>
    
    <!-- Card Lulus / Gagal -->
    <div class="bg-white rounded-[1.5rem] p-6 shadow-sm border border-gray-100 relative group">
        <div class="flex justify-between items-start mb-4">
            <p class="text-sm font-medium text-gray-500">Hasil Kelulusan</p>
            <div class="p-1 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </div>
        </div>
        <div>
            <p class="text-4xl font-bold text-gray-900"><?= $total_lulus ?> <span class="text-xl text-gray-300 font-normal">/ <?= $total_gagal ?></span></p>
            <p class="text-xs text-gray-400 mt-2 flex items-center">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span> Lulus &nbsp;&nbsp; <span class="inline-block w-2 h-2 rounded-full bg-red-400 mr-1"></span> Gagal
            </p>
        </div>
    </div>
</div>

<div class="bg-emerald-50 rounded-lg p-6 border border-emerald-100">
    <h2 class="text-lg font-bold text-emerald-800 mb-2">Panduan Cepat Sekretaris</h2>
    <ul class="list-disc list-inside text-emerald-700 space-y-1">
        <li>Gunakan menu <b>Rekap Pendaftar</b>, untuk melihat detail seluruh santri.</li>
        <li>Gunakan menu <b>Export Data</b> untuk mendownload data dalam format Microsoft Excel (.xls).</li>
        <li>Proses Verifikasi Pembayaran menjadi tugas dari <b>Bendahara Registrasi</b>.</li>
    </ul>
</div>

<?php
$content = ob_get_clean();
$title = "Dashboard Sekretaris - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
