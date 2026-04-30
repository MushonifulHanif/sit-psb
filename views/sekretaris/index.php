<?php ob_start(); ?>
<style>[x-cloak] { display: none !important; }</style>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Rekapitulasi Santri</h1>
        <p class="text-gray-600 mt-1">Daftar seluruh pendaftar dan progres statusnya.</p>
    </div>
    <a href="<?= url('sekretaris/export') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        Export Data
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden" x-data="{ 
    openModal: false, 
    detailModal: false, 
    passwordModal: false,
    selectedSantri: null,
    init() {
        this.$watch('openModal', (val) => {
            if (val && this.selectedSantri) {
                // Wait for DOM then sync flatpickr & custom dropdown
                this.$nextTick(() => {
                    const inputs = document.querySelectorAll('.datetimepicker');
                    inputs.forEach(input => {
                        if (input._flatpickr) {
                            input._flatpickr.setDate(input.value, false);
                        }
                    });
                    // Sync custom dropdown
                    window.dispatchEvent(new CustomEvent('set-mufatis', { detail: this.selectedSantri.mufatis_id || '' }));
                });
            }
        });
    }
}">
    <!-- Search Bar Basic -->
    <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center" x-data="{ search: '' }">
        <div class="relative w-full max-md sm:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" x-model="search" @input="$el.closest('.bg-white').querySelectorAll('tbody tr').forEach(row => { row.style.display = row.innerText.toLowerCase().includes(search.toLowerCase()) ? '' : 'none'; })" class="focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2 px-3 border" placeholder="Cari nama, nomor tes, atau asal sekolah...">
        </div>
    </div>
    
    <div class="overflow-x-auto h-[600px] overflow-y-auto relative">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-white sticky top-0 shadow-sm z-10 text-left">
                <tr>
                    <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">No Tes & Nama</th>
                    <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Biodata</th>
                    <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Password</th>
                    <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status PSB</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($santri as $row): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="text-xs text-emerald-600 font-mono mt-1"><?= htmlspecialchars($row['username']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs text-gray-600 flex flex-col gap-1">
                            <span class="flex items-center"><span class="w-16 font-semibold uppercase text-[9px] text-gray-400 tracking-tighter">Gen:</span> <?= $row['jk'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></span>
                            <span class="flex items-center"><span class="w-16 font-semibold uppercase text-[9px] text-gray-400 tracking-tighter">Asal:</span> <?= htmlspecialchars($row['asal_sekolah'] ?? '-') ?></span>
                            <span class="flex items-center"><span class="w-16 font-semibold uppercase text-[9px] text-gray-400 tracking-tighter">Status:</span> <?= ($row['is_lengkap'] ?? 0) ? '<span class="text-emerald-600 font-bold">Lengkap</span>' : '<span class="text-amber-500 font-bold">Belum Lengkap</span>' ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-xs font-mono font-bold text-gray-800 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100 flex items-center justify-between w-fit group">
                            <span x-data="{ show: false }">
                                <span x-show="!show" class="tracking-widest">••••••</span>
                                <span x-show="show" x-text="'<?= htmlspecialchars($row['password_plain'] ?? '-') ?>'" x-cloak></span>
                                <button type="button" @click="show = !show" class="ml-2 text-gray-400 hover:text-emerald-600 transition-colors">
                                    <svg x-show="!show" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    <svg x-show="show" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                </button>
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-[10px] leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                            <?= str_replace('_', ' ', $row['status_psb']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center flex items-center justify-center gap-2">
                        <!-- Detail Button -->
                        <button @click="detailModal = true; selectedSantri = <?= htmlspecialchars(json_encode($row)) ?>" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 p-2 rounded-lg transition-all border border-emerald-100" title="Detail Biodata">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>

                        <!-- WhatsApp Button -->
                        <?php 
                            $no_wa = preg_replace('/[^0-9]/', '', $row['no_wa'] ?? '');
                            if(strpos($no_wa, '0') === 0) $no_wa = '62' . substr($no_wa, 1);
                            
                            $pesan_wa = "Assalamu'alaikum Wr Wrb, Ayah/Bunda dari *" . $row['name'] . "*. Saya Sekretaris Panitia PSB PPRTQ Raudlatul Falah. Mengenai pendaftaran Ananda, kami ingin mendiskusikan penentuan jadwal tes seleksi agar tidak bentrok dengan kegiatan Ananda. Kira-kira kapan waktu luang Ayah/Bunda untuk mendampingi Ananda tes? Terimakasih.";
                            $url_wa = "https://wa.me/{$no_wa}?text=" . urlencode($pesan_wa);
                        ?>
                        <a href="<?= $url_wa ?>" target="_blank" class="bg-green-100 text-green-700 hover:bg-green-200 p-2 rounded-lg transition-all border border-green-200 shadow-sm shadow-green-100" title="Hubungi via WhatsApp">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.812 1.056 3.882 1.614 5.99 1.616h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </a>

                        <!-- Password Button -->
                        <button @click="passwordModal = true; selectedSantri = <?= htmlspecialchars(json_encode($row)) ?>" class="bg-amber-50 text-amber-700 hover:bg-amber-100 p-2 rounded-lg transition-all border border-amber-100" title="Ganti Password">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                        </button>

                        <?php if ($mode_ujian == 'custom'): ?>
                            <button @click="openModal = true; selectedSantri = <?= htmlspecialchars(json_encode($row)) ?>" class="bg-purple-100 text-purple-700 hover:bg-purple-200 p-2 rounded-lg transition-all border border-purple-200" title="Atur Jadwal">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($santri)): ?>
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-600">Belum ada data santri.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Detail Biodata -->
    <div x-show="detailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm" x-cloak>
        <div x-show="detailModal" @click="detailModal = false" class="fixed inset-0 transition-opacity"></div>
        <div x-show="detailModal" class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-4xl w-full relative overflow-hidden p-8 sm:p-10 transition-all transform scale-100">
            <div class="flex justify-between items-start mb-10">
                <div class="flex items-center">
                    <template x-if="selectedSantri && selectedSantri.file_foto">
                        <img :src="'<?= url('/') ?>' + selectedSantri.file_foto" class="w-24 h-32 object-cover rounded-3xl shadow-2xl mr-8 border-4 border-white/50">
                    </template>
                    <div>
                        <h3 class="text-3xl font-black text-gray-900" x-text="selectedSantri ? selectedSantri.name : ''"></h3>
                        <p class="text-emerald-600 font-black text-sm tracking-[0.2em] mt-2" x-text="selectedSantri ? selectedSantri.username : ''"></p>
                        <div class="flex gap-3 mt-4">
                            <span class="bg-white/50 backdrop-blur-sm border border-white/60 text-gray-700 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm" x-text="selectedSantri ? selectedSantri.jenjang : '-'"></span>
                            <span :class="selectedSantri && selectedSantri.is_lengkap == 1 ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white'" class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100" x-text="selectedSantri && selectedSantri.is_lengkap == 1 ? 'Lengkap' : 'Belum Lengkap'"></span>
                        </div>
                    </div>
                </div>
                <button @click="detailModal = false" class="text-gray-400 hover:text-gray-600 p-2 bg-white/50 hover:bg-white rounded-2xl transition-all border border-white/60">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Segmen 1 & 2 -->
                <div class="space-y-6">
                    <div class="bg-white/40 backdrop-blur-sm p-8 rounded-[2rem] border border-white/60 shadow-sm">
                        <h4 class="text-[10px] font-black text-emerald-800 uppercase tracking-widest mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Informasi Personal
                        </h4>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Panggilan</span><span class="text-sm font-black text-gray-800" x-text="selectedSantri ? selectedSantri.nama_panggilan : '-'"></span></div>
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">NISN</span><span class="text-sm font-black text-gray-800" x-text="selectedSantri ? (selectedSantri.nisn || '-') : '-'"></span></div>
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">TTL</span><span class="text-sm font-black text-gray-800" x-text="selectedSantri ? (selectedSantri.tempat_lahir + ', ' + selectedSantri.tgl_lahir) : '-'"></span></div>
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Warga</span><span class="text-sm font-black text-gray-800" x-text="selectedSantri ? selectedSantri.kewarganegaraan : '-'"></span></div>
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Saudara</span><span class="text-sm font-black text-gray-800" x-text="selectedSantri ? selectedSantri.jumlah_saudara : '-'"></span></div>
                            <div class="pt-4">
                                <span class="text-[10px] text-emerald-800 font-black uppercase tracking-widest block mb-2">Alamat Lengkap</span>
                                <p class="text-sm text-gray-700 font-medium leading-relaxed bg-white/30 p-4 rounded-2xl border border-white/40" x-text="selectedSantri ? selectedSantri.alamat_lengkap : '-'"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Segmen 3 & 4 -->
                <div class="space-y-6">
                    <div class="bg-white/40 backdrop-blur-sm p-8 rounded-[2rem] border border-white/60 shadow-sm">
                        <h4 class="text-[10px] font-black text-emerald-800 uppercase tracking-widest mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            Informasi Wali
                        </h4>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Ayah</span><span class="text-sm font-black text-gray-800" x-text="selectedSantri ? selectedSantri.nama_ayah : '-'"></span></div>
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Ibu</span><span class="text-sm font-black text-gray-800" x-text="selectedSantri ? selectedSantri.nama_ibu : '-'"></span></div>
                            <div class="flex justify-between border-b border-white/40 pb-2"><span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kontak WA</span><span class="text-sm font-black text-emerald-600" x-text="selectedSantri ? (selectedSantri.wa_ortu || selectedSantri.no_wa) : '-'"></span></div>
                        </div>
                    </div>

                    <div class="bg-white/40 backdrop-blur-sm p-8 rounded-[2rem] border border-white/60 shadow-sm">
                        <h4 class="text-[10px] font-black text-emerald-800 uppercase tracking-widest mb-6 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            Berkas Digital
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <template x-if="selectedSantri && selectedSantri.file_akta">
                                <a :href="'<?= url('/') ?>' + selectedSantri.file_akta" @click.prevent="$dispatch('image-preview', { src: $el.href, title: 'AKTA KELAHIRAN' })" class="flex flex-col items-center justify-center p-4 bg-white/50 border border-white/60 rounded-3xl hover:bg-white hover:shadow-xl hover:shadow-emerald-100 transition-all cursor-pointer group">
                                    <svg class="w-6 h-6 text-emerald-600 mb-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">Akta</span>
                                </a>
                            </template>
                            <template x-if="selectedSantri && selectedSantri.file_ktp">
                                <a :href="'<?= url('/') ?>' + selectedSantri.file_ktp" @click.prevent="$dispatch('image-preview', { src: $el.href, title: 'KTP KEDUA ORANG TUA' })" class="flex flex-col items-center justify-center p-4 bg-white/50 border border-white/60 rounded-3xl hover:bg-white hover:shadow-xl hover:shadow-emerald-100 transition-all cursor-pointer group">
                                    <svg class="w-6 h-6 text-emerald-600 mb-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v1m-4 0h4" /></svg>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">KTP</span>
                                </a>
                            </template>
                            <template x-if="selectedSantri && selectedSantri.file_kk">
                                <a :href="'<?= url('/') ?>' + selectedSantri.file_kk" @click.prevent="$dispatch('image-preview', { src: $el.href, title: 'KARTU KELUARGA' })" class="flex flex-col items-center justify-center p-4 bg-white/50 border border-white/60 rounded-3xl hover:bg-white hover:shadow-xl hover:shadow-emerald-100 transition-all cursor-pointer group">
                                    <svg class="w-6 h-6 text-emerald-600 mb-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">KK</span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Custom Schedule -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm" x-cloak>
        <div x-show="openModal" @click="openModal = false" class="fixed inset-0 transition-opacity"></div>
        <div x-show="openModal" class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-lg w-full relative p-8 sm:p-10 transition-all transform scale-100">
            <form action="<?= url('sekretaris/update-jadwal-kustom') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <input type="hidden" name="user_id" :value="selectedSantri ? selectedSantri.id : ''">
                
                <div class="mb-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-3xl bg-purple-600 text-white shadow-xl shadow-purple-100 mb-6 transform -rotate-6">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 leading-tight" x-text="selectedSantri ? selectedSantri.name : 'Atur Jadwal'"></h3>
                    <p class="text-xs font-black text-purple-600 uppercase tracking-[0.2em] mt-2" x-text="selectedSantri ? selectedSantri.username : ''"></p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-purple-800 mb-2 ml-1">Mulai Ujian</label>
                        <input type="text" name="jadwal_mulai" :value="selectedSantri ? selectedSantri.jadwal_mulai_kustom : ''" x-on:change="if(selectedSantri) selectedSantri.jadwal_mulai_kustom = $el.value" class="datetimepicker w-full border-gray-200 rounded-2xl focus:ring-purple-500/20 focus:border-purple-500 border bg-white/50 px-5 py-3.5 font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-purple-800 mb-2 ml-1">Selesai Ujian</label>
                        <input type="text" name="jadwal_selesai" :value="selectedSantri ? selectedSantri.jadwal_selesai_kustom : ''" x-on:change="if(selectedSantri) selectedSantri.jadwal_selesai_kustom = $el.value" class="datetimepicker w-full border-gray-200 rounded-2xl focus:ring-purple-500/20 focus:border-purple-500 border bg-white/50 px-5 py-3.5 font-bold text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-purple-800 mb-2 ml-1">Mufatis Penguji</label>
                        <?php 
                            $mufOptions = [['label' => '-- Otomatis / Random --', 'value' => '']];
                            foreach($mufatis_list as $m) { $mufOptions[] = ['label' => $m['name'], 'value' => (string)$m['id']]; }
                        ?>
                        <div class="relative group" x-data="{ 
                            open: false, 
                            value: '',
                            options: <?= htmlspecialchars(json_encode($mufOptions)) ?>,
                            get selectedLabel() {
                                return this.options.find(o => o.value == this.value)?.label || '-- Pilih Mufatis --'
                            }
                        }" @set-mufatis.window="value = $event.detail">
                            <input type="hidden" name="mufatis_id" :value="value">
                            <button type="button" @click="open = !open" @click.away="open = false"
                                    class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white/50 hover:bg-white font-black text-gray-700">
                                <span x-text="selectedLabel"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            <div x-show="open" x-transition x-cloak
                                 class="absolute z-[80] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                <template x-for="item in options" :key="item.value">
                                    <div @click="value = item.value; open = false" 
                                         class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-purple-50 transition-all font-black"
                                         :class="value == item.value ? 'bg-purple-50 text-purple-700' : 'text-gray-600'">
                                        <span x-text="item.label" class="text-xs whitespace-nowrap"></span>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                             :class="value == item.value ? 'border-purple-500 bg-purple-500' : 'border-gray-200 bg-white'">
                                            <div x-show="value == item.value" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button type="button" @click="openModal = false" class="flex-1 px-6 py-3.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 bg-white/50 border border-white/60 rounded-2xl transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-3.5 text-[10px] font-black uppercase tracking-widest text-white bg-purple-600 rounded-2xl hover:bg-purple-700 shadow-xl shadow-purple-100 transition-all active:scale-95">Simpan Jadwal</button>
                </div>
            </form>

            <div class="mt-10 pt-8 border-t border-dashed border-gray-200">
                <p class="text-[9px] font-black text-gray-400 uppercase mb-4 tracking-[0.3em] text-center">Area Berbahaya</p>
                <form action="<?= url('sekretaris/reset-ujian') ?>" method="POST" onsubmit="return confirmSubmit(this, 'Reset progres ujian santri?')">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    <input type="hidden" name="user_id" :value="selectedSantri ? selectedSantri.id : ''">
                    <button type="submit" class="w-full px-4 py-4 text-[10px] font-black text-red-600 bg-red-50/50 border border-red-100 rounded-2xl hover:bg-red-600 hover:text-white transition-all flex items-center justify-center tracking-widest uppercase shadow-sm group">
                        <svg class="w-4 h-4 mr-2 group-hover:rotate-180 transition-transform duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Reset & Ujian Ulang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Change Password -->
    <div x-show="passwordModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm" x-cloak>
        <div x-show="passwordModal" @click="passwordModal = false" class="fixed inset-0 transition-opacity"></div>
        <div x-show="passwordModal" class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-md w-full relative p-8 sm:p-10 transition-all transform scale-100">
            <form action="<?= url('sekretaris/update-password') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <input type="hidden" name="user_id" :value="selectedSantri ? selectedSantri.id : ''">
                
                <div class="mb-8 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-3xl bg-amber-500 text-white shadow-xl shadow-amber-100 mb-6 transform rotate-6">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 leading-tight">Ganti Password</h3>
                    <p class="text-xs font-black text-amber-600 uppercase tracking-[0.2em] mt-2" x-text="selectedSantri ? selectedSantri.name : ''"></p>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-amber-800 mb-2 ml-1">Password Baru</label>
                        <input type="text" name="password" required minlength="6" placeholder="Masukkan password baru..." class="w-full border-gray-200 rounded-2xl focus:ring-amber-500/20 focus:border-amber-500 border bg-white/50 px-5 py-4 font-bold text-sm">
                        <p class="text-[10px] text-gray-500 mt-2 italic px-1">Minimal 6 karakter. Harap informasikan password baru kepada santri terkait.</p>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button type="button" @click="passwordModal = false" class="flex-1 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 bg-white/50 border border-white/60 rounded-2xl transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-4 text-[10px] font-black uppercase tracking-widest text-white bg-amber-500 rounded-2xl hover:bg-amber-600 shadow-xl shadow-amber-100 transition-all active:scale-95">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Rekapitulasi Santri - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
?>
