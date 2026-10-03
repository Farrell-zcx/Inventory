@extends('layouts.app')

@section('title', 'Riwayat Transaksi Stok')

@section('header_actions')
@can('manage-transactions')
<a href="{{ route('stock_transactions.create') }}" class="bg-primary text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all ml-2">
    <span class="material-symbols-outlined">add</span> Buat Transaksi Baru
</a>
@endcan
@endsection

@section('content')
<div class="glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg mb-6 p-4">
    <div class="flex gap-3">
        <a href="{{ route('stock_transactions.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('type') ? 'bg-primary text-white shadow-[0_0_10px_rgba(57,255,20,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Semua</a>
        <a href="{{ route('stock_transactions.index', ['type' => 'masuk']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('type') == 'masuk' ? 'bg-[#22c55e] dark:bg-[#4ade80] text-white shadow-[0_0_10px_rgba(34,197,94,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Masuk</a>
        <a href="{{ route('stock_transactions.index', ['type' => 'keluar']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('type') == 'keluar' ? 'bg-[#ef4444] dark:bg-[#f87171] text-white shadow-[0_0_10px_rgba(239,68,68,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Keluar</a>
    </div>
</div>

<div class="glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg" id="polling-container" hx-get="{{ request()->fullUrl() }}" hx-trigger="every 5s" hx-select="#polling-container" hx-swap="outerHTML">
    <div class="p-4 border-b border-black/5 flex flex-wrap items-center justify-between gap-4 bg-white/50">
        <span class="text-sm font-medium text-on-surface">Daftar Transaksi</span>
    </div>
    <div class="flex-1 overflow-auto p-4 flex flex-col gap-2">
        @forelse ($transactions as $transaction)
        <div class="glass-row rounded-xl p-4 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-lg {{ $transaction->type == 'masuk' ? 'bg-[#22c55e]/10 dark:bg-[#4ade80]/10 border-[#22c55e]/20 dark:border-[#4ade80]/20 text-[#22c55e] dark:text-[#4ade80]' : 'bg-[#ef4444]/10 dark:bg-[#f87171]/10 border-[#ef4444]/20 dark:border-[#f87171]/20 text-[#ef4444] dark:text-[#f87171]' }} border flex items-center justify-center shrink-0 overflow-hidden">
                <span class="material-symbols-outlined text-[24px]">
                    {{ $transaction->type == 'masuk' ? ($transaction->reference_id ? 'assignment_return' : 'add_shopping_cart') : 'outbox' }}
                </span>
            </div>
            
            <div class="flex-1 min-w-0">
                <h4 class="text-on-surface font-semibold truncate text-base group-hover:text-primary transition-colors">{{ $transaction->item->code }} - {{ $transaction->item->name }}</h4>
                <div class="flex items-center flex-wrap gap-3 text-xs text-on-surface-variant mt-1">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $transaction->transaction_date->format('d M Y') }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">inventory_2</span> 
                        @if($transaction->type == 'masuk' && $transaction->supplier_id)
                            Pembelian dari: {{ $transaction->supplier->name }}
                        @elseif($transaction->type == 'masuk' && $transaction->reference_id)
                            Pengembalian Pinjaman
                        @elseif($transaction->type == 'keluar' && $transaction->borrower_name)
                            Peminjam/Tujuan: {{ $transaction->borrower_name }}
                        @else
                            Internal
                        @endif
                    </span>
                    @if($transaction->status == 'dipinjam')
                        <span class="text-warning flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-warning"></div> Dipinjam</span>
                    @elseif($transaction->status == 'dikembalikan')
                        <span class="text-primary flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full status-dot-green"></div> Dikembalikan</span>
                    @elseif($transaction->status == 'habis')
                        <span class="text-on-surface-variant flex items-center gap-1"><div class="w-1.5 h-1.5 rounded-full bg-on-surface-variant"></div> Habis Terpakai</span>
                    @endif
                </div>
            </div>
            
            <div class="w-32 shrink-0 text-right pr-4">
                <p class="text-[10px] text-on-surface-variant uppercase font-code-label mb-1">Jumlah</p>
                <p class="text-on-surface text-lg font-bold">{{ $transaction->quantity }} <span class="text-sm font-normal text-on-surface-variant">{{ $transaction->item->unit }}</span></p>
                @if($transaction->total_price)
                    <p class="text-xs text-secondary font-medium mt-1">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                @endif
            </div>
            
            <div class="flex items-center gap-2 shrink-0 border-l border-black/10 pl-4">
                <a href="{{ route('stock_transactions.show', $transaction) }}" class="p-2 text-outline hover:text-primary rounded-lg hover:bg-primary/10 transition-colors" title="Detail">
                    <span class="material-symbols-outlined">visibility</span>
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-on-surface-variant">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-50">receipt_long</span>
            <p>Belum ada data transaksi.</p>
        </div>
        @endforelse
    </div>
    
    @if($transactions->hasPages())
    <div class="p-4 border-t border-black/5 bg-white/30">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
