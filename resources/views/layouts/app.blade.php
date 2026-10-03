<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTORY - @yield('title', 'Global Control Center')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@600;700;900&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- HTMX for Real-time Polling -->
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
    <script>
        document.addEventListener('htmx:configRequest', (event) => {
            event.detail.headers['X-CSRF-TOKEN'] = '{{ csrf_token() }}';
        });
    </script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-background text-on-surface font-body-lg h-screen flex overflow-hidden selection:bg-primary-container selection:text-black">

<!-- SideNavBar -->
<aside class="bg-surface-container/50 backdrop-blur-2xl text-primary font-body-lg docked left-0 w-60 border-r border-black/5 shadow-xl fixed top-0 h-full flex-col z-40 hidden md:flex">
    <div class="p-6 border-b border-black/5">
        <h1 class="font-headline-md text-headline-md font-black text-primary tracking-tight">INVENTORY</h1>
        <p class="font-code-label text-code-label text-on-surface-variant mt-2 uppercase">Global Control Center</p>
    </div>
    
    <nav class="flex-1 py-6 flex flex-col gap-2 px-4 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('dashboard') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">dashboard</span>
            <span class="font-code-label text-code-label">Dashboard</span>
        </a>
        <a href="{{ route('items.index') }}" class="{{ request()->routeIs('items.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('items.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('items.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">inventory_2</span>
            <span class="font-code-label text-code-label">Inventory</span>
        </a>
        <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('categories.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('categories.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">category</span>
            <span class="font-code-label text-code-label">Categories</span>
        </a>
        <a href="{{ route('locations.index') }}" class="{{ request()->routeIs('locations.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('locations.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('locations.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">location_on</span>
            <span class="font-code-label text-code-label">Locations</span>
        </a>
        <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('suppliers.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('suppliers.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">local_shipping</span>
            <span class="font-code-label text-code-label">Suppliers</span>
        </a>
        <a href="{{ route('purchase_orders.index') }}" class="{{ request()->routeIs('purchase_orders.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('purchase_orders.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('purchase_orders.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">shopping_cart</span>
            <span class="font-code-label text-code-label">Purchase Orders</span>
        </a>
        <a href="{{ route('stock_transactions.index') }}" class="{{ request()->routeIs('stock_transactions.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('stock_transactions.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('stock_transactions.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">swap_horiz</span>
            <span class="font-code-label text-code-label">Transactions</span>
        </a>
        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group">
            <span class="material-symbols-outlined {{ request()->routeIs('reports.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('reports.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">analytics</span>
            <span class="font-code-label text-code-label">Reports</span>
        </a>
        @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'bg-white/50 text-primary border-l-4 border-primary shadow-sm font-bold' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface' }} py-3 px-4 flex items-center gap-3 transition-all rounded-lg group mt-4 border-t border-black/5 pt-4">
            <span class="material-symbols-outlined {{ request()->routeIs('admin.pengguna.*') ? 'text-primary' : 'text-outline group-hover:text-primary' }} transition-colors" style="{{ request()->routeIs('admin.pengguna.*') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">manage_accounts</span>
            <span class="font-code-label text-code-label">Kelola Pengguna</span>
        </a>
        @endif
    </nav>
    
    <div class="p-4 border-t border-black/5 flex flex-col gap-2">
        <a href="{{ route('logout') }}" class="w-full text-left text-on-surface-variant hover:bg-black/5 hover:text-error py-2 px-4 flex items-center gap-3 transition-all rounded-lg text-sm group">
            <span class="material-symbols-outlined text-outline group-hover:text-error transition-colors">logout</span>
            <span class="font-code-label text-code-label">Log Out</span>
        </a>
        
        <div class="mt-4 flex items-center gap-3 px-2">
            <div class="w-8 h-8 rounded-full border border-primary bg-primary-container text-on-primary-container flex items-center justify-center font-bold">
                @if(auth()->check())
                    {{ substr(auth()->user()->name, 0, 1) }}
                @else
                    U
                @endif
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-on-surface font-medium">{{ auth()->check() ? auth()->user()->name : 'User' }}</span>
                <span class="text-[10px] text-on-surface-variant">{{ auth()->check() ? auth()->user()->role : 'Admin' }}</span>
            </div>
        </div>
    </div>
</aside>

<!-- Top Nav (Mobile Fallback) -->
<nav class="md:hidden flex justify-between items-center w-full px-margin-mobile py-4 fixed top-0 z-40 bg-surface/80 backdrop-blur-xl border-b border-outline-variant/30 shadow-sm">
    <div class="font-display-lg-mobile text-display-lg-mobile text-primary tracking-wider">INVENTORY</div>
    <div class="flex items-center gap-4">
        <button class="text-on-surface-variant hover:text-primary transition-colors relative">
            <span class="material-symbols-outlined">notifications</span>
            <span class="absolute top-0 right-0 w-2 h-2 bg-primary-container rounded-full status-dot-green"></span>
        </button>
        <div class="w-8 h-8 rounded-full border border-primary bg-primary-container text-on-primary-container flex items-center justify-center font-bold text-xs">
            @if(auth()->check())
                {{ substr(auth()->user()->name, 0, 1) }}
            @else
                U
            @endif
        </div>
    </div>
</nav>

<!-- Main Content Area -->
<main class="flex-1 md:ml-60 h-screen overflow-y-auto pt-[72px] md:pt-0 relative bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-surface-container-high/40 via-background to-background">
    <!-- Desktop Header -->
    <header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md border-b border-outline-variant/30 px-6 py-4 hidden md:flex justify-between items-center">
        <div class="flex items-center gap-3">
            <h2 class="font-headline-md text-headline-md text-on-surface">@yield('title', 'Overview')</h2>
            @yield('header_badge')
        </div>
        
        <div class="flex items-center gap-6">
            <form action="{{ url()->current() }}" method="GET" class="relative hidden lg:block group" id="search-form">
                <button type="submit" class="absolute left-1 top-1/2 -translate-y-1/2 p-2 text-on-surface-variant text-[20px] group-focus-within:text-secondary transition-colors hover:text-primary z-10 flex items-center justify-center">
                    <span class="material-symbols-outlined">search</span>
                </button>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                    hx-get="{{ url()->current() }}" 
                    hx-trigger="keyup changed delay:500ms, search" 
                    hx-target="#polling-container" 
                    hx-select="#polling-container"
                    hx-swap="outerHTML"
                    hx-include="closest form"
                    class="bg-surface border border-outline-variant/50 rounded-lg pl-10 pr-4 py-2 w-64 text-sm focus:outline-none focus:border-secondary focus:ring-1 focus:ring-secondary transition-all placeholder:text-on-surface-variant/70 text-on-surface font-code-label shadow-inner" />
                @if(request()->except('search'))
                    @foreach(request()->except('search') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                @endif
            </form>
            
            <div class="flex items-center gap-3">
                @yield('header_actions')
            </div>
        </div>
    </header>
    
    <!-- Flash Messages Area -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.duration.500ms class="m-6 p-4 rounded-lg bg-primary-container/20 border border-primary text-primary font-medium flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.duration.500ms class="m-6 p-4 rounded-lg bg-error-container/20 border border-error text-error font-medium flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined">error</span>
            {{ session('error') }}
        </div>
    @endif

    <!-- Content Canvas -->
    <div class="p-6 md:p-8 relative min-h-full flex flex-col gap-6">
        @yield('content')
    </div>
</main>

</body>
</html>

