<?php ob_start(); ?>
<div class="min-h-screen flex items-center justify-center bg-emerald-50/50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Abstract decorations -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>
    
    <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-xl p-10 rounded-[2rem] shadow-2xl border border-white/50 relative z-10">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-emerald-200 mb-6 transform rotate-3 hover:rotate-0 transition-transform overflow-hidden">
                <?php if ($logo = get_pengaturan('app_logo')): ?>
                    <img src="<?= url($logo) ?>" class="max-w-full max-h-full object-contain">
                <?php else: ?>
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                <?php endif; ?>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Daftar Akun Baru
            </h2>
            <p class="mt-2 text-sm text-gray-500 font-medium">
                SIT-PSB PPRTQ Raudlatul Falah
            </p>
        </div>

        <?= display_flash_message() ?>

        <form class="mt-8 space-y-5" action="<?= url('auth/do-register') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            
            <div class="space-y-4">
                <!-- Nama -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <input id="name" name="name" type="text" required placeholder="Nama Lengkap" class="block w-full pl-11 pr-3 py-3.5 border border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 focus:bg-white transition-all shadow-sm" value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
                </div>

                <!-- Jenis Kelamin (Custom Premium Dropdown) -->
                <div class="relative group" x-data="{ 
                    open: false, 
                    value: '<?= htmlspecialchars($_SESSION['old']['jk'] ?? '') ?>',
                    options: [
                        { label: 'Laki-laki (Putra)', value: 'L' },
                        { label: 'Perempuan (Putri)', value: 'P' }
                    ],
                    get selectedLabel() {
                        return this.options.find(o => o.value == this.value)?.label || 'Pilih Jenis Kelamin'
                    }
                }">
                    <!-- Hidden input to store actual value for PHP $_POST -->
                    <input type="hidden" name="jk" :value="value" required>
                    
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors z-20">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>

                    <!-- Trigger Button -->
                    <button type="button" 
                            @click="open = !open" 
                            @click.away="open = false"
                            class="block w-full pl-11 pr-10 py-3.5 border border-gray-200 rounded-2xl text-sm text-left transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"
                            :class="value ? 'bg-white text-gray-800 font-medium' : 'bg-gray-50/50 text-gray-400'">
                        <span x-text="selectedLabel"></span>
                    </button>

                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 group-hover:text-emerald-500 transition-colors z-20">
                        <svg class="h-4 w-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </div>

                    <!-- Custom Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute z-[60] w-full mt-2 bg-white rounded-[2rem] shadow-2xl border border-emerald-50 overflow-hidden p-2"
                         x-cloak>
                        <template x-for="item in options" :key="item.value">
                            <div @click="value = item.value; open = false" 
                                 class="flex items-center justify-between px-6 py-4 rounded-[1.5rem] cursor-pointer transition-all hover:bg-emerald-50"
                                 :class="value == item.value ? 'bg-emerald-50/70' : ''">
                                <span x-text="item.label" 
                                      class="text-sm font-semibold transition-colors"
                                      :class="value == item.value ? 'text-emerald-700' : 'text-gray-600'"></span>
                                
                                <!-- Radio Indicator -->
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-300"
                                     :class="value == item.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                    <div x-show="value == item.value" 
                                         x-transition:enter="transition scale-0"
                                         x-transition:enter-start="scale-0"
                                         x-transition:enter-end="scale-100"
                                         class="w-2.5 h-2.5 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- WA -->
                <div class="relative group" x-data="{ 
                    formatNumber(val) {
                        let x = val.replace(/\D/g, '').match(/(\d{0,4})(\d{0,4})(\d{0,4})(\d{0,4})/);
                        return !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '') + (x[4] ? '-' + x[4] : '');
                    }
                }">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    </div>
                    <input id="no_wa" name="no_wa" type="text" required placeholder="No. WhatsApp (Contoh: 0857-xxxx-xxxx)" 
                           @input="$el.value = formatNumber($el.value)"
                           class="block w-full pl-11 pr-3 py-3.5 border border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 focus:bg-white transition-all shadow-sm">
                </div>

                <!-- Asal Sekolah -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <input id="asal_sekolah" name="asal_sekolah" type="text" required placeholder="Asal Sekolah" class="block w-full pl-11 pr-3 py-3.5 border border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 focus:bg-white transition-all shadow-sm">
                </div>

                <!-- Password -->
                <div x-data="{ show: false }" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required placeholder="Buat Password" class="block w-full pl-11 pr-12 py-3.5 border border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 focus:bg-white transition-all shadow-sm">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" @click="show = !show" title="Lihat Password">
                        <svg x-show="!show" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-cloak x-show="show" class="h-5 w-5 text-emerald-500 hover:text-emerald-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div x-data="{ showConf: false }" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <input id="password_confirm" name="password_confirm" :type="showConf ? 'text' : 'password'" required placeholder="Konfirmasi Password" class="block w-full pl-11 pr-12 py-3.5 border border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 focus:bg-white transition-all shadow-sm">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer" @click="showConf = !showConf">
                        <!-- Eye Open -->
                        <svg x-show="!showConf" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Closed -->
                        <svg x-cloak x-show="showConf" class="h-5 w-5 text-emerald-500 hover:text-emerald-700 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all shadow-md shadow-emerald-200 hover:shadow-lg hover:shadow-emerald-300">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-emerald-400 group-hover:text-emerald-300 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Daftar Sekarang
                </button>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-sm text-gray-500">
                    Sudah punya akun? 
                    <a href="<?= url('auth/login') ?>" class="font-bold text-emerald-600 hover:text-emerald-500 transition-colors">
                        Login di sini
                    </a>
                </p>
            </div>
        </form>
        <?php unset($_SESSION['old']); ?>
    </div>
</div>

<style>
    .animate-blob { animation: blob 7s infinite; }
    .animation-delay-2000 { animation-delay: 2s; }
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
</style>
<?php
$content = ob_get_clean();
$title = "Pendaftaran Santri - SIT-PSB";
require __DIR__ . '/../layouts/guest.php';
