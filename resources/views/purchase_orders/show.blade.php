@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('content')
<div class="max-w-4xl mx-auto relative">
    <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="glass-panel rounded-2xl border border-black/5 overflow-hidden shadow-2xl relative z-10 mb-6" id="polling-container" hx-get="{{ request()->fullUrl() }}" hx-trigger="every 5s" hx-select="#polling-container" hx-swap="outerHTML">
        <div class="p-6 border-b border-black/5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-on-surface flex items-center gap-2">
                    {{ $purchaseOrder->po_number }}
                    <span class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wider
                        {{ $purchaseOrder->status == 'disetujui' ? 'bg-primary/20 text-primary' : 
                          ($purchaseOrder->status == 'ditolak' ? 'bg-error/20 text-error' : 
                          ($purchaseOrder->status == 'diajukan' ? 'bg-warning/20 text-warning' : 
                          ($purchaseOrder->status == 'selesai' ? 'bg-on-surface-variant/20 text-on-surface-variant' : 'bg-secondary/20 text-secondary'))) }}">
                        {{ $purchaseOrder->status }}
                    </span>
                </h2>
                <p class="text-on-surface-variant text-sm mt-1">Dibuat pada {{ $purchaseOrder->created_at->format('d M Y H:i') }} oleh {{ $purchaseOrder->requestedBy->name }}</p>
            </div>
            
            <div class="flex gap-2">
                @if($purchaseOrder->status == 'draft')
                    @can('create-po')
                    <a href="{{ route('purchase_orders.edit', $purchaseOrder) }}" class="bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">edit</span> Edit
                    </a>
                    <form action="{{ route('purchase_orders.submit', $purchaseOrder) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="bg-warning text-white px-4 py-2 rounded-lg font-bold shadow-[0_0_10px_rgba(245,158,11,0.3)] hover:bg-warning/80 transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">send</span> Ajukan
                        </button>
                    </form>
                    @endcan
                @endif
                
                @if($purchaseOrder->status == 'diajukan')
                    @can('approve-po')
                    <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="bg-error/10 text-error hover:bg-error hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">cancel</span> Tolak
                    </button>
                    <form action="{{ route('purchase_orders.approve', $purchaseOrder) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg font-bold shadow-[0_0_10px_rgba(57,255,20,0.3)] hover:bg-primary-fixed transition-colors flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span> Setujui
                        </button>
                    </form>
                    @endcan
                @endif
                
                @if($purchaseOrder->status == 'disetujui')
                    @can('manage-transactions')
                    <!-- Integrasi ke Transaksi Masuk -->
                    <form action="{{ route('purchase_orders.complete', $purchaseOrder) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg font-bold shadow-[0_0_10px_rgba(57,255,20,0.3)] hover:bg-primary-fixed transition-colors flex items-center gap-1" title="Tutup PO dan catat penerimaan barang fisik">
                            <span class="material-symbols-outlined text-[18px]">inventory</span> Terima Barang & Selesaikan PO
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>
        
        @if($purchaseOrder->rejection_reason)
        <div class="bg-error/10 border-l-4 border-error p-4">
            <h4 class="text-error font-bold flex items-center gap-2"><span class="material-symbols-outlined">error</span> Alasan Penolakan</h4>
            <p class="text-on-surface mt-1 ml-8">{{ $purchaseOrder->rejection_reason }}</p>
            <p class="text-on-surface-variant text-xs mt-1 ml-8">Oleh: {{ $purchaseOrder->approvedBy->name ?? 'Unknown' }}</p>
        </div>
        @endif

        <div class="p-6">
            @if($purchaseOrder->notes)
            <div class="mb-6 bg-black/5 p-4 rounded-xl border border-black/5">
                <h4 class="font-code-label text-xs uppercase tracking-wider text-on-surface-variant mb-1">Catatan Tambahan</h4>
                <p class="text-on-surface">{{ $purchaseOrder->notes }}</p>
            </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <h3 class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider">Daftar Barang Diminta</h3>
                <div class="text-right">
                    <p class="text-xs text-on-surface-variant font-code-label uppercase tracking-wider">Total Estimasi Harga</p>
                    <p class="text-xl font-bold text-secondary">Rp {{ number_format($purchaseOrder->items->sum(function($item) { return $item->estimated_price * $item->quantity_requested; }), 0, ',', '.') }}</p>
                </div>
            </div>
            
            <div class="space-y-3">
                @foreach($purchaseOrder->items as $item)
                <div class="glass-row rounded-xl p-4 flex items-center gap-4 border border-black/5">
                    <div class="w-12 h-12 rounded-lg bg-black/5 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px] text-on-surface-variant">category</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-on-surface font-semibold text-base">
                            {{ $item->item_id ? $item->item->name : $item->item_name_temp . ' (Baru)' }}
                        </h4>
                        @if($item->item_id)
                        <div class="text-xs text-on-surface-variant mt-1 flex gap-2">
                            <span class="bg-secondary/10 text-secondary px-1.5 py-0.5 rounded">{{ $item->item->code }}</span>
                            <span>Sisa Stok: {{ $item->item->current_stock }} {{ $item->item->unit }}</span>
                        </div>
                        @endif
                        @if($item->notes)
                        <p class="text-sm text-on-surface-variant mt-2 italic"><span class="text-xs font-semibold not-italic">Spek/Catatan:</span> {{ $item->notes }}</p>
                        @endif
                    </div>
                    <div class="w-32 shrink-0 text-right">
                        <p class="text-[10px] text-on-surface-variant uppercase font-code-label mb-1">Estimasi (Satuan)</p>
                        <p class="text-on-surface text-sm font-bold">Rp {{ number_format($item->estimated_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-24 shrink-0 text-right">
                        <p class="text-[10px] text-on-surface-variant uppercase font-code-label mb-1">Jumlah</p>
                        <p class="text-on-surface text-lg font-bold">{{ $item->quantity_requested }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <a href="{{ route('purchase_orders.index') }}" class="inline-flex items-center gap-2 text-on-surface-variant hover:text-on-surface transition-colors font-medium">
        <span class="material-symbols-outlined">arrow_back</span> Kembali ke Daftar
    </a>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-surface rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden transform transition-all border border-black/10">
        <div class="p-6 border-b border-black/10 flex justify-between items-center bg-error/5">
            <h3 class="text-error font-bold text-lg flex items-center gap-2"><span class="material-symbols-outlined">cancel</span> Tolak Purchase Order</h3>
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')" class="text-outline hover:text-error transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('purchase_orders.reject', $purchaseOrder) }}" method="POST">
            @csrf
            <div class="p-6">
                <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Alasan Penolakan <span class="text-error">*</span></label>
                <textarea name="rejection_reason" rows="4" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-error transition-colors" required placeholder="Jelaskan alasan PO ini ditolak..."></textarea>
            </div>
            <div class="p-6 border-t border-black/10 bg-black/5 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 rounded-lg text-on-surface-variant hover:bg-black/5 transition-colors font-medium">Batal</button>
                <button type="submit" class="bg-error text-white font-bold py-2 px-6 rounded-lg hover:bg-red-600 transition-colors shadow-[0_0_10px_rgba(255,0,51,0.3)]">Konfirmasi Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection
