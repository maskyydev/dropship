<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Dropship Solution</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm fixed top-0 inset-x-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <button id="sidebarToggle" class="sm:hidden text-indigo-600 text-xl focus:outline-none">☰</button>
                <h1 class="text-lg font-bold text-indigo-600">Admin Panel</h1>
            </div>
            <div class="hidden sm:flex items-center space-x-6">
                <!-- <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">Dashboard</a> -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-red-600">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Page Wrapper -->
    <div class="flex pt-16 min-h-screen">
        <!-- Sidebar -->
        <aside id="adminSidebar" class="bg-indigo-800 text-white w-64 space-y-4 hidden sm:block sm:flex-shrink-0 z-20 sm:translate-x-0 transition-transform duration-300">
            @include('layouts.sidebar')
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>
