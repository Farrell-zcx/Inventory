@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
<div class="max-w-5xl mx-auto relative">
    <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="glass-panel rounded-2xl border border-black/5 overflow-hidden shadow-2xl relative z-10 mb-6">
        <div class="p-6 border-b border-black/5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-on-surface flex items-center gap-2">
                    {{ $item->name }}
                    <span class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wider bg-secondary/10 text-secondary border border-secondary/20">
                        {{ $item->code }}
                    </span>
                </h2>
                <div class="flex items-center gap-3 mt-2 text-sm text-on-surface-variant">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">folder</span> {{ $item->category ? $item->category->name : '-' }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">place</span> {{ $item->location ? $item->location->name : '-' }}</span>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('items.index') }}" class="px-4 py-2 rounded-lg text-on-surface-variant hover:bg-black/5 transition-colors font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
                </a>
                @can('manage-master-data')
                <a href="{{ route('items.edit', $item) }}" class="bg-primary text-white font-bold py-2 px-4 rounded-lg hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                </a>
                @endcan
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Info -->
            <div class="md:col-span-1 space-y-4">
                <div class="bg-black/5 p-4 rounded-xl border border-black/5 text-center">
                    <p class="text-xs text-on-surface-variant font-code-label uppercase mb-1">Stok Tersedia</p>
                    <p class="text-4xl font-bold {{ $item->current_stock > 0 ? 'text-primary' : 'text-error' }}">{{ $item->current_stock }}</p>
                    <p class="text-sm text-on-surface-variant mt-1">{{ $item->unit }}</p>
                </div>
                
                <div class="bg-black/5 p-4 rounded-xl border border-black/5">
                    <p class="text-xs text-on-surface-variant font-code-label uppercase mb-2">Kondisi</p>
                    @if($item->condition == 'baik')
                        <span class="inline-flex items-center gap-1 text-primary font-medium bg-primary/10 px-3 py-1 rounded-lg border border-primary/20"><div class="w-2 h-2 rounded-full status-dot-green"></div> Baik</span>
                    @elseif($item->condition == 'rusak_ringan')
                        <span class="inline-flex items-center gap-1 text-warning font-medium bg-warning/10 px-3 py-1 rounded-lg border border-warning/20"><div class="w-2 h-2 rounded-full bg-warning"></div> Rusak Ringan</span>
                    @else
                        <span class="inline-flex items-center gap-1 text-error font-medium bg-error/10 px-3 py-1 rounded-lg border border-error/20"><div class="w-2 h-2 rounded-full status-dot-red"></div> Rusak Berat</span>
                    @endif
                </div>

                @if($item->created_at)
                <div class="text-xs text-on-surface-variant text-center mt-4">
                    Ditambahkan pada: {{ $item->created_at->format('d M Y H:i') }}
                </div>
                @endif
            </div>

            <!-- Right: Stock History -->
            <div class="md:col-span-2">
                <h3 class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-4 border-b border-black/5 pb-2">Riwayat Transaksi Stok</h3>
                
                <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    @forelse($item->stockTransactions as $tx)
                    <div class="glass-row rounded-xl p-3 flex items-center gap-3 border border-black/5">
                        <div class="w-10 h-10 rounded-lg {{ $tx->type == 'masuk' ? 'bg-primary/10 text-primary border-primary/20' : 'bg-error/10 text-error border-error/20' }} border flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">
                                {{ $tx->type == 'masuk' ? 'assignment_return' : 'outbox' }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-on-surface-variant">{{ $tx->transaction_date->format('d M Y') }}</span>
                                <span class="font-bold {{ $tx->type == 'masuk' ? 'text-primary' : 'text-error' }}">
                                    {{ $tx->type == 'masuk' ? '+' : '-' }}{{ $tx->quantity }} {{ $item->unit }}
                                </span>
                            </div>
                            <p class="text-sm text-on-surface">
                                @if($tx->type == 'masuk')
                                    Terima dari {{ $tx->supplier ? $tx->supplier->name : 'Internal' }}
                                @else
                                    Keluar/Dipinjam oleh: {{ $tx->borrower_name ?? 'Internal' }}
                                @endif
                            </p>
                            @if($tx->notes)
                                <p class="text-xs text-on-surface-variant mt-1 italic">{{ $tx->notes }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">history</span>
                        <p class="text-sm">Belum ada riwayat transaksi untuk barang ini.</p>
                        <p class="text-xs mt-1">Lakukan transaksi Masuk/Keluar untuk mengubah stok.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
