<?php ob_start(); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Export Data ke Microsoft Excel</h1>
    <p class="text-gray-600 mt-1">Unduh data rekapitulasi santri untuk kebutuhan pelaporan.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl">
    <!-- Export Pendaftar -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6 flex flex-col h-full">
        <div class="flex-grow">
            <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                <span class="text-2xl mr-2">📋</span> Data Semua Pendaftar
            </h3>
            <p class="text-gray-600 text-sm mb-6">
                Mengunduh data biodata lengkap seluruh pendaftar beserta status PSB saat ini.
            </p>
        </div>
        <a href="<?= url('sekretaris/do-export/semua_pendaftar') ?>" class="w-full inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition shadow-sm">
            Unduh Excel (.xls)
        </a>
    </div>

    <!-- Export Nilai CBT -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6 flex flex-col h-full">
        <div class="flex-grow">
            <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                <span class="text-2xl mr-2">📊</span> Data Nilai Ujian
            </h3>
            <p class="text-gray-600 text-sm mb-6">
                Mengunduh hasil nilai skor Tes PG (CBT) dan nilai ujian lisan santri yang sudah melakukan tes.
            </p>
        </div>
        <a href="<?= url('sekretaris/do-export/nilai_cbt') ?>" class="w-full inline-block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded transition shadow-sm">
            Unduh Excel (.xls)
        </a>
    </div>

    <!-- Export Seragam -->
    <div class="bg-white rounded-lg shadow border border-gray-200 p-6 flex flex-col h-full">
        <div class="flex-grow">
            <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                <span class="text-2xl mr-2">👕</span> Data E-Seragam
            </h3>
            <p class="text-gray-600 text-sm mb-6">
                Mengunduh rekapan ukuran seragam masing-masing santri (dalam cm) beserta catatan tambahannya.
            </p>
        </div>
        <a href="<?= url('sekretaris/do-export/seragam') ?>" class="w-full inline-block text-center bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded transition shadow-sm">
            Unduh Excel (.xls)
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Export Data - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
