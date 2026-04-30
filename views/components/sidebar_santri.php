<?php
$user = Auth::user();
$status = $user['status_psb'];
if (!isset($step_results)) {
    $step_results = get_santri_step_results($user['id']);
}
if (!isset($current_step)) {
    $current_step = calculate_current_step($step_results);
}
?>
<aside 
    :class="{
        'w-72 transition-all duration-300': sidebarOpen,
        'w-0 lg:w-20 transition-all duration-300': !sidebarOpen,
        'fixed inset-y-0 left-0 z-50 lg:relative': sidebarOpen && window.innerWidth < 1024,
        'relative h-full': window.innerWidth >= 1024 || !sidebarOpen
    }"
    class="bg-white border-r border-gray-200 flex flex-col shadow-sm z-50 overflow-hidden">
    
    <!-- Branding -->
    <div class="h-20 flex items-center justify-between flex-shrink-0 px-6 border-b border-gray-50">
        <div class="flex items-center" :class="sidebarOpen ? 'space-x-3' : 'justify-center w-full'">
            <div class="bg-emerald-600 rounded-2xl p-2 flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-100">
                <?php if ($logo = get_pengaturan('app_logo')): ?>
                    <img src="<?= url($logo) ?>" class="h-6 w-6 object-contain">
                <?php else: ?>
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                <?php endif; ?>
            </div>
            <div x-show="sidebarOpen" x-transition class="flex flex-col" x-cloak>
                <span class="text-lg font-black tracking-tighter text-gray-900 leading-tight">SIT-PSB</span>
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Santri Panel</span>
            </div>
        </div>
        <button x-show="sidebarOpen && window.innerWidth < 1024" @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600" x-cloak>
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <!-- Stepper Menu -->
    <div class="flex-1 overflow-y-auto overflow-x-hidden py-8 px-4">
        <nav class="relative">
            <!-- Vertical Line (Backdrop) -->
            <div x-show="sidebarOpen" class="absolute left-9 top-4 bottom-4 w-1 bg-gray-100 rounded-full z-0" x-cloak></div>
            
            <!-- Steps -->
            <?php 
            $steps = [
                1 => ['label' => 'Profil & Biodata', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                2 => ['label' => 'Tes Online', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                3 => ['label' => 'Pengumuman', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                4 => ['label' => 'Daftar Ulang', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                5 => ['label' => 'Seragam', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
            ];
            foreach ($steps as $idx => $s): ?>
                <div class="relative z-10 mb-8 last:mb-0 group" :class="sidebarOpen ? '' : 'flex justify-center'">
                    <!-- Step Item Container -->
                    <div class="flex items-center" 
                         :class="{ 
                             'cursor-pointer': currentStep >= <?= $idx ?>, 
                             'cursor-not-allowed opacity-50': currentStep < <?= $idx ?> 
                         }"
                         @click="
                            if (currentStep >= <?= $idx ?>) {
                                if (<?= $idx ?> == 4 && !localStorage.getItem('result_revealed_<?= $user['id'] ?>') && '<?= $status ?>' === 'lulus') {
                                   Swal.fire({
                                       title: 'Lihat Pengumuman!',
                                       text: 'Silakan buka tab Pengumuman dan klik tombol Lihat Hasil terlebih dahulu.',
                                       icon: 'info',
                                       confirmButtonColor: '#059669'
                                   });
                                   activeTab = 3;
                                } else {
                                   activeTab = <?= $idx ?>; 
                                }
                            }
                            if(window.innerWidth < 1024) sidebarOpen = false;
                         ">
                        
                        <!-- Circle Indicator -->
                        <div :class="{
                            'bg-emerald-600 text-white shadow-lg shadow-emerald-100 ring-4 ring-emerald-50 scale-110': activeTab == <?= $idx ?>,
                            'bg-emerald-500 text-white': currentStep > <?= $idx ?>,
                            'bg-white text-gray-300 border-2 border-gray-100': currentStep < <?= $idx ?> && activeTab != <?= $idx ?>
                        }" class="w-10 h-10 rounded-2xl flex items-center justify-center transition-all duration-300 relative z-10 flex-shrink-0">
                            
                            <!-- Success Check -->
                            <?php if ($step_results[$idx]['completed']): ?>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-700 text-white rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                <svg class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <?php endif; ?>
                            
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $s['icon'] ?>" /></svg>
                        </div>
                        
                        <!-- Label (Desktop Only) -->
                        <div x-show="sidebarOpen" class="ml-4 flex flex-col" x-cloak>
                            <span :class="{
                                'text-emerald-700 font-black': activeTab == <?= $idx ?>,
                                'text-emerald-600 font-bold': currentStep > <?= $idx ?>,
                                'text-gray-400 font-medium': currentStep < <?= $idx ?> && activeTab != <?= $idx ?>
                            }" class="text-[10px] uppercase tracking-wider mb-0.5"><?= $s['label'] ?></span>
                            
                            <!-- Status Badges in Sidebar -->
                            <div class="flex items-center mt-0.5">
                                <template x-if="<?= $idx ?> > activeTab && !<?= $step_results[$idx]['completed'] ? 'true' : 'false' ?> && currentStep < <?= $idx ?>">
                                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest" x-cloak>Terkunci</span>
                                </template>
                                
                                <template x-if="<?= $step_results[$idx]['completed'] ? 'true' : 'false' ?>">
                                    <span class="text-[8px] font-black text-emerald-500 uppercase tracking-widest flex items-center" x-cloak>
                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        SELESAI
                                    </span>
                                </template>

                                <template x-if="!<?= $step_results[$idx]['completed'] ? 'true' : 'false' ?> && activeTab == <?= $idx ?>">
                                    <span class="text-[8px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 px-1.5 py-0.5 rounded-md border border-emerald-100" x-cloak>Sedang Dibuka</span>
                                </template>
                                
                                <template x-if="!<?= $step_results[$idx]['completed'] ? 'true' : 'false' ?> && currentStep == <?= $idx ?> && activeTab != <?= $idx ?>">
                                    <span class="text-[8px] font-black text-amber-600 uppercase tracking-widest whitespace-nowrap bg-amber-50 px-1.5 py-0.5 rounded-md border border-amber-100" x-cloak>Tahap Berjalan</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Footer Sidebar -->
    <div class="p-4 border-t border-gray-50 bg-gray-50/30">
        <div class="flex flex-col space-y-2">
            <!-- Bantuan WA -->
            <?php 
            $narahubung = json_decode($settings['list_narahubung'] ?? '[]', true);
            $wa_help = !empty($narahubung) ? $narahubung[0]['wa'] : '628123456789';
            ?>
            <a href="https://wa.me/<?= $wa_help ?>" target="_blank" 
               class="flex items-center p-3 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all group"
               :class="sidebarOpen ? 'px-4' : 'justify-center px-0'">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                <span x-show="sidebarOpen" class="ml-3 text-[10px] font-black uppercase tracking-widest whitespace-nowrap" x-cloak>HUBUNGI PANITIA</span>
            </a>

            <!-- Keluar -->
            <a href="<?= url('auth/logout') ?>" 
               class="flex items-center p-3 rounded-2xl text-red-600 hover:bg-red-50 transition-all group"
               :class="sidebarOpen ? 'px-4' : 'justify-center px-0'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="sidebarOpen" class="ml-3 text-[10px] font-black uppercase tracking-widest whitespace-nowrap" x-cloak>KELUAR AKUN</span>
            </a>
        </div>
    </div>
</aside>
