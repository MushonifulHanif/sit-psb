<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
        $seo_title = get_pengaturan('seo_title');
        $seo_desc = get_pengaturan('seo_description');
        $seo_keys = get_pengaturan('seo_keywords');
    ?>
    <title><?= $seo_title ?: ($title ?? 'SIT-PSB PPRTQ Raudlatul Falah') ?></title>
    <meta name="description" content="<?= htmlspecialchars($seo_desc ?? 'Sistem Informasi Terpadu Pendaftaran Santri Baru') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($seo_keys ?? 'psb, online, pesantren') ?>">
    <link rel="icon" type="image/png" href="<?= ($fav = get_pengaturan('app_favicon')) ? url($fav) : asset('img/favicon.png') ?>">
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="<?= url('manifest.json') ?>">
    <meta name="theme-color" content="#059669">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                        arabic: ['Amiri', 'serif'],
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Quill WYSIWYG -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <!-- Flatpickr (Premium Date Picker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Premium Flatpickr Styling */
        .flatpickr-calendar {
            border-radius: 1.5rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #f3f4f6 !important;
            padding: 0.5rem;
        }
        .flatpickr-day.selected {
            background: #059669 !important;
            border-color: #059669 !important;
            border-radius: 0.75rem !important;
        }
        .flatpickr-day:hover {
            border-radius: 0.75rem !important;
        }
        .flatpickr-monthDropdown-months {
            font-size: 0.9rem !important;
            font-weight: 700 !important;
            color: #111827 !important;
        }
        .flatpickr-current-month .numInputWrapper {
            width: 7ch !important;
        }
        .flatpickr-current-month input.cur-year {
            font-weight: 700 !important;
        }
        /* Mobile adjustment for month select */
        select.flatpickr-monthDropdown-months {
            padding: 2px 8px !important;
            border-radius: 0.5rem !important;
            border: 1px solid #e5e7eb !important;
        }

        /* Global Button Rounding */
        button, .btn, input[type="submit"], input[type="button"], .button-rounded {
            border-radius: 1rem !important; /* Matches rounded-2xl */
        }
        
        /* Custom Glassmorphism SweetAlert2 Style */
        .swal2-popup {
            background: rgba(255, 255, 255, 0.7) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.4) !important;
            border-radius: 2.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
            padding: 2rem !important;
            font-family: 'Instrument Sans', sans-serif !important;
        }
        .swal2-title {
            color: #111827 !important;
            font-weight: 800 !important;
            font-size: 1.5rem !important;
            padding-top: 1rem !important;
        }
        .swal2-html-container {
            color: #4b5563 !important;
            font-weight: 500 !important;
        }
        .swal2-icon {
            border-width: 3px !important;
            margin-top: 1rem !important;
        }
        .swal2-actions {
            margin-top: 2rem !important;
            gap: 12px !important;
        }
        .swal2-styled.swal2-confirm {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
            border-radius: 100px !important;
            font-weight: 800 !important;
            padding: 0.8rem 2.5rem !important;
            font-size: 0.9rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3) !important;
        }
        .swal2-styled.swal2-cancel {
            background: #f9fafb !important;
            color: #6b7280 !important;
            border-radius: 100px !important;
            font-weight: 700 !important;
            padding: 0.8rem 2.5rem !important;
            font-size: 0.9rem !important;
            border: 1px solid #e5e7eb !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }
        .swal2-styled.swal2-confirm:focus, .swal2-styled.swal2-cancel:focus {
            box-shadow: none !important;
        }

        /* Global Button Rounding */
        button, .btn, input[type="submit"], input[type="button"], .button-rounded {
            border-radius: 1rem !important; 
        }
        
        /* Smooth transitions for sidebar */
        .sidebar-transition { transition: width 0.3s ease-in-out; }
        
        /* Global Scaling for Mobile */
        @media (max-width: 640px) {
            html { font-size: 14px; }
            .mobile-scale { transform: scale(0.95); transform-origin: top left; }
        }
    </style>
</head>
<body x-data="{ sidebarOpen: window.innerWidth > 1024 }" 
      x-on:resize.window="sidebarOpen = window.innerWidth > 1024"
      class="bg-[#f4f7f6] text-gray-800 font-sans antialiased h-screen flex overflow-hidden">
    
    <!-- Masking & Datepicker Init -->
    <script>
        function formatRupiah(value) {
            if (!value) return '';
            let number_string = value.toString().replace(/[^,\d]/g, '');
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return rupiah + (split[1] !== undefined ? ',' + split[1] : '');
        }

        function parseRupiah(value) {
            if (!value) return 0;
            return parseFloat(value.toString().replace(/\./g, '').replace(',', '.'));
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Init Flatpickr Indonesian
            flatpickr.localize(flatpickr.l10ns.id);
            
            // Standard Date
            flatpickr(".datepicker", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                disableMobile: true,
                monthSelectorType: 'dropdown',
                onReady: function(selectedDates, dateStr, instance) {
                    const yearInput = instance.currentYearElement;
                    const container = yearInput.parentElement;
                    if (container) {
                        const select = document.createElement('select');
                        select.className = 'flatpickr-monthDropdown-months'; 
                        select.style.appearance = 'none';
                        select.style.border = 'none';
                        select.style.fontWeight = '700';
                        select.style.background = 'transparent';
                        select.style.cursor = 'pointer';
                        
                        const currentYear = new Date().getFullYear();
                        for (let i = currentYear + 5; i >= 1970; i--) {
                            const opt = document.createElement('option');
                            opt.value = i;
                            opt.text = i;
                            select.appendChild(opt);
                        }
                        
                        select.value = instance.currentYear;
                        select.addEventListener('change', (e) => {
                            instance.changeYear(parseInt(e.target.value));
                        });
                        
                        yearInput.style.display = 'none';
                        container.appendChild(select);
                        
                        instance.yearSelect = select;
                    }
                },
                onYearChange: function(selectedDates, dateStr, instance) {
                    if (instance.yearSelect) instance.yearSelect.value = instance.currentYear;
                }
            });

            // Datetime 24h
            flatpickr(".datetimepicker", {
                enableTime: true,
                time_24hr: true,
                dateFormat: "Y-m-d H:i:s",
                altInput: true,
                altFormat: "d/m/Y H:i",
                disableMobile: true,
                monthSelectorType: 'dropdown'
            });
        });
    </script>

    <!-- Mobile Backdrop (only when expanded on mobile) -->
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden" x-cloak></div>
    <!-- Sidebar -->
    <?php require __DIR__ . '/../components/sidebar.php'; ?>
 
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Navbar -->
        <?php require __DIR__ . '/../components/navbar.php'; ?>
 
        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-3 sm:p-6">
            <?= display_flash_message() ?>
            <?= $content ?? '' ?>
        </main>
    </div>
    
    <!-- Notification & Service Worker Logic -->
    <script>
        const VAPID_PUBLIC_KEY = '<?= VAPID_PUBLIC_KEY ?? "" ?>';
        
        function notifications() {
            return {
                open: false,
                count: 0,
                notifs: [],
                permission: Notification.permission,
                requestPermission() {
                    Notification.requestPermission().then(permission => {
                        this.permission = permission;
                        if(permission === 'granted') {
                            if ('serviceWorker' in navigator && 'PushManager' in window && VAPID_PUBLIC_KEY) {
                                navigator.serviceWorker.getRegistration().then(swReg => {
                                    if(swReg) this.subscribeUser(swReg);
                                });
                            }
                            this.fetchNotifs();
                            Swal.fire({ title: 'Berhasil!', text: 'Notifikasi telah aktif.', icon: 'success', timer: 2000, showConfirmButton: false });
                        }
                    });
                },
                init() {
                    this.fetchNotifs();
                    // Polling: Cek notifikasi baru setiap 15 detik agar lebih responsif
                    setInterval(() => {
                        this.fetchNotifs();
                    }, 15000); 
                    // Register Service Worker for Push
                    if ('serviceWorker' in navigator && 'PushManager' in window && VAPID_PUBLIC_KEY) {
                        navigator.serviceWorker.register('<?= url("service-worker.js") ?>', { scope: '<?= url("/") ?>' })
                        .then(swReg => {
                            console.log('SW Registered', swReg);
                            swReg.update(); // Paksa update SW terbaru
                            // Check permission
                            if(Notification.permission === 'granted') {
                                this.subscribeUser(swReg);
                            }
                        }).catch(err => console.error('SW Register Error:', err));
                    }
                },
                fetchNotifs() {
                    fetch('<?= url("notification/unread") ?>')
                        .then(res => res.json())
                        .then(data => {
                            this.count = data.count || 0;
                            this.notifs = data.data || [];
                            
                            if(this.notifs.length > 0) {
                                this.notifs.forEach(n => {
                                    if(!n.is_read && !localStorage.getItem('notif_seen_'+n.id) && Notification.permission === 'granted') {
                                        new Notification(n.judul, { 
                                            body: n.pesan,
                                            icon: '<?= asset("images/logo.png") ?>'
                                        });
                                        localStorage.setItem('notif_seen_'+n.id, '1');
                                    }
                                });
                            }
                        }).catch(e => console.error('Fetch Notif Error:', e));
                },
                markRead(id) {
                    fetch('<?= url("notification/mark-read/") ?>' + id)
                        .then(() => { this.fetchNotifs(); });
                },
                markAllRead() {
                    if (this.count > 0) {
                        fetch('<?= url("notification/mark-all-read") ?>', { method: 'POST' })
                            .then(() => { this.fetchNotifs(); });
                    }
                },
                subscribeUser(swReg) {
                    swReg.pushManager.getSubscription()
                    .then(sub => {
                        if (sub === null) {
                            return swReg.pushManager.subscribe({
                                userVisibleOnly: true,
                                applicationServerKey: this.urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                            });
                        } else {
                            return sub;
                        }
                    })
                    .then(subscription => {
                        if(subscription) {
                            fetch('<?= url("notification/subscribe") ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(subscription)
                            });
                        }
                    })
                    .catch(e => console.error('Push error:', e));
                },
                urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }
                    return outputArray;
                }
            }
        }

        function imagePreview() {
            return {
                show: false,
                src: '',
                title: '',
                open(src, title) {
                    this.src = src;
                    this.title = title || 'Pratinjau Gambar';
                    this.show = true;
                },
                download() {
                    const a = document.createElement('a');
                    a.href = this.src;
                    a.download = this.src.split('/').pop();
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            }
        }
    </script>

    <!-- Global Helpers -->
    <script>
        function confirmSubmit(form, title = 'Simpan perubahan?') {
            Swal.fire({
                title: title,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        function confirmLink(event, title = 'Lanjutkan?') {
            event.preventDefault();
            const url = event.currentTarget.href;
            Swal.fire({
                title: title,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
            return false;
        }
    </script>

    <!-- Global Image Preview Modal -->
    <div x-data="imagePreview()" 
         @image-preview.window="open($event.detail.src, $event.detail.title)"
         x-show="show" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @keydown.escape.window="show = false"
         x-cloak>
         
        <div class="relative bg-white rounded-[2rem] shadow-2xl overflow-hidden max-w-5xl w-full max-h-[90vh] flex flex-col" @click.away="show = false">
            <!-- Header -->
            <div class="px-8 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900" x-text="title"></h3>
                <div class="flex gap-2">
                    <button @click="download()" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all" title="Download Gambar">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </button>
                    <button @click="show = false" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
            
            <!-- Content -->
            <div class="flex-1 overflow-auto p-4 bg-gray-100/50 flex items-center justify-center min-h-0">
                <img :src="src" class="max-w-full max-h-full object-contain rounded-lg shadow-sm" alt="Preview">
            </div>
        </div>
    </div>
</body>
</html>
