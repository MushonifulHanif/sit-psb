<?php ob_start(); 
// Convert pengaturan rows to associative array for easier access
$set = [];
foreach ($pengaturan as $r) {
    $set[$r['key']] = $r['value'];
}
$list_narahubung = json_decode($set['list_narahubung'] ?? '[]', true);
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Pengaturan Sistem</h1>
        <p class="text-xs sm:text-sm text-gray-600 mt-1">Konfigurasi dasar, pembiayaan, dan mode pelaksanaan ujian.</p>
    </div>
</div>

<form action="<?= url('admin/update-pengaturan') ?>" method="POST" class="space-y-6 sm:space-y-8 pb-20" x-data="{ 
    mode_ujian: '<?= $set['mode_ujian'] ?? 'serempak' ?>',
    narahubung: <?= htmlspecialchars($set['list_narahubung'] ?? '[]') ?>
}">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

    <!-- Section 1: Identitas -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            </div>
            <h2 class="text-base sm:text-lg font-bold text-gray-800">Identitas Pesantren</h2>
        </div>
        <div class="p-4 sm:p-6 space-y-5">
            <div class="w-full">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pesantren</label>
                <input type="text" name="setting[nama_pesantren]" value="<?= htmlspecialchars($set['nama_pesantren'] ?? '') ?>" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 px-4 py-3 border transition">
            </div>
            <div class="w-full">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="setting[alamat_pesantren]" rows="2" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 px-4 py-3 border transition"><?= htmlspecialchars($set['alamat_pesantren'] ?? '') ?></textarea>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="w-full lg:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telp / Kantor</label>
                    <input type="text" name="setting[no_telp]" value="<?= htmlspecialchars($set['no_telp'] ?? '') ?>" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border">
                </div>
                <div class="w-full lg:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Resmi</label>
                    <input type="email" name="setting[email]" value="<?= htmlspecialchars($set['email'] ?? '') ?>" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border">
                </div>
            </div>

            <div class="w-full">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Website</label>
                <input type="text" name="setting[website]" value="<?= htmlspecialchars($set['website'] ?? '') ?>" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border">
            </div>

            <!-- Narahubung Dynamic List -->
            <div class="pt-6 border-t border-gray-100 mt-6">
                <label class="block text-sm font-bold text-emerald-800 mb-4 uppercase tracking-wider flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    Narahubung / Contact Person (Multiple)
                </label>
                <div class="space-y-4">
                    <template x-for="(item, index) in narahubung" :key="index">
                        <div class="relative bg-gray-50 p-4 rounded-2xl border border-gray-100 transition-all hover:border-emerald-200">
                            <button type="button" @click="narahubung.splice(index, 1)" class="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 transition z-10">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                            <div class="flex flex-col sm:flex-row gap-4">
                                <div class="w-full sm:w-1/2">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Nama Kontak</label>
                                    <input type="text" :name="'narahubung[nama]['+index+']'" x-model="item.nama" placeholder="Contoh: Ust. Fulan" class="w-full text-sm border-gray-200 rounded-xl focus:ring-emerald-500 px-3 py-3 border">
                                </div>
                                <div class="w-full sm:w-1/2">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">No. WhatsApp</label>
                                    <input type="text" :name="'narahubung[wa]['+index+']'" x-model="item.wa" placeholder="628..." class="w-full text-sm border-gray-200 rounded-xl focus:ring-emerald-500 px-3 py-3 border">
                                </div>
                            </div>
                        </div>
                    </template>
                    <button type="button" @click="narahubung.push({nama: '', wa: ''})" class="w-full py-4 border-2 border-dashed border-gray-200 rounded-2xl text-gray-500 hover:border-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 transition flex items-center justify-center font-bold">
                        <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Tambah Narahubung
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Pembiayaan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center">
            <div class="p-2 bg-emerald-100 rounded-lg mr-3">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m.599-1c.532-.1 1.016-.308 1.401-.599M12 16H11" /></svg>
            </div>
            <h2 class="text-base sm:text-lg font-bold text-gray-800">Keuangan & Pembiayaan</h2>
        </div>
        <div class="p-4 sm:p-6 space-y-5">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="w-full lg:w-1/3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bank</label>
                    <input type="text" name="setting[bank_rekening]" value="<?= htmlspecialchars($set['bank_rekening'] ?? 'BSI') ?>" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border">
                </div>
                <div class="w-full lg:w-2/3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="setting[no_rekening_bsi]" value="<?= htmlspecialchars($set['no_rekening_bsi'] ?? '') ?>" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Atas Nama Rekening</label>
                <input type="text" name="setting[nama_rekening_bsi]" value="<?= htmlspecialchars($set['nama_rekening_bsi'] ?? '') ?>" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border">
            </div>
            <div class="flex flex-col lg:flex-row gap-4 pt-2">
                <div class="w-full lg:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Biaya Registrasi Pendaftaran</label>
                    <div class="relative" x-data="{ val: '<?= $set['biaya_registrasi'] ?? '' ?>' }" x-init="val = formatRupiah(val); $watch('val', v => val = formatRupiah(v))">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-emerald-600 font-bold">Rp</span>
                        </div>
                        <input type="text" name="setting[biaya_registrasi]" x-model="val" class="pl-12 w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border font-bold text-emerald-700">
                    </div>
                </div>
                <div class="w-full lg:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Biaya Daftar Ulang (Taksi)</label>
                    <div class="relative" x-data="{ val: '<?= $set['biaya_daftar_ulang'] ?? '' ?>' }" x-init="val = formatRupiah(val); $watch('val', v => val = formatRupiah(v))">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-emerald-600 font-bold">Rp</span>
                        </div>
                        <input type="text" name="setting[biaya_daftar_ulang]" x-model="val" class="pl-12 w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border font-bold text-emerald-700">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Ujian -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            </div>
            <h2 class="text-base sm:text-lg font-bold text-gray-800">Pelaksanaan Ujian (CBT)</h2>
        </div>
        <div class="p-4 sm:p-6 space-y-6">
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="w-full lg:w-1/2">
                    <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wider">Mode Penjadwalan Ujian</label>
                    <div class="relative" x-data="{ open: false, options: [{ label: 'Serempak (Gelombang)', value: 'serempak' }, { label: 'Automatis (Masa Berlaku)', value: 'otomatis' }, { label: 'Custom (Per-Santri)', value: 'custom' }], get selectedLabel() { return this.options.find(o => o.value == mode_ujian)?.label || 'Pilih Mode' } }">
                        <input type="hidden" name="setting[mode_ujian]" :value="mode_ujian" required>
                        <button type="button" @click="open = !open" @click.away="open = false" class="w-full flex items-center justify-between px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50/50 font-bold text-emerald-800">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="open" x-transition x-cloak class="absolute z-[100] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                            <template x-for="item in options" :key="item.value">
                                <div @click="mode_ujian = item.value; open = false" class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all font-bold" :class="mode_ujian == item.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                    <span x-text="item.label" class="text-xs"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-1/2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Durasi Pengerjaan (Menit)</label>
                    <div class="flex items-center">
                        <input type="number" name="setting[durasi_ujian_menit]" value="<?= htmlspecialchars($set['durasi_ujian_menit'] ?? '45') ?>" class="w-full border-gray-200 rounded-l-xl px-4 py-3 border">
                        <span class="bg-gray-50 border border-l-0 border-gray-200 px-4 py-3 rounded-r-xl text-gray-500 text-sm">Menit</span>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Template WA Hafalan</label>
                <textarea name="setting[template_wa_hafalan]" rows="4" class="w-full border-gray-200 rounded-xl px-4 py-3 border text-sm font-mono"><?= htmlspecialchars($set['template_wa_hafalan'] ?? '') ?></textarea>
            </div>

            <div class="w-full">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pesan Ajakan Infaq</label>
                <textarea name="setting[pesan_infaq]" rows="3" class="w-full border-gray-200 rounded-xl px-4 py-3 border"><?= htmlspecialchars($set['pesan_infaq'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Submit Area -->
    <div class="flex justify-end pt-4">
        <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-10 rounded-2xl shadow-lg transition-all active:scale-95 flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
            Simpan Pengaturan
        </button>
    </div>
</form>

<div class="h-20"></div>

<?php
$content = ob_get_clean();
$title = "Pengaturan Sistem - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
?>
