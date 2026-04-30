<?php ob_start(); ?>
<div class="min-h-screen flex items-center justify-center bg-emerald-50/50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Abstract decorations -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>

    <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-xl p-10 rounded-[2rem] shadow-2xl border border-white/50 relative z-10">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-emerald-500 to-teal-400 rounded-[1.5rem] flex items-center justify-center shadow-lg shadow-emerald-200 mb-6 transform -rotate-3 hover:rotate-0 transition-transform overflow-hidden">
                <?php if ($logo = get_pengaturan('app_logo')): ?>
                    <img src="<?= url($logo) ?>" class="max-w-full max-h-full object-contain">
                <?php else: ?>
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                <?php endif; ?>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Selamat Datang
            </h2>
            <p class="mt-2 text-sm text-gray-500 font-medium">
                Login ke Portal SIT-PSB
            </p>
        </div>

        <?= display_flash_message() ?>

        <form class="mt-8 space-y-5" action="<?= url('auth/do-login') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="space-y-4">
                <!-- Username -->
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <input id="username" name="username" type="text" required placeholder="Username / No Tes" class="block w-full pl-11 pr-3 py-3.5 border border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 focus:bg-white transition-all shadow-sm">
                </div>

                <!-- Password -->
                <div x-data="{ show: false }" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <input id="password" name="password" :type="show ? 'text' : 'password'" required placeholder="Password" class="block w-full pl-11 pr-12 py-3.5 border border-gray-200 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 focus:bg-white transition-all shadow-sm">
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
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all shadow-md shadow-emerald-200 hover:shadow-lg hover:shadow-emerald-300">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-emerald-400 group-hover:text-emerald-300 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    Login ke Akun Saya
                </button>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500 font-medium">
                    Belum punya akun? 
                    <a href="<?= url('auth/register') ?>" class="font-bold text-emerald-600 hover:text-emerald-500 transition-colors">
                        Daftar sekarang
                    </a>
                </p>
            </div>

            <div class="text-center mt-6 pt-6 border-t border-gray-100">
                <a href="<?= url('') ?>" class="inline-flex items-center space-x-2 text-[10px] font-black text-gray-400 hover:text-emerald-600 transition-all tracking-[0.2em] uppercase">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    <span>Beranda</span>
                </a>
            </div>
        </form>
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
$title = "Login - SIT-PSB / PPRTQ Raudlatul Falah";
require __DIR__ . '/../layouts/guest.php';
