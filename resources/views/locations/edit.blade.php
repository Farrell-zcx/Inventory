@extends('layouts.app')

@section('title', 'Edit Lokasi')

@section('header_actions')
<a href="{{ route('locations.index') }}" class="glass-panel px-4 py-2 rounded-lg text-on-surface border border-black/10 hover:bg-black/5 text-sm transition-colors shadow-sm flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
</a>
@endsection

@section('content')
<div class="w-full max-w-2xl mx-auto glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg p-6">
    <form action="{{ route('locations.update', $location) }}" method="POST" class="flex flex-col gap-4">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Nama Lokasi</label>
            <input type="text" name="name" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors" value="{{ old('name', $location->name) }}" required>
            @error('name') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        
        <div>
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors">{{ old('description', $location->description) }}</textarea>
            @error('description') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>
        
        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-primary text-white font-bold py-2 px-6 rounded-lg hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all">
                Perbarui
            </button>
        </div>
    </form>
</div>
@endsection
