@extends('layouts.app')

@section('title', 'Data Barang')

@section('header_badge')
<span class="bg-surface-variant text-on-surface-variant font-code-label text-code-label px-2 py-1 rounded-md text-xs">Total: {{ $items->total() }}</span>
@endsection

@section('header_actions')
@can('manage-master-data')
<a href="{{ route('items.create') }}" class="bg-primary text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all ml-2">
    <span class="material-symbols-outlined">add</span> Tambah Barang
</a>
@endcan
@endsection

@section('content')
<!-- Left Sidebar Filters & Main Data Table Area -->
<div class="flex-1 flex flex-col lg:flex-row gap-6 relative">
    
    <!-- Atmospheric Background Element -->
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Left Sidebar Filters -->
    <div class="w-full lg:w-64 shrink-0 flex flex-col gap-6 overflow-y-auto pr-2">
        <form method="GET" action="{{ route('items.index') }}" class="flex flex-col gap-6" id="filterForm">
            <!-- Filter Group: Kondisi -->
            <div class="flex flex-col gap-3">
                <h3 class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider text-[10px]">Kondisi Barang</h3>
                <div class="grid grid-cols-2 gap-2">
                    <button type="submit" name="condition" value="" class="glass-panel rounded-lg p-3 flex justify-between items-center {{ request('condition') == '' ? 'border border-primary/30 bg-primary/5 text-primary' : 'text-on-surface hover:border-black/10' }} shadow-sm">
                        <span class="text-sm font-medium">Semua</span>
                    </button>
                    <button type="submit" name="condition" value="baik" class="glass-panel rounded-lg p-3 flex justify-between items-center {{ request('condition') == 'baik' ? 'border border-primary/30 bg-primary/5 text-primary' : 'text-on-surface hover:border-black/10' }} shadow-sm">
                        <span class="text-sm">Baik</span>
                    </button>
                    <button type="submit" name="condition" value="rusak_ringan" class="glass-panel rounded-lg p-3 flex justify-between items-center {{ request('condition') == 'rusak_ringan' ? 'border border-primary/30 bg-primary/5 text-primary' : 'text-on-surface hover:border-black/10' }} shadow-sm">
                        <span class="text-sm">Rusak Ringan</span>
                    </button>
                    <button type="submit" name="condition" value="rusak_berat" class="glass-panel rounded-lg p-3 flex justify-between items-center {{ request('condition') == 'rusak_berat' ? 'border border-primary/30 bg-primary/5 text-primary' : 'text-on-surface hover:border-black/10' }} shadow-sm">
                        <span class="text-sm">Rusak Berat</span>
                    </button>
                </div>
            </div>

            <!-- Filter Group: Sort By -->
            <div class="flex flex-col gap-3">
                <h3 class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider text-[10px]">Urutkan</h3>
                <div class="relative shadow-sm">
                    <select name="sort" onchange="document.getElementById('filterForm').submit()" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface appearance-none focus:outline-none focus:border-secondary">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama: A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama: Z-A</option>
                        <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stok Terbanyak</option>
                        <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stok Tersedikit</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">arrow_drop_down</span>
                </div>
            </div>
            
            <a href="{{ route('items.index') }}" class="mt-4 flex items-center justify-center gap-2 text-on-surface-variant hover:text-on-surface transition-colors text-sm py-2">
                <span class="material-symbols-outlined text-[18px]">refresh</span> Reset Filter
            </a>
        </form>
    </div>

    <!-- Main Data Table Area -->
    <div class="flex-1 flex flex-col min-w-0 glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg" id="polling-container" hx-get="{{ request()->fullUrl() }}" hx-trigger="every 5s" hx-select="#polling-container" hx-swap="outerHTML">
        <!-- Table Toolbar -->
        <div class="p-4 border-b border-black/5 flex flex-wrap items-center justify-between gap-4 bg-white/50">
            <div class="flex items-center gap-6">
                <span class="text-sm font-medium text-on-surface">Daftar Inventaris</span>
            </div>
        </div>

        <!-- Table Content -->
        <div class="flex-1 overflow-auto p-4 flex flex-col gap-2 relative">
            @forelse($items as $item)
            <!-- Data Row -->
            <div class="glass-row rounded-xl p-4 flex items-center gap-4 group">
                <div class="w-16 h-16 rounded-lg bg-black/5 border border-black/5 flex items-center justify-center shrink-0 overflow-hidden text-outline group-hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-[32px]">category</span>
                </div>
                
                <div class="flex-1 min-w-0">
                    <h4 class="text-on-surface font-semibold truncate text-base mb-1 group-hover:text-primary transition-colors">{{ $item->name }}</h4>
                    <div class="flex items-center flex-wrap gap-3 text-xs text-on-surface-variant">
                        <span class="bg-secondary/10 text-secondary border border-secondary/20 px-2 py-0.5 rounded font-code-label">{{ $item->code }}</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">folder</span> {{ $item->category->name }}</span>
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">inventory</span> Stok: <strong>{{ $item->current_stock }} {{ $item->unit }}</strong></span>
                        
                        @if($item->condition == 'baik')
                            <span class="flex items-center gap-1 text-primary"><div class="w-1.5 h-1.5 rounded-full status-dot-green"></div> Baik</span>
                        @elseif($item->condition == 'rusak_ringan')
                            <span class="flex items-center gap-1 text-secondary"><div class="w-1.5 h-1.5 rounded-full bg-secondary shadow-[0_0_5px_rgba(0,103,127,0.3)]"></div> Rusak Ringan</span>
                        @else
                            <span class="flex items-center gap-1 text-error"><div class="w-1.5 h-1.5 rounded-full status-dot-red"></div> Rusak Berat</span>
                        @endif
                    </div>
                </div>
                
                <div class="w-32 shrink-0 hidden md:block">
                    <p class="text-[10px] text-on-surface-variant uppercase font-code-label mb-1">Lokasi</p>
                    <p class="text-on-surface text-sm font-medium truncate" title="{{ $item->location->name }}">{{ $item->location->name }}</p>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('items.show', $item) }}" class="p-2 text-outline hover:text-primary rounded-lg hover:bg-primary/10 transition-colors" title="Detail">
                        <span class="material-symbols-outlined">visibility</span>
                    </a>
                    @can('manage-master-data')
                    <a href="{{ route('items.edit', $item) }}" class="p-2 text-outline hover:text-secondary rounded-lg hover:bg-secondary/10 transition-colors" title="Edit">
                        <span class="material-symbols-outlined">edit</span>
                    </a>
                    @endcan
                    @can('delete-master-data')
                    <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus barang ini?');" class="m-0 p-0">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-outline hover:text-error rounded-lg hover:bg-error/10 transition-colors" title="Hapus">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-on-surface-variant">
                <span class="material-symbols-outlined text-6xl mb-4 opacity-50">inventory_2</span>
                <p>Belum ada data barang.</p>
            </div>
            @endforelse
        </div>
        
        @if($items->hasPages())
        <div class="p-4 border-t border-black/5 bg-white/30">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
