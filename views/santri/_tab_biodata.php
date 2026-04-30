<?php
// Pre-fill logic correctly uses $biodata from controller
?>
<div class="max-w-4xl mx-auto py-6" x-data="{ 
    step: 1,
    photoPreview: '<?= $biodata['file_foto'] ? url($biodata['file_foto']) : '' ?>',
    updatePreview(event) {
        const file = event.target.files[0];
        if (file) {
            this.photoPreview = URL.createObjectURL(file);
        }
    }
}">
    <!-- Sub-Stepper (Progress Indicator Inside Biodata) -->
    <div class="mb-12">
        <div class="flex items-center justify-between relative px-2 max-w-2xl mx-auto">
            <!-- Line Background -->
            <div class="absolute left-10 right-10 top-5 h-0.5 bg-gray-100 z-0"></div>
            
            <template x-for="i in 4" :key="i">
                <div class="relative z-10 flex flex-col items-center flex-1 last:flex-none">
                    <button type="button" @click="if(step >= i) step = i" 
                         :class="step >= i ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-50' : 'bg-white text-gray-300 border-2 border-gray-100'" 
                         class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold transition-all duration-300 text-sm">
                        <span x-text="i"></span>
                    </button>
                    <span class="mt-2 text-[0.6rem] font-bold uppercase tracking-widest hidden sm:block" 
                          :class="step >= i ? 'text-emerald-700' : 'text-gray-400'"
                          x-text="['','Akademik','Personal','Wali','Berkas'][i]"></span>
                </div>
            </template>
        </div>
    </div>

    <!-- Form Start -->
    <form action="<?= url('santri/simpan-biodata') ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">

        <!-- Segmen 1: Informasi Akademik -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4">
            <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                    Segmen 1: Informasi Akademik
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Pilihan Jenjang <span class="text-red-500">*</span></label>
                        <!-- Pilihan Jenjang (Custom Premium Dropdown) -->
                        <div x-data="{ 
                            open: false,
                            jenjang: '<?= $biodata['jenjang'] ?? '' ?>',
                            options: [
                                { label: 'MITQ II RAUDLATUL FALAH', value: 'MITQ II RAUDLATUL FALAH' },
                                { label: 'SMPITQ RAUDLATUL FALAH', value: 'SMPITQ RAUDLATUL FALAH' },
                                { label: 'SMAITQ RAUDLATUL FALAH', value: 'SMAITQ RAUDLATUL FALAH' }
                            ],
                            get selectedLabel() {
                                return this.options.find(o => o.value == this.jenjang)?.label || '-- Pilih Jenjang --'
                            }
                        }">
                            <input type="hidden" name="jenjang" :value="jenjang" required>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors z-10">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 14l9-5-9-5-9 5 9 5z" /><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                                </div>
                                <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full text-left pl-11 pr-10 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm transition-all bg-white text-sm font-bold text-gray-700 flex items-center justify-between">
                                    <span x-text="selectedLabel"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>

                                <div x-show="open" x-transition x-cloak
                                     class="absolute z-[40] w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                    <template x-for="item in options" :key="item.value">
                                        <div @click="jenjang = item.value; open = false" 
                                             class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all font-bold"
                                             :class="jenjang == item.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                            <span x-text="item.label" class="text-xs"></span>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                                 :class="jenjang == item.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                                <div x-show="jenjang == item.value" x-transition class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Sekolah Asal <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <input type="text" name="asal_sekolah" required value="<?= htmlspecialchars($biodata['asal_sekolah'] ?? '') ?>" placeholder="Nama Sekolah Sebelumnya" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm transition-all bg-white text-sm font-medium">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmen 2: Data Santri -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4">
            <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Segmen 2: Data Pribadi Santri
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Nama Lengkap (Sesuai Ijazah) <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <input type="text" name="name" required value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm bg-white font-bold text-sm text-gray-800">
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Nama Panggilan <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                            </div>
                            <input type="text" name="nama_panggilan" required value="<?= htmlspecialchars($biodata['nama_panggilan'] ?? '') ?>" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium">
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">NISN (Jika ada)</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m4 16l4-16M6 9h14M4 15h14" /></svg>
                            </div>
                            <input type="text" name="nisn" value="<?= htmlspecialchars($biodata['nisn'] ?? '') ?>" placeholder="Nomor Induk Siswa Nasional" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium">
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <!-- Jenis Kelamin (Custom Premium Dropdown) -->
                        <div x-data="{ 
                            open: false,
                            jk: '<?= $biodata['jk'] ?? 'L' ?>',
                            options: [
                                { label: 'Laki-laki (Putra)', value: 'L' },
                                { label: 'Perempuan (Putri)', value: 'P' }
                            ],
                            get selectedLabel() {
                                return this.options.find(o => o.value == this.jk)?.label || '-- Pilih Jenjang --'
                            }
                        }">
                            <input type="hidden" name="jk" :value="jk" required>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors z-10">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                                <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full text-left pl-11 pr-10 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm transition-all bg-white text-sm font-bold text-gray-700 flex items-center justify-between">
                                    <span x-text="selectedLabel"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>

                                <div x-show="open" x-transition x-cloak
                                     class="absolute z-[40] w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                    <template x-for="item in options" :key="item.value">
                                        <div @click="jk = item.value; open = false" 
                                             class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all font-bold"
                                             :class="jk == item.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                            <span x-text="item.label" class="text-xs"></span>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                                 :class="jk == item.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                                <div x-show="jk == item.value" x-transition class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Tempat Lahir <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <input type="text" name="tempat_lahir" required value="<?= htmlspecialchars($biodata['tempat_lahir'] ?? '') ?>" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium">
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <input type="text" name="tgl_lahir" required value="<?= htmlspecialchars($biodata['tgl_lahir'] ?? '') ?>" class="datepicker w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm bg-white cursor-pointer text-sm font-medium">
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Kewarganegaraan <span class="text-red-500">*</span></label>
                        <div class="flex gap-4 p-1.5 bg-white rounded-2xl border border-gray-200">
                            <label class="flex items-center cursor-pointer px-4 py-2 hover:bg-emerald-50 rounded-xl transition-all group flex-1">
                                <input type="radio" name="kewarganegaraan" value="WNI" <?= ($biodata['kewarganegaraan'] ?? 'WNI') == 'WNI' ? 'checked' : '' ?> class="text-emerald-600 focus:ring-emerald-500">
                                <span class="ml-2 text-sm font-bold text-gray-600 group-hover:text-emerald-700">WNI</span>
                            </label>
                            <label class="flex items-center cursor-pointer px-4 py-2 hover:bg-emerald-50 rounded-xl transition-all group flex-1">
                                <input type="radio" name="kewarganegaraan" value="WNA" <?= ($biodata['kewarganegaraan'] ?? '') == 'WNA' ? 'checked' : '' ?> class="text-emerald-600 focus:ring-emerald-500">
                                <span class="ml-2 text-sm font-bold text-gray-600 group-hover:text-emerald-700">WNA</span>
                            </label>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Jumlah Saudara <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <input type="number" name="jumlah_saudara" required value="<?= htmlspecialchars($biodata['jumlah_saudara'] ?? 0) ?>" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium">
                        </div>
                    </div>
                    <div class="col-span-1 lg:col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute top-4 left-4 pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            </div>
                            <textarea name="alamat_lengkap" required rows="3" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium" placeholder="Jalan, Desa/Kelurahan, Kecamatan, Kota/Kabupaten, Kode Pos."><?= htmlspecialchars($biodata['alamat_lengkap'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmen 3: Data Wali -->
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4">
            <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    Segmen 3: Data Orang Tua / Wali
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Nama Ayah <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <input type="text" name="nama_ayah" required value="<?= htmlspecialchars($biodata['nama_ayah'] ?? '') ?>" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium">
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Nama Ibu <span class="text-red-500">*</span></label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <input type="text" name="nama_ibu" required value="<?= htmlspecialchars($biodata['nama_ibu'] ?? '') ?>" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium">
                        </div>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">No. HP/WhatsApp Orang Tua <span class="text-red-500">*</span></label>
                        <div class="relative group" x-data="{ 
                            formatNumber(val) {
                                let x = val.replace(/\D/g, '').match(/(\d{0,4})(\d{0,4})(\d{0,4})(\d{0,4})/);
                                return !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '');
                            }
                        }">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            </div>
                            <input type="text" name="wa_ortu" required value="<?= htmlspecialchars($biodata['wa_ortu'] ?? ($biodata['no_wa'] ?? '')) ?>" 
                                   @input="$el.value = formatNumber($el.value)"
                                   class="block w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium" placeholder="08xx-xxxx-xxxx">
                        </div>
                        <p class="text-[0.65rem] text-gray-500 mt-2 italic px-1">Pastikan nomor aktif WhatsApp untuk pengumuman lebih lanjut.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segmen 4: Berkas -->
        <div x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4">
            <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    Segmen 4: Unggah Berkas Dokumen
                </h3>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                    <!-- Foto Preview Space -->
                    <div class="lg:col-span-1 flex flex-col items-center justify-center p-8 bg-white rounded-3xl border-2 border-dashed border-gray-100 hover:border-emerald-200 transition-all group relative overflow-hidden h-full">
                        <div class="absolute inset-0 bg-emerald-50/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <template x-if="photoPreview">
                            <div class="relative z-10 w-40 h-52 group/photo">
                                <img :src="photoPreview" class="w-full h-full object-cover rounded-2xl shadow-2xl border-4 border-white transition-all transform group-hover/photo:scale-[1.02]">
                                <div class="absolute -top-3 -right-3 bg-emerald-500 text-white p-2 rounded-full shadow-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </div>
                            </div>
                        </template>
                        <template x-if="!photoPreview">
                            <div class="z-10 w-40 h-52 bg-gray-50 rounded-2xl flex flex-col items-center justify-center mb-0 text-gray-300 border-2 border-gray-50 border-dashed">
                                <svg class="w-16 h-16 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span class="text-[10px] font-bold uppercase tracking-widest opacity-50">PRATINJAU FOTO</span>
                            </div>
                        </template>
                        
                        <div class="mt-8 text-center relative z-10">
                            <label class="cursor-pointer inline-flex items-center bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3 px-8 rounded-2xl shadow-xl shadow-emerald-100 transition-all transform hover:-translate-y-1 active:scale-95 text-xs uppercase tracking-widest">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                Pilih Pas Foto 3x4
                                <input type="file" name="file_foto" @change="updatePreview" accept="image/*" class="hidden" <?= $biodata['file_foto'] ? '' : 'required' ?>>
                            </label>
                            <p class="text-[10px] text-gray-400 mt-4 font-bold lowercase italic tracking-wide">Maksimal 2MB. Background Merah/Biru.</p>
                            <?php if($biodata['file_foto']): ?>
                                <div class="mt-4 inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                                    <svg class="w-3 h-3 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Berkas Tersimpan
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Other Files -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-emerald-200 transition-all group">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-800 mb-3 ml-1">Akta Kelahiran <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <input type="file" name="file_akta" class="w-full pl-11 pr-4 py-3 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-xs font-bold text-gray-500 file:hidden cursor-pointer transition-all" accept=".pdf,image/*" <?= $biodata['file_akta'] ? '' : 'required' ?>>
                                <?php if($biodata['file_akta']): ?>
                                    <div class="absolute inset-y-0 right-4 flex items-center">
                                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-emerald-200 transition-all group">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-800 mb-3 ml-1">KTP Kedua Orang Tua <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </div>
                                <input type="file" name="file_ktp" class="w-full pl-11 pr-4 py-3 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-xs font-bold text-gray-500 file:hidden cursor-pointer transition-all" accept=".pdf,image/*" <?= $biodata['file_ktp'] ? '' : 'required' ?>>
                                <?php if($biodata['file_ktp']): ?>
                                    <div class="absolute inset-y-0 right-4 flex items-center">
                                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:border-emerald-200 transition-all group">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-800 mb-3 ml-1">Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                </div>
                                <input type="file" name="file_kk" class="w-full pl-11 pr-4 py-3 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-xs font-bold text-gray-500 file:hidden cursor-pointer transition-all" accept=".pdf,image/*" <?= $biodata['file_kk'] ? '' : 'required' ?>>
                                <?php if($biodata['file_kk']): ?>
                                    <div class="absolute inset-y-0 right-4 flex items-center">
                                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-amber-50/50 p-6 rounded-3xl border-2 border-dashed border-amber-100">
                            <p class="text-[10px] font-bold text-amber-800 uppercase tracking-widest flex items-center mb-1">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Rekomendasi Format
                            </p>
                            <p class="text-[10px] text-amber-700 leading-relaxed font-medium">Berkas dapat berupa scan/foto yang jelas (Warna). Format: JPG, PNG, PDF. Maksimal 2MB per file.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center bg-white p-4 rounded-3xl border border-gray-100 shadow-xl shadow-gray-100 sticky bottom-6 z-20">
            <button type="button" x-show="step > 1" @click="step--" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-2xl transition-all flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </button>
            <div x-show="step === 1"></div> <!-- Spacer -->

            <button type="button" x-show="step < 4" @click="step++" class="px-10 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 transition-all flex items-center ml-auto">
                Lanjut
                <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </button>

            <button type="submit" x-show="step === 4" class="px-10 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-2xl shadow-emerald-200 transition-all transform hover:scale-105 active:scale-95 ml-auto flex items-center tracking-wide uppercase text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Simpan & Selesaikan
            </button>
        </div>
    </form>
</div>
