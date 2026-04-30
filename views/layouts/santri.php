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
    <title><?= $seo_title ?: ($title ?? 'Dashboard Santri - SIT-PSB') ?></title>
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
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .font-arabic { font-family: 'Amiri', serif !important; }
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: width 0.3s ease-in-out; }
        
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
        .swal2-confirm, .swal2-cancel {
            border-radius: 1.25rem !important;
            padding: 0.85rem 2.5rem !important;
            font-weight: 800 !important;
            font-size: 0.85rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            transition: all 0.3s ease !important;
        }
        .swal2-confirm {
            background: #059669 !important;
            box-shadow: 0 10px 20px -5px rgba(5, 150, 105, 0.3) !important;
        }
        .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 15px 25px -5px rgba(5, 150, 105, 0.4) !important;
        }
        .swal2-cancel {
            background: rgba(0, 0, 0, 0.05) !important;
            color: #6b7280 !important;
        }
        .swal2-cancel:hover {
            background: rgba(0, 0, 0, 0.1) !important;
        }
    </style>
</head>
<body x-data="{ 
    sidebarOpen: window.innerWidth >= 1024, 
    currentStep: <?= $current_step ?? 1 ?>, 
    activeTab: (<?= $current_step ?? 1 ?> == 4 && '<?= $user['status_psb'] ?>' === 'lulus' && !localStorage.getItem('result_revealed_<?= $user['id'] ?>')) ? 3 : <?= $current_step ?? 1 ?> 
}" class="bg-[#f4f7f6] text-gray-800 antialiased h-screen flex overflow-hidden">
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
            flatpickr.localize(flatpickr.l10ns.id);
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

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden" x-cloak></div>

    <!-- Sidebar Khusus Santri (Progress Stepper) -->
    <?php require __DIR__ . '/../components/sidebar_santri.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Navbar -->
        <?php require __DIR__ . '/../components/navbar_santri.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-3 sm:p-8">
            <div class="max-w-5xl mx-auto">
                <?= display_flash_message() ?>
                <?= $content ?? '' ?>
            </div>
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
                    setInterval(() => this.fetchNotifs(), 15000); // Cek setiap 15 detik
                    
                    if ('serviceWorker' in navigator && 'PushManager' in window && VAPID_PUBLIC_KEY) {
                        navigator.serviceWorker.register('<?= url("service-worker.js") ?>', { scope: '<?= url("/") ?>' })
                        .then(swReg => {
                            swReg.update();
                            if(Notification.permission === 'granted') this.subscribeUser(swReg);
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
                    fetch('<?= url("notification/mark-read/") ?>' + id).then(() => { this.fetchNotifs(); });
                },
                markAllRead() {
                    if (this.count > 0) {
                        fetch('<?= url("notification/mark-all-read") ?>', { method: 'POST' }).then(() => { this.fetchNotifs(); });
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
                        }
                        return sub;
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

        function confirmSubmit(form, title = 'Simpan perubahan?', text = '') {
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#059669'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }

        function confirmLink(event, title = 'Lanjutkan?', text = '') {
            event.preventDefault();
            const url = event.currentTarget.href;
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Iya',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#059669'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
            return false;
        }
    </script>
</body>
</html>
