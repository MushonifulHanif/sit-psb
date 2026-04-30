<?php ob_start(); ?>
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Landing Page</h1>
        <p class="text-gray-600 mt-1">Kelola Hero, Navigasi, dan Section konten halaman utama.</p>
    </div>
    <button @click="$dispatch('open-modal-section', { action: 'add' })" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-2xl shadow-lg shadow-emerald-100 transition flex items-center transform active:scale-95">
        <svg class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
        Tambah Section Baru
    </button>
</div>

<div class="space-y-8">
    
    <!-- 0. Branding & Identity Settings -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center">
            <div class="p-2.5 bg-emerald-100 rounded-xl mr-4">
                <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h2 class="text-xl font-black text-gray-800">Branding & Identitas</h2>
        </div>
        <form action="<?= url('admin/update-konten-landing') ?>" method="POST" enctype="multipart/form-data" class="p-8">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Logo Aplikasi -->
                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-700">Logo Utama Aplikasi</label>
                    <div class="flex items-center space-x-6 bg-gray-50 p-6 rounded-3xl border border-gray-100 group hover:border-emerald-200 transition-all">
                        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center border border-gray-100 shadow-sm overflow-hidden">
                            <?php if(!empty($konten['app_logo'])): ?>
                                <img src="<?= url($konten['app_logo']) ?>" class="max-w-full max-h-full object-contain">
                            <?php else: ?>
                                <div class="bg-emerald-600 w-12 h-12 rounded-xl flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="app_logo_file" accept="image/*" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                            <p class="text-[10px] text-gray-400 mt-2">PNG/JPG. Rekomendasi 512x512px.</p>
                        </div>
                    </div>
                </div>

                <!-- Favicon -->
                <div class="space-y-4">
                    <label class="block text-sm font-bold text-gray-700">Favicon (Ikon Tab)</label>
                    <div class="flex items-center space-x-6 bg-gray-50 p-6 rounded-3xl border border-gray-100 group hover:border-blue-200 transition-all">
                        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center border border-gray-100 shadow-sm overflow-hidden">
                            <?php if(!empty($konten['app_favicon'])): ?>
                                <img src="<?= url($konten['app_favicon']) ?>" class="w-10 h-10 object-contain">
                            <?php else: ?>
                                <img src="<?= asset('img/favicon.png') ?>" class="w-10 h-10 object-contain">
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="app_favicon_file" accept="image/*" class="text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-[10px] text-gray-400 mt-2">Format ICO/PNG. Rekomendasi 32x32px.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-gray-900 hover:bg-black text-white font-black py-4 px-10 rounded-2xl shadow-xl shadow-gray-200 transition-all transform active:scale-95 flex items-center tracking-widest text-xs">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    UPDATE IDENTITAS
                </button>
            </div>
        </form>
    </div>

    <!-- 1. Hero & Footer Settings -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center">
            <div class="p-2.5 bg-blue-100 rounded-xl mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h2 class="text-xl font-black text-gray-800">Visual Hero & Link Footer</h2>
        </div>
        <form action="<?= url('admin/update-konten-landing') ?>" method="POST" enctype="multipart/form-data" class="p-8">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Hero Judul Utama</label>
                        <input type="text" name="konten[hero_judul]" value="<?= htmlspecialchars($konten['hero_judul'] ?? '') ?>" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50/50 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Hero Subtulisan (Deskripsi)</label>
                        <textarea name="konten[hero_subjudul]" rows="4" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50/50 focus:bg-white transition-all"><?= htmlspecialchars($konten['hero_subjudul'] ?? '') ?></textarea>
                    </div>

                    <!-- Hero Badge Settings -->
                    <div class="p-6 bg-emerald-50 rounded-[2rem] border border-emerald-100 space-y-4">
                        <h4 class="text-xs font-black text-emerald-800 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                            Badge Highlight (Top Bar)
                        </h4>
                        <div>
                            <label class="block text-[10px] font-bold text-emerald-700 mb-1 uppercase">Teks Badge</label>
                            <input type="text" name="konten[hero_badge_text]" value="<?= htmlspecialchars($konten['hero_badge_text'] ?? 'PENDAFTARAN TAHUN AJARAN 2026 TELAH DIBUKA') ?>" placeholder="Misal: KUOTA TERBATAS!" class="w-full border-emerald-100 rounded-xl focus:ring-emerald-500 px-4 py-2.5 border text-sm">
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Style Color Dropdown -->
                            <div>
                                <label class="block text-[10px] font-bold text-emerald-700 mb-1 uppercase">Gaya Warna</label>
                                <div class="relative" x-data="{ 
                                    open: false, 
                                    value: '<?= $konten['hero_badge_style'] ?? 'emerald' ?>',
                                    options: [
                                        { label: 'Emerald (Hijau)', value: 'emerald' },
                                        { label: 'Blue (Biru)', value: 'blue' },
                                        { label: 'Amber (Oranye)', value: 'amber' },
                                        { label: 'Rose (Merah Muda)', value: 'rose' }
                                    ],
                                    get selectedLabel() { return this.options.find(o => o.value == this.value)?.label || 'Pilih Gaya' }
                                }">
                                    <input type="hidden" name="konten[hero_badge_style]" :value="value">
                                    <button type="button" @click="open = !open" @click.away="open = false" class="w-full flex items-center justify-between px-4 py-2.5 border border-emerald-100 rounded-xl text-xs bg-white font-bold text-emerald-800 focus:ring-2 focus:ring-emerald-500/20">
                                        <span x-text="selectedLabel"></span>
                                        <svg class="w-3 h-3 text-emerald-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="open" x-transition x-cloak class="absolute z-50 left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-emerald-50 overflow-hidden p-1">
                                        <template x-for="opt in options">
                                            <div @click="value = opt.value; open = false" class="px-4 py-2 rounded-lg cursor-pointer hover:bg-emerald-50 text-[11px] font-bold" :class="value == opt.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                                <span x-text="opt.label"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Font Size Dropdown -->
                            <div>
                                <label class="block text-[10px] font-bold text-emerald-700 mb-1 uppercase">Ukuran Font</label>
                                <div class="relative" x-data="{ 
                                    open: false, 
                                    value: '<?= $konten['hero_badge_size'] ?? 'text-xs' ?>',
                                    options: [
                                        { label: 'Kecil (XS)', value: 'text-[10px]' },
                                        { label: 'Normal (SM)', value: 'text-xs' },
                                        { label: 'Besar (MD)', value: 'text-sm' },
                                        { label: 'Sangat Besar (LG)', value: 'text-base' }
                                    ],
                                    get selectedLabel() { return this.options.find(o => o.value == this.value)?.label || 'Pilih Ukuran' }
                                }">
                                    <input type="hidden" name="konten[hero_badge_size]" :value="value">
                                    <button type="button" @click="open = !open" @click.away="open = false" class="w-full flex items-center justify-between px-4 py-2.5 border border-emerald-100 rounded-xl text-xs bg-white font-bold text-emerald-800 focus:ring-2 focus:ring-emerald-500/20">
                                        <span x-text="selectedLabel"></span>
                                        <svg class="w-3 h-3 text-emerald-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="open" x-transition x-cloak class="absolute z-50 left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-emerald-50 overflow-hidden p-1">
                                        <template x-for="opt in options">
                                            <div @click="value = opt.value; open = false" class="px-4 py-2 rounded-lg cursor-pointer hover:bg-emerald-50 text-[11px] font-bold" :class="value == opt.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                                <span x-text="opt.label"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Animation Dropdown -->
                            <div class="col-span-2 lg:col-span-1">
                                <label class="block text-[10px] font-bold text-emerald-700 mb-1 uppercase">Animasi</label>
                                <div class="relative" x-data="{ 
                                    open: false, 
                                    value: '<?= $konten['hero_badge_animation'] ?? 'ping' ?>',
                                    options: [
                                        { label: 'Ping Dot (Default)', value: 'ping' },
                                        { label: 'Pulse Glow', value: 'pulse' },
                                        { label: 'Bounce Soft', value: 'bounce' },
                                        { label: 'Tanpa Animasi', value: 'none' }
                                    ],
                                    get selectedLabel() { return this.options.find(o => o.value == this.value)?.label || 'Pilih Animasi' }
                                }">
                                    <input type="hidden" name="konten[hero_badge_animation]" :value="value">
                                    <button type="button" @click="open = !open" @click.away="open = false" class="w-full flex items-center justify-between px-4 py-2.5 border border-emerald-100 rounded-xl text-xs bg-white font-bold text-emerald-800 focus:ring-2 focus:ring-emerald-500/20">
                                        <span x-text="selectedLabel"></span>
                                        <svg class="w-3 h-3 text-emerald-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="open" x-transition x-cloak class="absolute z-50 left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-emerald-50 overflow-hidden p-1">
                                        <template x-for="opt in options">
                                            <div @click="value = opt.value; open = false" class="px-4 py-2 rounded-lg cursor-pointer hover:bg-emerald-50 text-[11px] font-bold" :class="value == opt.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                                <span x-text="opt.label"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Cinematic Gallery Section -->
                    <div class="mt-8 p-8 bg-gray-900 rounded-[2.5rem] border border-gray-800 space-y-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    Hero Cinematic Gallery
                                </h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase mt-1">Multi-background dengan efek Ken Burns</p>
                            </div>
                            <input type="file" name="hero_multi_files[]" multiple class="hidden" id="multi_upload" onchange="this.form.submit()">
                            <label for="multi_upload" class="bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black px-5 py-2.5 rounded-xl cursor-pointer transition-all shadow-lg shadow-emerald-900/20">
                                TAMBAH GAMBAR
                            </label>
                        </div>

                        <?php 
                            $images = json_decode($konten['hero_images_json'] ?? '[]', true) ?: [];
                        ?>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <?php foreach($images as $path): ?>
                                <div class="relative group aspect-video rounded-2xl overflow-hidden border border-white/5 bg-black">
                                    <img src="<?= asset($path) ?>" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition-opacity">
                                    <form action="<?= url('admin/delete-hero-image') ?>" method="POST" class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/60">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                        <input type="hidden" name="path" value="<?= $path ?>">
                                        <button type="submit" onclick="return confirm('Hapus gambar ini dari galeri?')" class="p-3 bg-red-500 text-white rounded-2xl hover:bg-red-600 shadow-xl transform group-hover:scale-110 transition-all">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if(empty($images)): ?>
                                <div class="col-span-3 py-10 border-2 border-dashed border-gray-800 rounded-3xl flex flex-col items-center justify-center text-gray-600">
                                    <svg class="w-10 h-10 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="text-[10px] font-bold uppercase tracking-widest">Belum ada gambar galeri</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Stats Manager Section -->
                    <div class="mt-8 p-6 bg-blue-50 rounded-[2rem] border border-blue-100 space-y-4" 
                         x-data="{ 
                            stats: <?= htmlspecialchars($konten['hero_stats_json'] ?? '[]') ?>,
                            icons: ['users', 'office-building', 'book-open', 'emoji-happy', 'academic-cap', 'lightning-bolt']
                         }">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-black text-blue-800 uppercase tracking-widest flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                Hero Stats Bar (4 Items)
                            </h4>
                            <div class="relative w-24" x-data="{ 
                                open: false, 
                                value: '<?= $konten['hero_stats_style'] ?? 'emerald' ?>',
                                options: [
                                    { label: 'Hijau', value: 'emerald' },
                                    { label: 'Biru', value: 'blue' },
                                    { label: 'Gelap', value: 'dark' }
                                ],
                                get selectedLabel() { return this.options.find(o => o.value == this.value)?.label || 'Warna' }
                            }">
                                <input type="hidden" name="konten[hero_stats_style]" :value="value">
                                <button type="button" @click="open = !open" @click.away="open = false" class="w-full flex items-center justify-between px-2 py-1 border border-blue-100 rounded-lg text-[10px] bg-white font-bold text-blue-700">
                                    <span x-text="selectedLabel"></span>
                                    <svg class="w-3 h-3 text-blue-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="open" x-transition x-cloak class="absolute z-50 left-0 right-0 mt-1 bg-white rounded-xl shadow-xl border border-blue-50 overflow-hidden p-1">
                                    <template x-for="opt in options">
                                        <div @click="value = opt.value; open = false" class="px-3 py-1.5 rounded-lg cursor-pointer hover:bg-blue-50 text-[10px] font-bold" :class="value == opt.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600'">
                                            <span x-text="opt.label"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="konten[hero_stats_json]" :value="JSON.stringify(stats)">
                        
                        <div class="grid grid-cols-1 gap-4">
                            <template x-for="(item, index) in stats" :key="index">
                                <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-blue-100">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <!-- Icon Selector -->
                                        <div class="relative" x-data="{ openIcon: false }">
                                            <button type="button" @click="openIcon = !openIcon" @click.away="openIcon = false" class="text-xs bg-blue-50 rounded-lg px-3 py-2 font-bold text-blue-600 flex items-center gap-1 border border-blue-100/50">
                                                <span x-text="item.icon"></span>
                                                <svg class="w-3 h-3 transition-transform" :class="openIcon ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                            </button>
                                            <div x-show="openIcon" x-transition x-cloak class="absolute z-[60] left-0 mt-1 bg-white rounded-xl shadow-xl border border-blue-50 overflow-hidden p-1 min-w-[120px]">
                                                <template x-for="ic in icons">
                                                    <div @click="item.icon = ic; openIcon = false" class="px-3 py-1.5 rounded-lg cursor-pointer hover:bg-blue-50 text-[10px] font-bold" :class="item.icon == ic ? 'bg-blue-50 text-blue-700' : 'text-gray-600'">
                                                        <span x-text="ic"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Number & Suffix -->
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="item.num" placeholder="Angka" class="w-16 text-xs font-bold border-none bg-gray-50 rounded-lg px-2 py-2 focus:ring-emerald-500/20 focus:bg-white transition-all">
                                            <input type="text" x-model="item.suffix" placeholder="+" class="w-10 text-xs font-bold border-none bg-gray-50 rounded-lg px-2 py-2 focus:ring-emerald-500/20 focus:bg-white transition-all">
                                        </div>

                                        <!-- Label (Flexible) -->
                                        <div class="flex-grow min-w-[120px]">
                                            <input type="text" x-model="item.label" placeholder="Label" class="w-full text-xs font-black border-none bg-gray-50 rounded-lg px-3 py-2 focus:ring-emerald-500/20 focus:bg-white transition-all uppercase tracking-tight">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6 pt-10 border-t border-gray-100">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 tracking-tight">Website Utama</label>
                    <input type="text" name="konten[footer_website]" value="<?= htmlspecialchars($konten['footer_website'] ?? '') ?>" placeholder="https://..." class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 tracking-tight">Email Official</label>
                    <input type="email" name="konten[footer_email]" value="<?= htmlspecialchars($konten['footer_email'] ?? '') ?>" placeholder="info@pesantren.com" class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 tracking-tight">Instagram Link</label>
                    <input type="text" name="konten[footer_instagram]" value="<?= htmlspecialchars($konten['footer_instagram'] ?? '') ?>" placeholder="https://instagram.com/..." class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 tracking-tight">Facebook Link</label>
                    <input type="text" name="konten[footer_facebook]" value="<?= htmlspecialchars($konten['footer_facebook'] ?? '') ?>" placeholder="https://facebook.com/..." class="w-full border-gray-200 rounded-xl focus:ring-emerald-500 px-4 py-3 border text-sm">
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-gray-900 hover:bg-black text-white font-black py-4 px-10 rounded-2xl shadow-xl shadow-gray-200 transition-all transform active:scale-95 flex items-center tracking-widest text-xs">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    UPDATE VISUAL HERO
                </button>
            </div>
        </form>
    </div>

    <!-- 1.5 SEO Metadata Settings -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center">
            <div class="p-2.5 bg-emerald-100 rounded-xl mr-4">
                <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <h2 class="text-xl font-black text-gray-800">SEO Metadata Settings (Google Friendly)</h2>
        </div>
        <form action="<?= url('admin/update-konten-landing') ?>" method="POST" class="p-8">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 tracking-tight">Judul Halaman (SEO Title)</label>
                    <input type="text" name="konten[seo_title]" value="<?= htmlspecialchars($konten['seo_title'] ?? '') ?>" placeholder="Misal: PSB Online SIT Raudlatul Falah - Pendaftaran Santri Baru" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50/50 focus:bg-white transition-all">
                    <p class="text-[10px] text-gray-400 mt-2 font-medium italic">Saran: Gunakan 50-60 karakter agar tidak terpotong di Google.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 tracking-tight">Deskripsi Halaman (Meta Description)</label>
                    <textarea name="konten[seo_description]" rows="4" placeholder="Jelaskan secara singkat tentang sekolah/pondok Anda..." class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50/50 focus:bg-white transition-all"><?= htmlspecialchars($konten['seo_description'] ?? '') ?></textarea>
                    <p class="text-[10px] text-gray-400 mt-2 font-medium italic">Saran: Gunakan 150-160 karakter untuk ringkasan pencarian.</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 tracking-tight">Kata Kunci (SEO Keywords)</label>
                    <textarea name="konten[seo_keywords]" rows="4" placeholder="Misal: psb online, pesantren, raudlatul falah, pendaftaran santri baru..." class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50/50 focus:bg-white transition-all"><?= htmlspecialchars($konten['seo_keywords'] ?? '') ?></textarea>
                    <p class="text-[10px] text-gray-400 mt-2 font-medium italic">Pisahkan setiap kata kunci dengan tanda koma (,).</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-10 rounded-2xl shadow-xl shadow-emerald-100 transition-all transform active:scale-95 flex items-center tracking-widest text-xs">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    SIMPAN PENGATURAN SEO
                </button>
            </div>
        </form>
    </div>

    <!-- 2. Dynamic Sections List -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-2.5 bg-emerald-100 rounded-xl mr-4">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <h2 class="text-xl font-black text-gray-800">Daftar Section (Dynamic Content)</h2>
            </div>
            <span class="text-xs font-bold text-gray-400 bg-gray-200/50 px-3 py-1 rounded-full uppercase tracking-widest">Total: <?= count($sections) ?> Section</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 text-[10px] sm:text-xs uppercase tracking-[0.2em] border-b border-gray-50">
                        <th class="px-8 py-5 font-black">JUDUL & TAG</th>
                        <th class="px-8 py-5 font-black">TIPE KONTEN</th>
                        <th class="px-8 py-5 font-black">STATUS</th>
                        <th class="px-8 py-5 font-black text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if(empty($sections)): ?>
                        <tr><td colspan="4" class="px-8 py-16 text-center text-gray-400 italic font-medium">Belum ada section yang dibuat. Tambahkan sekarang untuk mengisi landing page.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($sections as $s): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-6">
                            <div class="font-black text-gray-900 text-lg"><?= htmlspecialchars($s['title']) ?></div>
                            <div class="text-[10px] bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg font-black inline-block mt-2 tracking-tighter uppercase">MENU TAG: <?= htmlspecialchars($s['tag']) ?></div>
                        </td>
                        <td class="px-8 py-6">
                            <?php if($s['type'] == 'video'): ?>
                                <span class="bg-purple-100 text-purple-700 text-[10px] px-3 py-1.5 rounded-xl font-black flex items-center w-fit uppercase tracking-wider shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M10 15l5.197-3L10 9v6z" /><path d="M21.58 7.187a2.316 2.316 0 00-1.64-1.655C18.483 5 12 5 12 5s-6.483 0-7.94.532c-.803.228-1.437.863-1.665 1.655C2 8.632 2 12 2 12s0 3.368.532 4.813c.228.792.862 1.427 1.665 1.655C5.517 19 12 19 12 19s6.483 0 7.94-.532c.803-.228 1.437-.863 1.64-1.655C22 15.368 22 12 22 12s0-3.368-.42-4.813z" /></svg>
                                    Video Embed
                                </span>
                            <?php else: ?>
                                <span class="bg-blue-100 text-blue-700 text-[10px] px-3 py-1.5 rounded-xl font-black w-fit flex items-center uppercase tracking-wider shadow-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    Rich Text Editor
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6">
                            <a href="<?= url('admin/toggle-landing-section/' . $s['id']) ?>" 
                               class="<?= $s['is_active'] ? 'bg-emerald-500' : 'bg-gray-300' ?> relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none ring-offset-2 focus:ring-2 focus:ring-emerald-500 shadow-inner">
                                <span class="<?= $s['is_active'] ? 'translate-x-5' : 'translate-x-0' ?> pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-xl ring-0 transition duration-200 ease-in-out"></span>
                            </a>
                        </td>
                        <td class="px-8 py-6 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-3">
                                <button @click="$dispatch('open-modal-section', { action: 'edit', data: <?= htmlspecialchars(json_encode($s)) ?> })" class="text-emerald-500 hover:text-white p-2.5 hover:bg-emerald-500 rounded-xl transition-all shadow-sm hover:shadow-lg hover:shadow-emerald-100">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <a href="<?= url('admin/delete-landing-section/' . $s['id']) ?>" onclick="return confirmLink(event, 'Hapus Section?', 'Hapus section ini? Tindakan ini tidak dapat dibatalkan.')" class="text-red-400 hover:text-white p-2.5 hover:bg-red-500 rounded-xl transition-all shadow-sm hover:shadow-lg hover:shadow-red-100">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit Section -->
<div x-data="{ 
         open: false, 
         action: 'add', 
         form: { title: '', tag: '', content: '', type: 'text', video_url: '', order_num: 0 },
         quill: null,
         init() {
            this.$watch('open', value => {
                if(value) {
                    this.$nextTick(() => { this.setupEditor(); });
                }
            });
         },
         setupEditor() {
            const container = this.$refs.quillContainer;
            if(!container) return;
            
            // Re-render prevention logic if needed, but here we rebuild to ensure clean slate
            container.innerHTML = '';
            const editorDiv = document.createElement('div');
            container.appendChild(editorDiv);
            
            this.quill = new Quill(editorDiv, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['clean']
                    ]
                }
            });
            
            this.quill.root.innerHTML = this.form.content || '';
            this.quill.on('text-change', () => {
                this.form.content = this.quill.root.innerHTML;
            });
         }
     }" 
     @open-modal-section.window="open = true; action = $event.detail.action; form = $event.detail.data || { title: '', tag: '', content: '', type: 'text', video_url: '', order_num: 0 }"
     x-show="open" 
     class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900/90 backdrop-blur-sm" @click="open = false"></div>
        <div class="inline-block align-bottom bg-white rounded-[3rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100">
            <form :action="action == 'add' ? '<?= url('admin/store-landing-section') ?>' : '<?= url('admin/update-landing-section') ?>'" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <input type="hidden" name="id" :value="form.id" x-if="action == 'edit'">
                
                <div class="bg-gray-50/50 px-10 py-8 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900" x-text="action == 'add' ? 'Tambah Section Baru' : 'Edit Section Konten'"></h3>
                        <p class="text-sm text-gray-500 mt-1">Gunakan editor ini untuk menyusun konten Landing Page secara dinamis.</p>
                    </div>
                    <button type="button" @click="open = false" class="bg-white p-3 rounded-2xl shadow-sm text-gray-400 hover:text-red-500 transition-all border border-gray-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-10 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">JUDUL SECTION</label>
                            <input type="text" name="title" x-model="form.title" required placeholder="Misal: Kurikulum Pesatren" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">TAG MENU (SINGLE WORD)</label>
                            <input type="text" name="tag" x-model="form.tag" required placeholder="Misal: Kurikulum" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                        <!-- Tipe Section (Custom Premium Dropdown) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">TIPE SECTION</label>
                            <div class="relative group" x-data="{ 
                                open: false, 
                                options: [
                                    { label: 'Rich Text Editor (WYSIWYG)', value: 'text' },
                                    { label: 'Video Tutorial (YouTube Embed)', value: 'video' }
                                ],
                                get selectedLabel() {
                                    return this.options.find(o => o.value == form.type)?.label || 'Pilih Tipe'
                                }
                            }">
                                <input type="hidden" name="type" :value="form.type" required>
                                <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50 hover:bg-white font-bold text-emerald-800">
                                    <span x-text="selectedLabel"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>

                                <div x-show="open" x-transition x-cloak
                                     class="absolute z-20 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                    <template x-for="item in options" :key="item.value">
                                        <div @click="form.type = item.value; open = false" 
                                             class="flex items-center justify-between px-5 py-4 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all"
                                             :class="form.type == item.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                            <span x-text="item.label" class="text-xs font-bold uppercase tracking-tight"></span>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                                 :class="form.type == item.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                                <div x-show="form.type == item.value" x-transition class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-widest text-[10px]">URUTAN TAMPIL (POSISI)</label>
                            <input type="number" name="order_num" x-model="form.order_num" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500 px-5 py-4 border bg-gray-50">
                        </div>
                    </div>

                    <div x-show="form.type == 'video'" x-transition class="p-6 bg-purple-50 rounded-3xl border border-purple-100">
                        <label class="block text-sm font-bold text-purple-700 mb-2 uppercase tracking-widest text-[10px]">LINK YOUTUBE EMBED URL</label>
                        <input type="text" name="video_url" x-model="form.video_url" placeholder="https://www.youtube.com/embed/XXXXXXX" class="w-full border-purple-200 rounded-2xl focus:ring-purple-500 px-5 py-4 border bg-white">
                        <p class="text-[10px] text-purple-600 mt-3 font-medium italic">Penting: Gunakan format embed youtube (bukan link berbagi biasa). Ambil dari Share > Embed.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-widest text-[10px]">KONTEN DESKRIPSI (WYSIWYG EDITOR)</label>
                        <!-- Quill Container wrapper to avoid height 0 issues -->
                        <div class="min-h-[300px] border border-gray-200 rounded-3xl overflow-hidden bg-white shadow-inner">
                            <div x-ref="quillContainer" class="min-h-[260px]"></div>
                        </div>
                        <input type="hidden" name="content" x-model="form.content">
                    </div>
                </div>

                <div class="bg-gray-50/50 px-10 py-8 flex justify-end space-x-4 border-t border-gray-100">
                    <button type="button" @click="open = false" class="px-8 py-4 text-sm font-black text-gray-400 hover:text-gray-900 transition-colors tracking-widest">BATAL</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-12 rounded-2xl shadow-xl shadow-emerald-100 transition-all transform active:scale-95 tracking-widest text-xs">
                        SIMPAN SECTION
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="h-32"></div>

<?php
$content = ob_get_clean();
$title = "Advanced Landing Manager - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
?>
