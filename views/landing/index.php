<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $settings['nama_pesantren'] ?? 'SIT-PSB' ?> - Penerimaan Santri Baru</title>
    <link rel="icon" type="image/png" href="<?= ($fav = get_pengaturan('app_favicon')) ? url($fav) : asset('img/favicon.png') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        
        button, .btn, a.btn, .button-rounded {
            border-radius: 1rem !important;
        }

        .swal2-popup {
            border-radius: 2rem !important;
            font-family: 'Instrument Sans', sans-serif !important;
        }
        .swal2-styled.swal2-confirm {
            background-color: #059669 !important;
            border-radius: 1rem !important;
        }
        
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
        
        .animation-delay-200 { animation-delay: 0.2s; }
        .animation-delay-400 { animation-delay: 0.4s; }
        .animation-delay-600 { animation-delay: 0.6s; }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
        
        .hero-overlay {
            background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 100%);
        }
        @media (max-width: 768px) {
            .hero-overlay {
                background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.5) 100%);
            }
        }
    </style>
</head>
<body x-data="{ mobileMenuOpen: false }" class="bg-white text-gray-900 selection:bg-emerald-100 selection:text-emerald-900 font-sans">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/70 backdrop-blur-xl border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <div class="bg-emerald-600 rounded-xl p-2 mr-3 shadow-lg shadow-emerald-200">
                        <?php if ($logo = get_pengaturan('app_logo')): ?>
                            <img src="<?= url($logo) ?>" class="h-6 w-6 object-contain">
                        <?php else: ?>
                            <img src="<?= asset('img/logo.png') ?>" class="h-8 w-8 rounded-lg shadow-sm">
                        <?php endif; ?>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-gray-900"><?= $settings['nama_pesantren'] ?? 'SIT-PSB' ?></span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-10">
                    <a href="#alur" class="text-gray-600 hover:text-emerald-600 font-bold text-sm tracking-wide transition-colors">ALUR</a>
                    <a href="#keunggulan" class="text-gray-600 hover:text-emerald-600 font-bold text-sm tracking-wide transition-colors">KEUNGGULAN</a>
                    <?php foreach ($sections as $s): ?>
                        <a href="#<?= strtolower(str_replace(' ', '-', $s['tag'])) ?>" class="text-gray-600 hover:text-emerald-600 font-bold text-sm tracking-wide transition-colors"><?= strtoupper(htmlspecialchars($s['tag'])) ?></a>
                    <?php endforeach; ?>
                    <a href="#faq" class="text-gray-600 hover:text-emerald-600 font-bold text-sm tracking-wide transition-colors">FAQ</a>
                </div>

                <div class="flex items-center gap-4">
                    <a href="<?= url('auth/login') ?>" class="hidden sm:block bg-gray-900 hover:bg-black text-white px-7 py-3 rounded-2xl font-bold shadow-2xl transition-all hover:scale-105 active:scale-95 text-xs tracking-widest">
                        PORTAL LOGIN
                    </a>
                    
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                        <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="md:hidden bg-white border-b border-gray-100 py-6 px-4 space-y-4" x-cloak>
            <a @click="mobileMenuOpen = false" href="#alur" class="block text-gray-600 font-bold text-lg">Alur Pendaftaran</a>
            <a @click="mobileMenuOpen = false" href="#keunggulan" class="block text-gray-600 font-bold text-lg">Keunggulan</a>
            <?php foreach ($sections as $s): ?>
                <a @click="mobileMenuOpen = false" href="#<?= strtolower(str_replace(' ', '-', $s['tag'])) ?>" class="block text-gray-600 font-bold text-lg"><?= htmlspecialchars($s['tag']) ?></a>
            <?php endforeach; ?>
            <a @click="mobileMenuOpen = false" href="#faq" class="block text-gray-600 font-bold text-lg">FAQ</a>
            <hr class="border-gray-100">
            <a href="<?= url('auth/login') ?>" class="block w-full bg-emerald-600 text-white text-center py-4 rounded-2xl font-bold tracking-widest uppercase text-sm">PORTAL LOGIN</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative min-h-[95vh] flex items-center overflow-visible z-30" 
             x-data="{ 
                active: 0,
                images: <?= htmlspecialchars($settings['hero_images_json'] ?? '[]') ?>,
                init() {
                    if(this.images.length > 1) {
                        setInterval(() => {
                            this.active = (this.active + 1) % this.images.length;
                        }, 8000);
                    }
                }
             }">
        
        <!-- Cinematic Multi-Image Background -->
        <div class="absolute inset-0 z-0 overflow-hidden bg-black">
            <template x-for="(img, index) in images" :key="index">
                <div class="absolute inset-0 transition-opacity duration-[3000ms] ease-in-out"
                     :class="active == index ? 'opacity-100' : 'opacity-0'">
                    <img :src="'<?= url('') ?>/' + img" class="w-full h-full object-cover animate-ken-burns">
                </div>
            </template>
            
            <!-- Fallback if no images in gallery -->
            <template x-if="images.length == 0">
                <div class="absolute inset-0">
                    <div class="absolute inset-0 bg-emerald-900"></div>
                </div>
            </template>
            
            <!-- Dark Cinematic Overlay -->
            <div class="absolute inset-0 hero-overlay z-10 opacity-70"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/40 z-10"></div>
        </div>

        <style>
            @keyframes ken-burns {
                0% { transform: scale(1); }
                100% { transform: scale(1.15); }
            }
            .animate-ken-burns {
                animation: ken-burns 30s linear infinite alternate;
            }
        </style>

        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-32 pb-80 lg:pt-20 lg:pb-32">
            <div class="max-w-4xl">
                <?php 
                    $badgeStyle = $settings['hero_badge_style'] ?? 'emerald';
                    $badgeAnim = $settings['hero_badge_animation'] ?? 'ping';
                    $badgeText = !empty($settings['hero_badge_text']) ? $settings['hero_badge_text'] : 'PENDAFTARAN TAHUN AJARAN ' . ($settings['tahun_ajaran'] ?? date('Y')) . ' TELAH DIBUKA';
                    
                    $styleClasses = [
                        'emerald' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
                        'blue' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                        'amber' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
                        'rose' => 'bg-rose-500/20 text-rose-400 border-rose-500/30'
                    ];
                    
                    $dotClasses = [
                        'emerald' => 'bg-emerald-500',
                        'blue' => 'bg-blue-500',
                        'amber' => 'bg-amber-500',
                        'rose' => 'bg-rose-500'
                    ];

                    $animClass = '';
                    if($badgeAnim == 'bounce') $animClass = 'animate-bounce';
                    if($badgeAnim == 'pulse') $animClass = 'animate-pulse';
                    $badgeSize = $settings['hero_badge_size'] ?? 'text-xs';
                ?>
                <div class="inline-flex items-center <?= $styleClasses[$badgeStyle] ?? $styleClasses['emerald'] ?> border backdrop-blur-md px-5 py-2.5 rounded-full font-black <?= $badgeSize ?> tracking-widest mb-8 animate-fade-in-up <?= $animClass ?>">
                    <?php if($badgeAnim == 'ping'): ?>
                        <span class="relative flex h-2 w-2 mr-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?= $dotClasses[$badgeStyle] ?? 'bg-emerald-500' ?> opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 <?= $dotClasses[$badgeStyle] ?? 'bg-emerald-500' ?>"></span>
                        </span>
                    <?php endif; ?>
                    <?= htmlspecialchars($badgeText) ?>
                </div>
                <h1 class="text-5xl md:text-[7rem] font-black text-white tracking-tight mb-8 leading-[1] animate-fade-in-up animation-delay-200">
                    <?= htmlspecialchars($settings['hero_judul'] ?? 'Penerimaan Santri Baru') ?>
                </h1>
                <p class="text-xl md:text-2xl text-gray-300 mb-12 leading-relaxed animate-fade-in-up animation-delay-400 max-w-2xl min-h-[3em]">
                    <span id="typing-text" data-text="<?= htmlspecialchars($settings['hero_subjudul'] ?? '') ?>"></span>
                </p>
                <div class="flex flex-col sm:flex-row gap-5 animate-fade-in-up animation-delay-600">
                    <a href="<?= url('auth/register') ?>" class="px-10 py-5 bg-emerald-600 text-white rounded-2xl font-black text-lg shadow-[0_20px_50px_rgba(16,185,129,0.3)] hover:bg-emerald-500 transition-all hover:-translate-y-1 flex items-center justify-center">
                        DAFTAR SEKARANG
                        <svg class="w-6 h-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                    <a href="#alur" class="px-10 py-5 bg-white/10 backdrop-blur-md text-white border border-white/20 rounded-2xl font-bold text-lg hover:bg-white/20 transition-all flex items-center justify-center">
                        LIHAT INFO PENDAFTARAN
                    </a>
                </div>
            </div>
        </div>

        <!-- Dynamic Floating Stats Section -->
        <div class="absolute -bottom-16 lg:-bottom-10 inset-x-0 z-50 px-4">
            <div class="max-w-7xl mx-auto">
                <?php 
                    $statsStyle = $settings['hero_stats_style'] ?? 'emerald';
                    $statsJson = json_decode($settings['hero_stats_json'] ?? '[]', true);
                    
                    $statsBg = [
                        'emerald' => 'bg-white/20 border-white/30 shadow-[0_30px_100px_rgba(0,0,0,0.5)]',
                        'blue' => 'bg-blue-900/60 border-blue-400/30 shadow-[0_30px_100px_rgba(0,0,0,0.5)]',
                        'dark' => 'bg-gray-900/90 border-white/20 shadow-[0_30px_100px_rgba(0,0,0,0.5)]'
                    ];
                    $statsAccent = [
                        'emerald' => 'bg-emerald-500',
                        'blue' => 'bg-blue-500',
                        'dark' => 'bg-gray-700'
                    ];
                ?>
                <div class="<?= $statsBg[$statsStyle] ?? $statsBg['emerald'] ?> backdrop-blur-2xl rounded-3xl lg:rounded-[3rem] p-6 lg:p-10 border shadow-2xl grid grid-cols-2 lg:flex lg:flex-nowrap items-center justify-around gap-6 lg:gap-0 animate-fade-in-up animation-delay-1000">
                    <?php if(!empty($statsJson)): foreach($statsJson as $index => $item): ?>
                        <div class="flex flex-col lg:flex-row items-center lg:items-center text-center lg:text-left group">
                            <div class="w-10 h-10 lg:w-14 lg:h-14 <?= $statsAccent[$statsStyle] ?? $statsAccent['emerald'] ?> rounded-xl lg:rounded-2xl flex items-center justify-center mb-3 lg:mb-0 lg:mr-5 shadow-lg group-hover:scale-110 transition-transform">
                                <?php if($item['icon'] == 'users'): ?>
                                    <svg class="w-5 h-5 lg:w-7 lg:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                <?php elseif($item['icon'] == 'office-building'): ?>
                                    <svg class="w-5 h-5 lg:w-7 lg:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <?php elseif($item['icon'] == 'book-open'): ?>
                                    <svg class="w-5 h-5 lg:w-7 lg:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5S19.832 6.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                <?php else: ?>
                                    <svg class="w-5 h-5 lg:w-7 lg:h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="flex items-baseline justify-center lg:justify-start">
                                    <span class="text-xl lg:text-4xl font-black text-white counter" data-target="<?= $item['num'] ?>">0</span>
                                    <span class="text-sm lg:text-2xl font-black text-emerald-400 ml-1"><?= htmlspecialchars($item['suffix']) ?></span>
                                </div>
                                <div class="text-[8px] lg:text-xs font-bold text-white/80 uppercase tracking-widest lg:tracking-[0.2em] mt-1"><?= htmlspecialchars($item['label']) ?></div>
                            </div>
                        </div>
                        <?php if($index < count($statsJson)-1): ?>
                            <div class="hidden lg:block w-px h-12 bg-white/10"></div>
                        <?php endif; ?>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Counter Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.counter');
            const observerOptions = { threshold: 0.2 };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        const target = parseInt(counter.getAttribute('data-target'));
                        const duration = 2000;
                        const startTime = performance.now();

                        const animate = (currentTime) => {
                            const elapsed = currentTime - startTime;
                            const progress = Math.min(elapsed / duration, 1);
                            
                            const easeOut = 1 - Math.pow(1 - progress, 3);
                            
                            counter.innerText = Math.ceil(easeOut * target);

                            if (progress < 1) {
                                requestAnimationFrame(animate);
                            } else {
                                counter.innerText = target;
                            }
                        };
                        requestAnimationFrame(animate);
                        observer.unobserve(counter);
                    }
                });
            }, observerOptions);

            counters.forEach(c => observer.observe(c));

            // Typing Effect Script
            const typingTarget = document.getElementById('typing-text');
            if (typingTarget) {
                const fullText = typingTarget.getAttribute('data-text');
                let charIndex = 0;
                
                const type = () => {
                    if (charIndex < fullText.length) {
                        typingTarget.innerHTML += fullText.charAt(charIndex);
                        charIndex++;
                        setTimeout(type, 30);
                    }
                };
                
                // Start typing after a short delay
                setTimeout(type, 1000);
            }
        });
    </script>

    <!-- Alur Pendaftaran Section -->
    <section id="alur" class="py-32 px-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h4 class="text-4xl md:text-6xl font-black text-gray-900 tracking-tight leading-tight">Alur Pendaftaran <span class="text-emerald-600">Mudah</span></h4>
                <p class="text-lg text-gray-500 mt-6 max-w-2xl mx-auto font-medium">Hanya butuh 3 tahapan mudah untuk bergabung menjadi bagian dari santri kami di tahun ajaran ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Step 1 -->
                <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col items-center text-center group hover:-translate-y-2 transition-all">
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <span class="text-4xl font-black text-emerald-600 italic">1</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 leading-tight">Registrasi & Aktivasi Akun</h3>
                    <p class="text-gray-500 mt-4 leading-relaxed font-medium">Daftarkan akun wali santri & lakukan pembayaran registrasi melalui transfer untuk aktivasi.</p>
                </div>
                <!-- Step 2 -->
                <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col items-center text-center group hover:-translate-y-2 transition-all">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <span class="text-4xl font-black text-blue-600 italic">2</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 leading-tight">Lengkapi Biodata & Berkas</h3>
                    <p class="text-gray-500 mt-4 leading-relaxed font-medium">Lengkapi profil santri, upload berkas (KTP/KK), dan lakukan pengukuran seragam online.</p>
                </div>
                <!-- Step 3 -->
                <div class="bg-white p-10 rounded-[3rem] shadow-xl shadow-gray-200/50 border border-gray-100 flex flex-col items-center text-center group hover:-translate-y-2 transition-all">
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <span class="text-4xl font-black text-emerald-600 italic">3</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 leading-tight">Tes Seleksi & Hasil</h3>
                    <p class="text-gray-500 mt-4 leading-relaxed font-medium">Kerjakan soal tes secara online dari rumah & lihat pengumuman kelulusan di portal.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section id="keunggulan" class="py-32 px-4 bg-white text-center sm:text-left">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row items-end justify-between mb-20 gap-8">
                <div class="max-w-2xl">
                    <h2 class="text-4xl md:text-6xl font-black text-gray-900 tracking-tight leading-tight">Mencetak Generasi <span class="text-emerald-600">Unggul</span></h2>
                    <p class="text-lg text-gray-500 mt-6 font-medium leading-relaxed">Kami membekali santri dengan Al-Qur'an dan kemandirian serta karakter di era modern.</p>
                </div>
                <div class="bg-emerald-50 px-8 py-4 rounded-2xl border-2 border-emerald-100 hidden lg:block">
                    <span class="text-emerald-700 font-black tracking-widest text-sm italic uppercase">PESANTREN MODERN TERINTEGRASI</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50 transition-all cursor-default">
                    <div class="bg-white w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm mb-6 mx-auto sm:mx-0">
                        <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.247 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 leading-tight">Program Tahfidz Intensif</h4>
                </div>
                <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all cursor-default">
                    <div class="bg-white w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm mb-6 mx-auto sm:mx-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 leading-tight">Kurikulum Terpadu</h4>
                </div>
                <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50 transition-all cursor-default">
                    <div class="bg-white w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm mb-6 mx-auto sm:mx-0">
                        <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 leading-tight">Character Building</h4>
                </div>
                <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all cursor-default">
                    <div class="bg-white w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm mb-6 mx-auto sm:mx-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    </div>
                    <h4 class="text-xl font-black text-gray-900 leading-tight">Fasilitas Modern</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Sections from Admin -->
    <div class="relative bg-white border-y border-gray-100">
        <?php foreach ($sections as $index => $s): 
            $isEven = $index % 2 == 1;
        ?>
        <section id="<?= strtolower(str_replace(' ', '-', $s['tag'])) ?>" class="py-24 px-4 <?= $isEven ? 'bg-gray-50' : 'bg-white' ?>">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col md:flex-row items-center gap-12 lg:gap-24">
                    <div class="flex-1 <?= $isEven ? 'md:order-2' : '' ?>">
                        <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight mb-8 text-center md:text-left"><?= htmlspecialchars($s['title']) ?></h2>
                        
                        <div class="prose prose-emerald lg:prose-xl max-w-none text-gray-600 leading-relaxed font-medium">
                            <?php if($s['type'] == 'video'): ?>
                                <div class="mb-10 text-lg italic border-l-4 border-emerald-500 pl-6 py-2 bg-emerald-50/50 rounded-r-xl">
                                    <?= $s['content'] ?>
                                </div>
                                <?php 
                                    $videoUrl = $s['video_url'];
                                    if (strpos($videoUrl, 'drive.google.com') !== false) {
                                        if (strpos($videoUrl, 'file/d/') !== false) {
                                            $parts = explode('file/d/', $videoUrl);
                                            $id = explode('/', $parts[1])[0];
                                            $videoUrl = "https://drive.google.com/file/d/$id/preview";
                                        }
                                    }
                                ?>
                                <?php if(!empty($videoUrl)): ?>
                                <div class="group relative pt-[56.25%] rounded-[3rem] overflow-hidden shadow-2xl bg-gray-900 ring-8 ring-white">
                                    <iframe class="absolute inset-0 w-full h-full" src="<?= htmlspecialchars($videoUrl) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="bg-white p-8 md:p-12 rounded-[3.5rem] shadow-xl shadow-gray-200/30 border border-gray-100/50">
                                    <?= $s['content'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endforeach; ?>
    </div>

    <!-- FAQ Section -->
    <section id="faq" class="py-32 px-4 bg-gray-50" x-data="{ active: null }">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-6xl font-black text-gray-900 tracking-tight leading-tight"><span class="text-emerald-600">Pertanyaan</span> Umum</h2>
                <p class="text-lg text-gray-500 mt-6 font-medium">Beberapa pertanyaan yang sering diajukan mengenai pendaftaran santri baru.</p>
            </div>

            <div class="space-y-6">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-md">
                    <button @click="active = (active === 1 ? null : 1)" class="w-full text-left p-8 flex justify-between items-center transition-colors hover:bg-emerald-50/50">
                        <span class="text-xl font-bold text-gray-900 text-wrap leading-tight">Kapan pendaftaran santri baru ditutup?</span>
                        <svg class="w-6 h-6 text-emerald-600 transition-transform flex-shrink-0 ml-4" :class="active === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="active === 1" x-transition class="p-8 pt-0 text-gray-600 leading-relaxed font-bold">
                        Pendaftaran ditutup menyesuaikan dengan kuota yang tersedia pada setiap gelombang. Informasi penutupan akan diumumkan di dashboard santri setelah Anda login.
                    </div>
                </div>
                <!-- FAQ 2 -->
                <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-md">
                    <button @click="active = (active === 2 ? null : 2)" class="w-full text-left p-8 flex justify-between items-center transition-colors hover:bg-emerald-50/50">
                        <span class="text-xl font-bold text-gray-900 text-wrap leading-tight">Berapa biaya pendaftaran (registrasi)?</span>
                        <svg class="w-6 h-6 text-emerald-600 transition-transform flex-shrink-0 ml-4" :class="active === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="active === 2" x-transition class="p-8 pt-0 text-gray-600 leading-relaxed font-bold">
                        Biaya registrasi adalah sesuai dengan rincian biaya pendaftaran yang tampil saat Anda membuat akun. Biaya ini mencakup administrasi sistem dan ujian online santri.
                    </div>
                </div>
                <!-- FAQ 3 -->
                <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-md">
                    <button @click="active = (active === 3 ? null : 3)" class="w-full text-left p-8 flex justify-between items-center transition-colors hover:bg-emerald-50/50">
                        <span class="text-xl font-bold text-gray-900 text-wrap leading-tight">Bagaimana cara konfirmasi pembayaran?</span>
                        <svg class="w-6 h-6 text-emerald-600 transition-transform flex-shrink-0 ml-4" :class="active === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="active === 3" x-transition class="p-8 pt-0 text-gray-600 leading-relaxed font-bold">
                        Anda dapat mentransfer biaya pendaftaran ke nomor rekening BSI yang tersedia di menu Pembayaran. Setelah transfer, harap upload foto bukti transfer untuk kami verifikasi.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-white pt-24 pb-12 px-4 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

        <div class="max-w-7xl mx-auto relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-16 mb-20">
                <div class="lg:col-span-2">
                    <div class="flex items-center mb-8">
                        <div class="bg-emerald-600 p-2 rounded-xl mr-3">
                            <img src="<?= asset('img/logo.png') ?>" class="h-7 w-7 rounded-lg">
                        </div>
                        <h3 class="text-2xl font-black tracking-tight uppercase"><?= $settings['nama_pesantren'] ?? 'SIT-PSB' ?></h3>
                    </div>
                    <address class="text-xl text-gray-400 max-w-sm leading-relaxed mb-10 font-bold not-italic">
                        <?= nl2br(htmlspecialchars($settings['alamat_pesantren'] ?? '')) ?>
                    </address>
                    <div class="flex space-x-5">
                        <?php if(!empty($settings['footer_instagram'])): ?>
                             <a href="<?= $settings['footer_instagram'] ?>" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-emerald-600 hover:text-white transition-all transform hover:-translate-y-1"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" /></svg></a>
                        <?php endif; ?>
                         <?php if(!empty($settings['footer_facebook'])): ?>
                             <a href="<?= $settings['footer_facebook'] ?>" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-emerald-600 hover:text-white transition-all transform hover:-translate-y-1"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-2.221c0-.822.112-1.117.73-1.117h3.27v-4.662h-4.004c-4.225 0-5.996 2.015-5.996 5.625v2.375z" /></svg></a>
                         <?php endif; ?>
                        <?php if(!empty($settings['footer_email'])): ?>
                             <a href="mailto:<?= $settings['footer_email'] ?>" class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-gray-400 hover:bg-emerald-600 hover:text-white transition-all transform hover:-translate-y-1"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-black tracking-widest text-emerald-500 uppercase mb-8">Informasi</h3>
                    <ul class="space-y-4 text-gray-400 font-extrabold uppercase text-[11px] tracking-widest">
                        <li><a href="#alur" class="hover:text-white transition-colors">Alur Pendaftaran</a></li>
                        <li><a href="#keunggulan" class="hover:text-white transition-colors">Keunggulan</a></li>
                        <?php foreach ($sections as $s): ?>
                            <li><a href="#<?= strtolower(str_replace(' ', '-', $s['tag'])) ?>" class="hover:text-white transition-colors"><?= htmlspecialchars($s['tag']) ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="#faq" class="hover:text-white transition-colors">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-black tracking-widest text-emerald-500 uppercase mb-8">Narahubung</h3>
                    <div class="space-y-4">
                        <?php 
                        $narahubung = json_decode($settings['list_narahubung'] ?? '[]', true);
                        foreach($narahubung as $n): 
                            $wa = preg_replace('/[^0-9]/', '', $n['wa']);
                            if(strpos($wa, '0') === 0) $wa = '62' . substr($wa, 1);
                            $msg = urlencode("Assalamu'alaikum " . $n['nama'] . ", mohon info terkait Pendaftaran Santri Baru di PPRTQ Raudlatul Falah");
                        ?>
                            <a href="https://wa.me/<?= $wa ?>?text=<?= $msg ?>" class="flex items-center p-4 bg-white/5 rounded-2xl hover:bg-emerald-600/20 group transition-all cursor-pointer">
                                <div class="bg-emerald-600 p-2 rounded-xl mr-3 group-hover:scale-110 transition-transform">
                                    <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.025 3.212l-.545 2.034 2.087-.547c.879.479 1.827.82 2.944.821 3.181 0 5.767-2.587 5.768-5.768 0-3.181-2.587-5.766-5.768-5.766zm3.435 8.243c-.15.422-.865.776-1.196.818-.329.043-.659.063-1.077-.073-.243-.078-.545-.191-.914-.352-1.571-.687-2.593-2.28-2.671-2.383-.078-.103-.637-.847-.637-1.614 0-.766.402-1.143.545-1.291.144-.148.314-.185.42-.185.106 0 .21 0 .302.005.097.004.227-.037.355.27.128.307.44.1.536.297.095.196.095.38.037.498-.058.118-.118.196-.236.332-.118.136-.248.304-.354.408-.113.111-.231.233-.1.458.132.225.586.966 1.258 1.564.866.772 1.597 1.012 1.823 1.124.225.112.358.095.492-.058.133-.153.57-.659.722-.885.152-.225.305-.191.516-.112.21.079 1.336.63 1.566.744.229.117.382.176.44.275.053.1.053.57-.102.993z" /></svg>
                                </div>
                                <div>
                                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest leading-none mb-1">WhatsApp</div>
                                    <div class="font-bold text-gray-200 group-hover:text-white transition-colors uppercase text-sm"><?= htmlspecialchars($n['nama']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="pt-12 border-t border-white/5 flex flex-col md:flex-row justify-between items-center text-gray-500 text-sm">
                <p class="mb-4 md:mb-0">&copy; <?= date('Y') ?> PPRTQ Raudlatul Falah.</p>
            </div>
        </div>
    </footer>

</body>
</html>
