<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SIT-PSB PPRTQ Raudlatul Falah' ?></title>
    <!-- Instrument Sans Font -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Global Button Rounding */
        button, .btn, input[type="submit"], input[type="button"], a.btn {
            border-radius: 1rem !important; /* Matches rounded-2xl */
        }
        
        /* Custom SweetAlert2 Style */
        .swal2-popup {
            border-radius: 2rem !important;
            font-family: 'Instrument Sans', sans-serif !important;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <?= $content ?? '' ?>

</body>
</html>
