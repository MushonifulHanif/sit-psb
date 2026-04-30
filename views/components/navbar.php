<?php
$user = Auth::user();
$name = $user ? $user['name'] : 'Guest';
$role = $user ? $user['role'] : '';
?>
<header class="bg-white sticky top-0 z-20 border-b border-gray-100 shadow-sm backdrop-blur-md bg-white/90">
    <div class="px-4 py-3 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-10">
            <!-- Left side: Hamburger and Logo (if mobile) -->
            <div class="flex items-center">
                <?php if (Auth::check() && in_array($role, ['admin','sekretaris','bendahara_reg','bendahara_du','mufatis'])): ?>
                <!-- Toggle Sidebar (Unified) -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-emerald-600 focus:outline-none p-2 rounded-lg hover:bg-emerald-50 transition-colors mr-2">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                <?php endif; ?>
                
                <div class="flex-shrink-0 flex items-center lg:hidden">
                    <img src="<?= asset('img/logo.png') ?>" alt="Logo" class="w-7 h-7 rounded-lg mr-2 shadow-sm border border-emerald-100">
                    <span class="font-bold text-xl text-emerald-700 tracking-tight">SIT-PSB</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <?php if (Auth::check()): ?>
                    <!-- Notifikasi Bell -->
                    <div x-data="notifications()" x-init="init()" class="relative">
                        <button @click="open = !open; if(open) { fetchNotifs(); markAllRead(); }" class="text-gray-500 hover:text-emerald-700 relative focus:outline-none p-1 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span x-show="count > 0" class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-red-600 ring-2 ring-white" x-cloak></span>
                        </button>
                        
                        <!-- Dropdown Notif -->
                        <div x-show="open" @click.away="open = false" 
                             class="fixed inset-x-4 top-16 sm:absolute sm:inset-auto sm:right-0 sm:top-auto sm:mt-2 w-auto sm:w-80 rounded-xl shadow-2xl py-1 bg-white ring-1 ring-black ring-opacity-5 z-[60] overflow-hidden" 
                             x-transition x-cloak>
                            <div class="px-4 py-3 border-b bg-gray-50 flex flex-col gap-2">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Pemberitahuan</h3>
                                    <span x-show="count > 0" class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full text-[10px] font-bold" x-text="count"></span>
                                </div>
                                <!-- Tombol Request Permission Manual (Penting untuk HP) -->
                                <button x-show="permission === 'default'" @click="requestPermission()" class="w-full text-[10px] bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-emerald-700 transition-colors">
                                    🔔 Aktifkan Notifikasi HP
                                </button>
                            </div>
                            <div class="max-h-64 overflow-y-auto w-full">
                                <template x-if="notifs.length === 0">
                                    <div class="px-4 py-8 text-center">
                                        <p class="text-xs font-bold text-gray-400">Tidak ada notifikasi.</p>
                                    </div>
                                </template>
                                <template x-for="n in notifs" :key="n.id">
                                    <a :href="n.link || '#'" @click="markRead(n.id)" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0" :class="{ 'bg-emerald-50/30': !n.is_read }">
                                        <p class="text-xs font-bold text-gray-800" x-text="n.judul"></p>
                                        <p class="text-[10px] text-gray-500 mt-1 line-clamp-2" x-text="n.pesan"></p>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-emerald-700 focus:outline-none bg-gray-50 border border-gray-100 px-3 py-1.5 rounded-full transition-colors">
                        <div class="bg-emerald-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs font-bold uppercase shadow-sm">
                            <?= substr(htmlspecialchars($name), 0, 1) ?>
                        </div>
                        <span class="font-bold text-sm hidden sm:block"><?= htmlspecialchars($name) ?></span>
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" x-cloak class="absolute right-0 mt-3 w-56 rounded-2xl shadow-xl py-2 bg-white ring-1 ring-black ring-opacity-5 z-50 border border-gray-100 overflow-hidden">
                        <!-- User Identity (Crucial for Mobile) -->
                        <div class="px-4 py-3 bg-gray-50/50 border-b border-gray-50 mb-1">
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Akun Saya</p>
                            <p class="text-sm font-black text-gray-900 truncate"><?= htmlspecialchars($name) ?></p>
                            <p class="text-[10px] text-emerald-600 font-bold uppercase leading-none mt-1">
                                <?= str_replace('_', ' ', $role) ?>
                            </p>
                        </div>

                        <a href="<?= url('auth/logout') ?>" class="group block px-4 py-3 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 font-bold transition-all flex items-center">
                            <div class="p-1.5 bg-red-100 rounded-lg mr-3 group-hover:bg-red-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </div>
                            Logout Sesi
                        </a>
                    </div>
                </div>
                <?php else: ?>
                    <a href="<?= url('auth/login') ?>" class="text-gray-700 hover:text-gray-900 font-medium">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
