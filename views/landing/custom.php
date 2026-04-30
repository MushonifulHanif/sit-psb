<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO & Metadata -->
    <title><?= $settings['nama_pesantren'] ?? 'SIT-PSB' ?> - Penerimaan Santri Baru</title>
    
    <!-- Dynamic Favicon -->
    <?php if(!empty($settings['site_favicon'])): ?>
        <link rel="icon" type="image/png" href="<?= asset($settings['site_favicon']) ?>">
    <?php endif; ?>

    <!-- Fonts -->
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

    <!-- Custom Styles from Builder -->
    <style>
        body { font-family: 'Instrument Sans', sans-serif; margin: 0; padding: 0; }
        <?= $builderPage['css_content'] ?>
    </style>
</head>
<body class="bg-white text-gray-900 selection:bg-emerald-100 selection:text-emerald-900">

    <!-- Custom HTML from Builder -->
    <?= $builderPage['html_content'] ?>

    <!-- SweetAlert2 for Interactions -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
