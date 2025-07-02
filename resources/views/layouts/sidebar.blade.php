<div class="flex flex-col h-full">
    <div class="p-4 border-b border-indigo-700">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
    </div>

    <nav class="flex-1 p-4 space-y-2 text-sm">
        @php
            $navItems = [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home'],
                ['label' => 'Data Barang', 'route' => 'admin.products.index', 'icon' => 'archive'],
                ['label' => 'Data Terjual', 'route' => 'admin.sales.index', 'icon' => 'shopping-cart'],
                ['label' => 'Manajemen User', 'route' => 'admin.users.index', 'icon' => 'users']
            ];
        @endphp

        @foreach($navItems as $item)
        <a href="{{ route($item['route']) }}" class="flex items-center space-x-2 p-2 rounded hover:bg-indigo-700 {{ request()->routeIs(Str::before($item['route'], '.').'*.') ? 'bg-indigo-900' : '' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                @switch($item['icon'])
                    @case('home') <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"></path> @break
                    @case('archive') <path d="M20 7l-8-4-8 4m16 0l-8 4M4 7v10l8 4 8-4V7"></path> @break
                    @case('shopping-cart') <path d="M3 3h2l.4 2M7 13h10l4-8H5.4"></path> @break
                    @case('users') <path d="M17 20h5v-1a4 4 0 00-5-3.87M9 20H4v-1a4 4 0 015-3.87"></path> @break
                @endswitch
            </svg>
            <span>{{ $item['label'] }}</span>
        </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-indigo-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center space-x-2 w-full p-2 rounded hover:bg-indigo-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4"></path>
                </svg>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>
