<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    
    <!-- Tambahkan CSS khusus admin -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-200">
    <div class="min-h-screen flex flex-col">
        <nav class="bg-red-600 p-4 text-white">
            <div class="container mx-auto">
                <h1 class="text-lg font-semibold">Admin Panel</h1>
            </div>
        </nav>

        <main class="flex-grow">
            {{ $slot }}
        </main>

        <footer class="bg-gray-800 text-white text-center p-3">
            © 2025 Admin Panel - All Rights Reserved
        </footer>
    </div>
</body>
</html>
