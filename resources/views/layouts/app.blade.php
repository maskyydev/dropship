<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="midtrans-client-key" content="{{ config('services.midtrans.clientKey') }}">
    <title>Dropship Solution - {{ $title ?? 'Dashboard' }}</title>
    
    <!-- ✅ Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    {{-- Include Navbar --}}
    @include('layouts.navbar')

    <!-- Main Content -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
<script src="https://unpkg.com/alpinejs" defer></script>
</body>
</html>
