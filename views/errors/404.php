<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | SIT-PSB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation { animation: float 6s ease-in-out infinite; }
    </style>
</head>
<body class="bg-[#f4f7f6] min-h-screen flex items-center justify-center p-6 overflow-hidden relative">
    
    <!-- Abstract Background Decorations -->
    <div class="absolute top-[-10%] left-[-10%] w-80 h-80 bg-emerald-100 rounded-full blur-3xl opacity-60"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-emerald-200 rounded-full blur-3xl opacity-40"></div>

    <div class="max-w-2xl w-full text-center relative z-10">
        <!-- Animated 404 Text -->
        <div class="relative inline-block mb-8">
            <h1 class="text-[12rem] font-black text-emerald-950 leading-none opacity-5 tracking-tighter">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="bg-emerald-600 p-8 rounded-[2.5rem] shadow-2xl shadow-emerald-200 rotate-6 float-animation">
                    <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-[3rem] p-10 shadow-xl">
            <h2 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Waduh, Halaman Hilang!</h2>
            <p class="text-gray-600 text-lg mb-10 leading-relaxed font-medium">
                Sepertinya kamu tersesat atau link yang kamu tuju sudah tidak tersedia. <br class="hidden sm:block"> Jangan khawatir, mari kembali ke jalan yang benar.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= url('/') ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black px-10 py-4 rounded-2xl shadow-xl shadow-emerald-100 transition-all transform active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Kembali ke Beranda
                </a>
                <button onclick="window.history.back()" class="bg-white hover:bg-gray-50 text-gray-700 font-black px-10 py-4 rounded-2xl border border-gray-100 shadow-sm transition-all transform active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Halaman Sebelumnya
                </button>
            </div>
        </div>

        <p class="mt-12 text-gray-400 text-[10px] font-bold uppercase tracking-[0.3em]">
            &copy; <?= date('Y') ?> SIT-PSB / PPRTQ Raudlatul Falah
        </p>
    </div>

</body>
</html>
