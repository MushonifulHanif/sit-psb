<header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-6 z-40">
    <div class="flex items-center">
        <!-- Toggle Menu (Desktop & Mobile) -->
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-emerald-600 p-2 rounded-2xl hover:bg-emerald-50 transition-all mr-4">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
            </svg>
        </button>
        
        <h2 class="text-lg font-black text-gray-900 tracking-tight flex items-center">
            <span class="lg:hidden">SIT-PSB Dashboard</span>
            <span class="hidden lg:inline" x-text="['','Informasi Profil','Ujian Online','Hasil Seleksi','Daftar Ulang','Pesanan Seragam'][activeTab]"></span>
        </h2>
    </div>

    <div class="flex items-center space-x-2 sm:space-x-4">
        <!-- Notifikasi Bell -->
        <div x-data="notifications()" x-init="init()" class="relative mr-2">
            <button @click="open = !open; if(open) { fetchNotifs(); markAllRead(); }" class="text-gray-500 hover:text-emerald-700 relative focus:outline-none p-2 rounded-2xl hover:bg-emerald-50 transition-all">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span x-show="count > 0" class="absolute top-1 right-1 block h-3 w-3 rounded-full bg-red-600 ring-2 ring-white animate-pulse" x-cloak></span>
            </button>
            
            <!-- Dropdown Notif -->
            <div x-show="open" @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="fixed inset-x-4 top-20 sm:absolute sm:inset-auto sm:right-0 sm:top-auto sm:mt-3 w-auto sm:w-80 rounded-[2rem] shadow-2xl bg-white border border-gray-100 z-[60] overflow-hidden" x-cloak>
                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex flex-col gap-2">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Pemberitahuan</h3>
                        <span x-show="count > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px] font-black" x-text="count"></span>
                    </div>
                    <button x-show="permission === 'default'" @click="requestPermission()" class="w-full text-[10px] bg-emerald-600 text-white px-4 py-2 rounded-xl font-black hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100">
                        🔔 AKTIFKAN NOTIFIKASI HP
                    </button>
                </div>
                <div class="max-h-80 overflow-y-auto custom-scrollbar">
                    <template x-if="notifs.length === 0">
                        <div class="px-6 py-10 text-center">
                            <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 8-8-8"/></svg>
                            </div>
                            <p class="text-xs font-bold text-gray-400">Belum ada notifikasi.</p>
                        </div>
                    </template>
                    <template x-for="n in notifs" :key="n.id">
                        <a :href="n.link || '#'" @click="markRead(n.id)" class="block px-6 py-4 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition-colors" :class="{ 'bg-emerald-50/30': !n.is_read }">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-white border border-gray-100 flex items-center justify-center mr-3 shadow-sm">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-black text-gray-900 truncate uppercase tracking-tight" x-text="n.judul"></p>
                                    <p class="text-[10px] font-bold text-gray-500 mt-0.5 leading-relaxed line-clamp-2" x-text="n.pesan"></p>
                                </div>
                                <div x-show="!n.is_read" class="ml-2 flex-shrink-0">
                                    <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
        <!-- User Badge -->
        <div class="hidden sm:flex flex-col items-end mr-2">
            <span class="text-xs font-black text-gray-900 leading-none"><?= htmlspecialchars($user['name']) ?></span>
            <span class="text-[10px] font-bold text-gray-400 leading-none mt-1"><?= htmlspecialchars($user['username']) ?></span>
        </div>
        
        <div class="relative group" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center focus:outline-none transition-all transform active:scale-95">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-black shadow-lg shadow-emerald-100">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            </button>
            <!-- Minimalist Dropdown -->
            <div x-show="open" @click.away="open = false" 
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50 overflow-hidden" x-cloak>
                 <a href="<?= url('auth/logout') ?>" class="flex items-center px-4 py-3 text-xs font-bold text-red-600 hover:bg-red-50 transition-all">
                    <svg class="h-4 w-4 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                 </a>
            </div>
        </div>
    </div>
</header>
