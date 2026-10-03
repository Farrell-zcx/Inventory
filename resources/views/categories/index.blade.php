@extends('layouts.app')

@section('title', 'Kategori')

@section('header_actions')
@can('manage-master-data')
<a href="{{ route('categories.create') }}" class="bg-primary text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all ml-2">
    <span class="material-symbols-outlined">add</span> Tambah Kategori
</a>
@endcan
@endsection

@section('content')
<div class="glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg" id="polling-container" hx-get="{{ request()->fullUrl() }}" hx-trigger="every 5s" hx-select="#polling-container" hx-swap="outerHTML">
    <div class="p-4 border-b border-black/5 flex flex-wrap items-center justify-between gap-4 bg-white/50">
        <span class="text-sm font-medium text-on-surface">Daftar Kategori</span>
    </div>
    <div class="flex-1 overflow-auto p-4 flex flex-col gap-2">
        @forelse($categories as $cat)
        <div class="glass-row rounded-xl p-4 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-lg bg-black/5 border border-black/5 flex items-center justify-center shrink-0 overflow-hidden text-outline group-hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-[24px]">category</span>
            </div>
            
            <div class="flex-1 min-w-0">
                <h4 class="text-on-surface font-semibold truncate text-base group-hover:text-primary transition-colors">{{ $cat->name }}</h4>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                @can('manage-master-data')
                <a href="{{ route('categories.edit', $cat) }}" class="p-2 text-outline hover:text-secondary rounded-lg hover:bg-secondary/10 transition-colors" title="Edit">
                    <span class="material-symbols-outlined">edit</span>
                </a>
                @endcan
                @can('delete-master-data')
                <form action="{{ route('categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');" class="m-0 p-0">
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
            <p>Belum ada data kategori.</p>
        </div>
        @endforelse
    </div>
    
    @if($categories->hasPages())
    <div class="p-4 border-t border-black/5 bg-white/30">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
