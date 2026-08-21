<!DOCTYPE html>
<html lang="vi" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Commerce System') - TMDT Laravel</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Lucide Icons (Chính là Lucide SVG trong hệ sinh thái Lucide-React / Shadcn) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Project Dedicated Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @yield('styles')
</head>
<body class="d-flex flex-column h-100">
    <!-- Top Navigation Component -->
    <x-navbar />

    <!-- Main Content Container -->
    <main class="container py-3 pb-5 flex-shrink-0">
        <!-- Flash Alert Component -->
        <x-alert />

        <!-- Dynamic Page Content -->
        @yield('content')
    </main>

    <!-- Footer Component -->
    <x-footer />

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Lucide Icons Init -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
