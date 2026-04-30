<?php ob_start(); ?>
<?php
// Tentukan status tab (lock/unlock) based on various flags from Controller
$status = $user['status_psb'];

// Logic Penentuan Tahapan Terkini & Status Selesai
$step_results = get_santri_step_results($user['id']);
$current_step = calculate_current_step($step_results);

// Additional variables needed for some tab views
$biaya_du = floatval($settings['biaya_daftar_ulang'] ?? 0);
$total_bayar_du = $total_bayar_du ?? 0;
?>

<!-- Tab Contents (Integrated with Global activeTab) -->
<div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 min-h-[500px] overflow-hidden">
    <div class="p-6 sm:p-10">
        
        <!-- Tab 1: Profil & Biodata (Includes Payment Info) -->
        <div x-show="activeTab === 1" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            
            <!-- Conditional Dashboard for Tab 1 -->
            <?php if (!$can_fill_biodata): ?>
                <!-- Sub-State A: Payment Required -->
                <div class="max-w-2xl mx-auto py-8">
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-8 rounded-3xl mb-10 shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-amber-100 p-3 rounded-2xl">
                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <div class="ml-5">
                                <h3 class="text-xl font-black text-amber-900 tracking-tight">Verifikasi Pembayaran Diperlukan</h3>
                                <p class="text-amber-700 mt-2 leading-relaxed">Silakan lakukan pembayaran biaya registrasi sebesar <span class="font-bold text-2xl text-amber-900"><?= format_rupiah($settings['biaya_registrasi'] ?? 0) ?></span> untuk melanjutkan ke tahap pengisian biodata.</p>
                            </div>
                        </div>
                    </div>

                    <?php
                    $pending_reg = false;
                    $ditolak_reg = false;
                    $catatan_reg = '';
                    foreach ($pembayaran_list as $p) {
                        if ($p['jenis'] == 'registrasi') {
                            if ($p['status'] == 'pending') $pending_reg = true;
                            if ($p['status'] == 'ditolak') {
                                $ditolak_reg = true;
                                $catatan_reg = $p['catatan_verifikasi'] ?? 'Bukti tidak valid.';
                            }
                            break;
                        }
                    }
                    ?>

                    <?php if ($pending_reg): ?>
                        <div class="bg-blue-50 rounded-[2.5rem] p-12 border border-blue-100 text-center shadow-lg shadow-blue-50">
                            <div class="w-20 h-20 bg-blue-100 rounded-3xl flex items-center justify-center mx-auto mb-6 animate-bounce shadow-inner">
                                <svg class="w-10 h-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h4 class="text-2xl font-black text-blue-900 tracking-tight">Menunggu Verifikasi</h4>
                            <p class="text-blue-700 mt-3 text-lg leading-relaxed max-w-md mx-auto">Kami sedang memeriksa bukti pembayaran Anda. Proses ini biasanya memakan waktu maksimal 1x24 jam.</p>
                        </div>
                    <?php else: ?>
                        <div class="bg-white rounded-[2.5rem] p-10 border-4 border-dashed border-gray-100 bg-gradient-to-b from-white to-gray-50">
                             <?php if ($ditolak_reg): ?>
                                <div class="bg-red-50 text-red-700 p-5 rounded-2xl mb-8 text-sm flex items-start border border-red-100">
                                    <svg class="w-6 h-6 mr-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <div>
                                        <p class="font-bold">Pembayaran Ditolak</p>
                                        <p class="opacity-80 mt-1">Alasan: <b><?= htmlspecialchars($catatan_reg) ?></b>. Silakan unggah ulang bukti yang benar.</p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <h3 class="text-xl font-bold text-gray-900 mb-8 flex items-center">
                                <span class="bg-emerald-600 text-white w-10 h-10 rounded-2xl flex items-center justify-center mr-4 text-sm font-black shadow-lg shadow-emerald-200">1</span>
                                Unggah Bukti Transfer
                            </h3>
                            <form action="<?= url('santri/upload-pembayaran-reg') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
                                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                <div class="bg-white p-8 rounded-3xl border-2 border-gray-100 shadow-sm hover:border-emerald-300 transition-all group relative">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 group-hover:text-emerald-600 transition-all ml-1">Pilih Berkas Struk/Screenshot <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                        </div>
                                        <input type="file" name="bukti_transfer" required accept="image/*,.pdf" class="block w-full pl-12 pr-4 py-3.5 border border-gray-200 rounded-2xl text-sm file:hidden cursor-pointer hover:bg-gray-50 transition-all font-medium text-gray-500">
                                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-emerald-600 font-bold text-xs uppercase tracking-widest">PILIH FILE</div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-4 font-bold italic uppercase tracking-widest text-center">Format: JPG, PNG, PDF. Maksimal 5MB.</p>
                                </div>
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-5 rounded-3xl shadow-2xl shadow-emerald-200 transition-all transform active:scale-95 uppercase tracking-widest text-sm flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    Konfirmasi Sekarang
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Sub-State B: Fill Full Biodata -->
                <?php require __DIR__ . '/_tab_biodata.php'; ?>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Tes Online -->
        <div x-show="activeTab === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            <?php require __DIR__ . '/_tab_cbt.php'; ?>
        </div>

        <!-- Tab 3: Pengumuman -->
        <div x-show="activeTab === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            <?php require __DIR__ . '/_tab_pengumuman.php'; ?>
        </div>

        <!-- Tab 4: Daftar Ulang -->
        <div x-show="activeTab === 4" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            <?php require __DIR__ . '/_tab_daftar_ulang.php'; ?>
        </div>

        <!-- Tab 5: E-Seragam -->
        <div x-show="activeTab === 5" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            <?php require __DIR__ . '/_tab_seragam.php'; ?>
        </div>

    </div>
</div>

<?php if ($status == 'selesai'): ?>
    <?php require __DIR__ . '/_pesan_selamat_modal.php'; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
$title = "Dashboard Santri - SIT-PSB";
require __DIR__ . '/../layouts/santri.php';
?>
