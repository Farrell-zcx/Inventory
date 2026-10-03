@extends('layouts.app')

@section('title', 'Detail Transaksi #' . $stockTransaction->id)

@section('header_actions')
<a href="{{ route('stock_transactions.index') }}" class="glass-panel px-4 py-2 rounded-lg text-on-surface border border-black/10 hover:bg-black/5 text-sm transition-colors shadow-sm flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 relative">
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Info Umum -->
    <div class="glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg p-6 relative z-10">
        <h3 class="text-lg font-semibold text-on-surface mb-4 border-b border-black/5 pb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">info</span> Informasi Umum
        </h3>
        <div class="flex flex-col gap-4 text-sm">
            <div class="flex flex-col border-b border-black/5 pb-3">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Tipe Transaksi</span>
                <div>
                    @if($stockTransaction->type == 'masuk')
                        <span class="bg-secondary/10 text-secondary border border-secondary/20 px-3 py-1 rounded-full font-semibold text-xs">Masuk</span>
                    @else
                        <span class="bg-error/10 text-error border border-error/20 px-3 py-1 rounded-full font-semibold text-xs">Keluar</span>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-col border-b border-black/5 pb-3">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Barang</span>
                <span class="font-semibold text-on-surface text-base">{{ $stockTransaction->item->code }} - {{ $stockTransaction->item->name }}</span>
            </div>
            
            <div class="flex flex-col border-b border-black/5 pb-3">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Jumlah</span>
                <span class="font-bold text-on-surface text-lg">{{ $stockTransaction->quantity }} <span class="text-sm font-normal text-on-surface-variant">{{ $stockTransaction->item->unit }}</span></span>
            </div>
            
            <div class="flex flex-col border-b border-black/5 pb-3">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Tanggal Transaksi</span>
                <span class="text-on-surface">{{ $stockTransaction->transaction_date->format('d F Y') }}</span>
            </div>
            
            <div class="flex flex-col border-b border-black/5 pb-3">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Dibuat Oleh (PIC)</span>
                <span class="text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-[16px] text-on-surface-variant">person</span> {{ $stockTransaction->user->name }}</span>
            </div>
            
            <div class="flex flex-col">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Waktu Sistem</span>
                <span class="text-on-surface-variant">{{ $stockTransaction->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Detail Khusus -->
    <div class="glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg p-6 relative z-10">
        <h3 class="text-lg font-semibold text-on-surface mb-4 border-b border-black/5 pb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary">feed</span> Detail {{ $stockTransaction->type == 'masuk' ? 'Penerimaan' : 'Pengeluaran' }}
        </h3>
        
        <div class="flex flex-col gap-4 text-sm">
            @if($stockTransaction->type == 'masuk')
                @if(!$stockTransaction->reference_id)
                    <div class="flex flex-col border-b border-black/5 pb-3">
                        <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Tipe Masuk</span>
                        <span class="text-on-surface font-semibold text-secondary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">add_shopping_cart</span> Pembelian / Restok</span>
                    </div>
                    <div class="flex flex-col border-b border-black/5 pb-3">
                        <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Supplier</span>
                        <span class="text-on-surface">{{ $stockTransaction->supplier ? $stockTransaction->supplier->name : '-' }}</span>
                    </div>
                    @if($stockTransaction->unit_price || $stockTransaction->total_price)
                        <div class="grid grid-cols-2 gap-4 border-b border-black/5 pb-3">
                            <div class="flex flex-col">
                                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Harga Satuan</span>
                                <span class="text-on-surface">Rp {{ number_format($stockTransaction->unit_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Total Harga</span>
                                <span class="text-on-surface font-semibold text-secondary">Rp {{ number_format($stockTransaction->total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="flex flex-col border-b border-black/5 pb-3">
                        <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Tipe Masuk</span>
                        <span class="text-on-surface font-semibold text-primary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">assignment_return</span> Pengembalian Pinjaman</span>
                    </div>
                    <div class="flex flex-col border-b border-black/5 pb-3">
                        <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Ref Peminjaman</span>
                        @if($stockTransaction->reference)
                            <a href="{{ route('stock_transactions.show', $stockTransaction->reference->id) }}" class="text-secondary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">link</span> ID #{{ $stockTransaction->reference->id }} (Dipinjam oleh: {{ $stockTransaction->reference->borrower_name }})
                            </a>
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </div>
                @endif
            @else
                <div class="flex flex-col border-b border-black/5 pb-3">
                    <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Peminjam / Tujuan</span>
                    <span class="text-on-surface font-semibold">{{ $stockTransaction->borrower_name ?? '-' }}</span>
                </div>
                
                <div class="flex flex-col border-b border-black/5 pb-3">
                    <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Status Keluar</span>
                    <div>
                        @if($stockTransaction->status == 'dipinjam')
                            <span class="bg-warning/10 text-warning border border-warning/20 px-3 py-1 rounded-full font-semibold text-xs">Dipinjam</span>
                        @elseif($stockTransaction->status == 'dikembalikan')
                            <span class="bg-primary/10 text-primary border border-primary/20 px-3 py-1 rounded-full font-semibold text-xs">Dikembalikan</span>
                        @elseif($stockTransaction->status == 'habis')
                            <span class="bg-black/5 text-on-surface-variant border border-black/10 px-3 py-1 rounded-full font-semibold text-xs">Habis Terpakai</span>
                        @else
                            <span class="text-on-surface-variant">-</span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex flex-col border-b border-black/5 pb-3">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-1">Catatan</span>
                <p class="text-on-surface">{{ $stockTransaction->notes ?: '-' }}</p>
            </div>
            
            <div class="flex flex-col">
                <span class="font-code-label text-code-label text-on-surface-variant uppercase mb-2">Dokumen Lampiran</span>
                <div>
                    @if($stockTransaction->document_path)
                        <a href="{{ asset('storage/' . $stockTransaction->document_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 rounded-lg text-sm font-semibold transition-colors">
                            <span class="material-symbols-outlined text-[18px]">description</span> Lihat Dokumen
                        </a>
                    @else
                        <span class="text-on-surface-variant text-sm italic">Tidak ada dokumen lampiran.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
