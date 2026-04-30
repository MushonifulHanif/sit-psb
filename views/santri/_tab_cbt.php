<?php
// Tentukan apakah bisa ujian. Prioritas: 
// 1. Jadwal Custom (jika ada di data user)
// 2. Global Mode (Otomatis / Serempak)

$bisa_ujian = false;
$belum_waktunya = false; 
$sudah_lewat = false;
$jadwal_info = "";
$jadwal_akhir = "";

$now = new DateTime();
$mode = $settings['mode_ujian'] ?? 'serempak';

// PRIORITAS 1: Cek apakah ada jadwal Custom untuk santri ini
if (!empty($user['jadwal_mulai_kustom']) || !empty($user['jadwal_selesai_kustom'])) {
    if (!empty($user['jadwal_mulai_kustom']) && !empty($user['jadwal_selesai_kustom'])) {
        $mulai = new DateTime($user['jadwal_mulai_kustom']);
        $selesai = new DateTime($user['jadwal_selesai_kustom']);

        $bisa_ujian = ($now >= $mulai && $now <= $selesai);
        $belum_waktunya = ($now < $mulai);
        $sudah_lewat = ($now > $selesai);
        $jadwal_info = $mulai->format('d M Y H:i');
        $jadwal_akhir = $selesai->format('d M Y H:i');
    } else {
        // Jadwal parsial (mulai ada tapi selesai tidak, atau sebaliknya)
        $belum_waktunya = true;
        $jadwal_info = "Menunggu Kelengkapan Jadwal";
    }
} 
// PRIORITAS 2: Gunakan Mode Global jika tidak ada jadwal custom
else {
    if ($mode == 'serempak') {
        if ($gelombang && !empty($gelombang['jadwal_ujian_mulai']) && !empty($gelombang['jadwal_ujian_selesai'])) {
            $mulai = new DateTime($gelombang['jadwal_ujian_mulai']);
            $selesai = new DateTime($gelombang['jadwal_ujian_selesai']);
            
            $bisa_ujian = ($now >= $mulai && $now <= $selesai);
            $belum_waktunya = ($now < $mulai);
            $sudah_lewat = ($now > $selesai);
            $jadwal_info = $mulai->format('d M Y H:i');
            $jadwal_akhir = $selesai->format('d M Y H:i');
        }
    } elseif ($mode == 'otomatis') {
        if (!empty($user['ready_at'])) {
            $mulai = new DateTime($user['ready_at']);
            $durasi_hari = $settings['durasi_otomatis_hari'] ?? 3;
            $selesai = (clone $mulai)->modify("+$durasi_hari days");

            $bisa_ujian = ($now >= $mulai && $now <= $selesai);
            $sudah_lewat = ($now > $selesai);
            $jadwal_info = "Sekarang (Otomatis)";
            $jadwal_akhir = $selesai->format('d M Y H:i');
        } else {
            $belum_waktunya = true;
        }
    } elseif ($mode == 'custom') {
        // Mode custom dipilih di pengaturan, tapi santri ini belum diset tgl-nya sama sekali
        $belum_waktunya = true;
    }
}

if ($status == 'siap_ujian'): 
    if (!$is_biodata_lengkap):
?>
        <div class="text-center py-20">
            <div class="w-20 h-20 bg-amber-50 rounded-3xl flex items-center justify-center mx-auto mb-6 border-2 border-dashed border-amber-200">
                <svg class="w-10 h-10 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900">Lengkapi Biodata Terlebih Dahulu</h3>
            <p class="text-gray-500 mt-2 max-w-md mx-auto">Anda belum menyelesaikan pengisian biodata di Tahap 1. Silakan lengkapi data Anda untuk melihat syarat video dan jadwal ujian.</p>
            <button @click="activeTab = 1" class="mt-8 px-8 py-3 bg-emerald-600 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all">Pergi ke Tahap 1</button>
        </div>
<?php
    elseif (!$cbt || !$cbt['sudah_kirim_video']):
        require __DIR__ . '/_pra_syarat_cbt.php';
    else: 
        if ($bisa_ujian):
?>
            <div class="text-center py-10">
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Ujian CBT Siap Dimulai</h3>
                <p class="text-gray-600 mb-6">Waktu pengerjaan adalah <?= htmlspecialchars($settings['durasi_ujian_menit'] ?? 45) ?> menit. Pastikan koneksi internet stabil.</p>
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-xl inline-block">
                    <p class="text-sm text-emerald-800 font-bold flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Jadwal Berakhir: <?= $jadwal_akhir ?>
                    </p>
                </div>
                <br>
                <a href="<?= url('santri/mulai-cbt') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-10 rounded-2xl text-lg shadow-lg shadow-emerald-100 transition-all hover:scale-105 active:scale-95 inline-flex items-center">
                    Mulai Ujian Sekarang
                </a>
            </div>
<?php
        elseif ($belum_waktunya):
?>
            <div class="text-center py-10">
                <span class="text-6xl border border-gray-200 bg-gray-50 rounded-full p-6 inline-block mb-6 shadow-sm">⏳</span>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Ujian Belum Dimulai</h3>
                <p class="text-gray-600">
                    <?php if ($mode == 'otomatis' && empty($user['ready_at'])): ?>
                        Pembayaran Anda sedang diverifikasi atau diproses. Mohon tunggu.
                    <?php elseif (empty($jadwal_info)): ?>
                        Jadwal khusus Anda belum diatur oleh Panitia. Mohon hubungi Narahubung.
                    <?php else: ?>
                        Jadwal ujian Anda: <b><?= $jadwal_info ?></b>
                    <?php endif; ?>
                </p>
                <p class="text-gray-500 text-sm mt-4">Halaman ini akan otomatis menampilkan tombol Mulai saat jadwal tiba.</p>
            </div>
<?php
        elseif ($sudah_lewat):
?>
            <div class="text-center py-12 bg-red-50 border border-red-100 rounded-3xl">
                <span class="text-6xl mb-6 inline-block">⌛</span>
                <h3 class="text-2xl font-bold text-red-800 mb-2">Waktu Ujian Telah Berakhir</h3>
                <p class="text-red-600 mb-4 px-10">Maaf, masa ujian untuk akun Anda telah berakhir pada <b><?= $jadwal_akhir ?></b>.</p>
                <p class="text-red-500 text-sm">Jika ini adalah kesalahan atau Anda membutuhkan bantuan, silakan hubungi Panitia.</p>
            </div>
<?php
        endif;
    endif;
elseif (in_array($status, ['sudah_ujian', 'lulus', 'daftar_ulang', 'selesai'])):
?>
    <div class="text-center py-10">
        <span class="text-6xl border border-emerald-100 bg-emerald-50 rounded-full p-6 inline-block mb-6 shadow-sm text-emerald-500">✓</span>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Telah Selesai Mengerjakan Ujian</h3>
        <p class="text-gray-600">Alhamdulillah, Anda telah menyelesaikan seluruh rangkaian tes. <br>Silakan menunggu hasil pengumuman resmi di tab berikutnya.</p>
    </div>
<?php endif; ?>
