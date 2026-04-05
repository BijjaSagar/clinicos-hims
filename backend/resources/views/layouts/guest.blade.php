<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Sign In') — ClinicOS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="image/png" href="{{ asset('images/clinicos-logo.png') }}" />
    <meta name="theme-color" content="#ffffff" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Sora', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="font-sans antialiased min-h-screen bg-white text-gray-900">
    {{-- Pure white canvas so the logo reads as one surface with the page (replace PNG with transparent asset if you still see a white rectangle in the file). --}}
    <main class="min-h-screen w-full flex flex-col items-center px-4 sm:px-6 py-8 sm:py-12">
        @yield('content')
    </main>

    <footer class="pb-8 pt-2 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} RH Technology, Pune · ABDM-ready EMR
    </footer>

    <script>
    (function () {
        console.log('[ClinicOS][guest-layout]', { path: '{{ request()->path() }}', title: document.title });
    })();
    </script>
    @stack('scripts')
</body>
</html>
