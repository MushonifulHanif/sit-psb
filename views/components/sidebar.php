<?php
$role = Auth::user()['role'] ?? '';
$menu = [];

// Map menu item to SVG icons
$icons = [
    'Dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />',
    'Panitia' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
    'Gelombang' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />',
    'Bank Soal' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'Item Seragam' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />',
    'Pengaturan' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    'Konten Landing' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />',
    'Tahun Ajaran' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'Rekap Pendaftar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />',
    'Export Data' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />',
    'Verifikasi Registrasi' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'Tracking Piutang' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'Verifikasi Daftar Ulang' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'Daftar Santri' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
    'Kelulusan' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
];

if ($role === 'admin') {
    $menu = [
        ['url' => 'admin', 'title' => 'Dashboard'],
        ['url' => 'admin/users', 'title' => 'Panitia'],
        ['url' => 'admin/gelombang', 'title' => 'Gelombang'],
        ['url' => 'admin/soal', 'title' => 'Bank Soal'],
        ['url' => 'admin/item-seragam', 'title' => 'Item Seragam'],
        ['url' => 'admin/pengaturan', 'title' => 'Pengaturan'],
        ['url' => 'admin/konten-landing', 'title' => 'Konten Landing'],
        ['url' => 'admin/tahun-ajaran', 'title' => 'Tahun Ajaran'],
    ];
} elseif ($role === 'sekretaris') {
    $menu = [
        ['url' => 'sekretaris', 'title' => 'Dashboard'],
        ['url' => 'sekretaris/pendaftar', 'title' => 'Rekap Pendaftar'],
        ['url' => 'sekretaris/export', 'title' => 'Export Data'],
    ];
} elseif ($role === 'bendahara_reg') {
    $menu = [
        ['url' => 'bendahara-reg', 'title' => 'Verifikasi Registrasi'],
    ];
} elseif ($role === 'bendahara_du') {
    $menu = [
        ['url' => 'bendahara-du', 'title' => 'Tracking Piutang'],
        ['url' => 'bendahara-du/verifikasi', 'title' => 'Verifikasi Daftar Ulang'],
    ];
} elseif ($role === 'mufatis') {
    $menu = [
        ['url' => 'mufatis', 'title' => 'Daftar Santri'],
        ['url' => 'mufatis/kelulusan', 'title' => 'Kelulusan'],
    ];
}
?>
<aside 
    :class="{
        'w-64': sidebarOpen,
        'w-20': !sidebarOpen,
        'fixed inset-y-0 left-0 z-50 shadow-2xl': sidebarOpen && window.innerWidth < 1024,
        'relative lg:static': !sidebarOpen || window.innerWidth >= 1024
    }"
    class="bg-white border-r border-gray-100 flex flex-col z-30 transition-all duration-300 ease-in-out overflow-hidden h-full">
    
    <!-- Header Sidebar -->
    <div class="h-16 flex items-center justify-between flex-shrink-0 px-4 border-b border-gray-100">
        <div class="flex items-center space-x-3 px-1">
            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200 overflow-hidden border-2 border-white">
                <?php if ($logo = get_pengaturan('app_logo')): ?>
                    <img src="<?= url($logo) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <img src="<?= asset('img/logo.png') ?>" class="w-full h-full object-cover">
                <?php endif; ?>
            </div>
            <span x-show="sidebarOpen" x-transition class="text-xl font-extrabold tracking-tight text-gray-900 truncate" x-cloak>SIT-PSB</span>
        </div>
        <!-- Close button for mobile only when expanded -->
        <button x-show="sidebarOpen && window.innerWidth < 1024" @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600" x-cloak>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>
    
    <div class="mt-6 flex-1 h-0 overflow-y-auto overflow-x-hidden pt-0" :class="sidebarOpen ? 'p-3' : 'p-2'">
        <nav class="space-y-1.5">
            <div x-show="sidebarOpen" x-transition class="mb-5 px-3 tracking-wider text-[0.65rem] font-bold uppercase text-gray-400" x-cloak>
                MENU <?= str_replace('_', ' ', strtoupper($role)) ?>
            </div>
            <div x-show="!sidebarOpen" class="mb-5 flex justify-center text-[0.65rem] font-bold uppercase text-gray-400" x-cloak>
                <div class="w-4 h-[1px] bg-gray-300"></div>
            </div>
            
            <?php foreach ($menu as $item): 
                $icon = $icons[$item['title']] ?? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                $isActive = (isset($_GET['url']) && $_GET['url'] === $item['url']) || (!isset($_GET['url']) && $item['url'] === $role);
            ?>
                <a href="<?= url($item['url']) ?>" 
                   class="group flex items-center py-2.5 font-medium rounded-xl transition-all duration-200 <?php echo $isActive ? 'bg-[#f0fdf4] text-emerald-700 shadow-sm' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'; ?>"
                   :class="sidebarOpen ? 'px-3 w-full' : 'px-0 justify-center w-full'"
                   title="<?= htmlspecialchars($item['title']) ?>">
                   
                    <svg class="flex-shrink-0 h-5 w-5 <?php echo $isActive ? 'text-emerald-600' : 'text-gray-400 group-hover:text-gray-600'; ?>" 
                         :class="sidebarOpen ? 'mr-3' : 'mr-0'"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <?= $icon ?>
                    </svg>
                    
                    <span x-show="sidebarOpen" x-transition class="text-sm truncate" x-cloak>
                        <?= htmlspecialchars($item['title']) ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</aside>


