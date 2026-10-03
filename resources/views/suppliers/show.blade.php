@extends('layouts.app')

@section('title', 'Detail Supplier')

@section('content')
<div class="max-w-5xl mx-auto relative">
    <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="glass-panel rounded-2xl border border-black/5 overflow-hidden shadow-2xl relative z-10 mb-6">
        <div class="p-6 border-b border-black/5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-on-surface flex items-center gap-2">
                    {{ $supplier->name }}
                </h2>
                <div class="flex items-center gap-4 mt-2 text-sm text-on-surface-variant">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">call</span> {{ $supplier->contact }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">map</span> {{ $supplier->address }}</span>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('suppliers.index') }}" class="px-4 py-2 rounded-lg text-on-surface-variant hover:bg-black/5 transition-colors font-medium flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
                </a>
                @can('manage-suppliers')
                <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-primary text-white font-bold py-2 px-4 rounded-lg hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all flex items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                </a>
                @endcan
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Info -->
            <div class="md:col-span-1 space-y-4">
                <div class="bg-black/5 p-4 rounded-xl border border-black/5">
                    <p class="text-xs text-on-surface-variant font-code-label uppercase mb-2">Catatan</p>
                    <p class="text-sm text-on-surface">{{ $supplier->notes ?: 'Tidak ada catatan khusus.' }}</p>
                </div>

                @if($supplier->created_at)
                <div class="text-xs text-on-surface-variant mt-4">
                    Ditambahkan pada: {{ $supplier->created_at->format('d M Y H:i') }}
                </div>
                @endif
            </div>

            <!-- Right: Stock History -->
            <div class="md:col-span-2">
                <h3 class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-4 border-b border-black/5 pb-2">Riwayat Transaksi Masuk (Penerimaan)</h3>
                
                <div class="space-y-3">
                    @forelse($transactions as $tx)
                    <div class="glass-row rounded-xl p-3 flex items-center gap-3 border border-black/5">
                        <div class="w-10 h-10 rounded-lg bg-primary/10 text-primary border-primary/20 border flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">assignment_return</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-on-surface">{{ $tx->item->name }}</span>
                                <span class="font-bold text-primary">
                                    +{{ $tx->quantity }} {{ $tx->item->unit }}
                                </span>
                            </div>
                            <p class="text-sm text-on-surface-variant">
                                Tanggal: {{ $tx->transaction_date->format('d M Y') }} | Oleh: {{ $tx->user->name }}
                            </p>
                            @if($tx->notes)
                                <p class="text-xs text-on-surface mt-1 italic">{{ $tx->notes }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">history</span>
                        <p class="text-sm">Belum ada riwayat transaksi dari supplier ini.</p>
                    </div>
                    @endforelse
                </div>

                @if($transactions->hasPages())
                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
