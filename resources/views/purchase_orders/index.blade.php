@extends('layouts.app')

@section('title', 'Purchase Orders (PO)')

@section('header_actions')
@can('create-po')
<a href="{{ route('purchase_orders.create') }}" class="bg-primary text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all ml-2">
    <span class="material-symbols-outlined">add</span> Buat PO Baru
</a>
@endcan
@endsection

@section('content')
<div class="glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg mb-6 p-4">
    <div class="flex gap-3 overflow-x-auto pb-2">
        <a href="{{ route('purchase_orders.index') }}" class="px-4 py-2 shrink-0 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-primary text-white shadow-[0_0_10px_rgba(57,255,20,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Semua</a>
        <a href="{{ route('purchase_orders.index', ['status' => 'draft']) }}" class="px-4 py-2 shrink-0 rounded-lg text-sm font-medium transition-colors {{ request('status') == 'draft' ? 'bg-secondary text-secondary-on-container shadow-[0_0_10px_rgba(0,255,255,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Draft</a>
        <a href="{{ route('purchase_orders.index', ['status' => 'diajukan']) }}" class="px-4 py-2 shrink-0 rounded-lg text-sm font-medium transition-colors {{ request('status') == 'diajukan' ? 'bg-warning text-white shadow-[0_0_10px_rgba(245,158,11,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Perlu Approval</a>
        <a href="{{ route('purchase_orders.index', ['status' => 'disetujui']) }}" class="px-4 py-2 shrink-0 rounded-lg text-sm font-medium transition-colors {{ request('status') == 'disetujui' ? 'bg-primary text-white shadow-[0_0_10px_rgba(57,255,20,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Disetujui</a>
        <a href="{{ route('purchase_orders.index', ['status' => 'ditolak']) }}" class="px-4 py-2 shrink-0 rounded-lg text-sm font-medium transition-colors {{ request('status') == 'ditolak' ? 'bg-error text-white shadow-[0_0_10px_rgba(255,0,51,0.3)]' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Ditolak</a>
        <a href="{{ route('purchase_orders.index', ['status' => 'selesai']) }}" class="px-4 py-2 shrink-0 rounded-lg text-sm font-medium transition-colors {{ request('status') == 'selesai' ? 'bg-on-surface-variant text-white' : 'bg-black/5 text-on-surface hover:bg-black/10' }}">Selesai</a>
    </div>
</div>

<div class="glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg" id="polling-container" hx-get="{{ request()->fullUrl() }}" hx-trigger="every 5s" hx-select="#polling-container" hx-swap="outerHTML">
    <div class="p-4 border-b border-black/5 flex flex-wrap items-center justify-between gap-4 bg-white/50">
        <span class="text-sm font-medium text-on-surface">Daftar Purchase Order</span>
    </div>
    <div class="flex-1 overflow-auto p-4 flex flex-col gap-2">
        @forelse ($purchaseOrders as $po)
        <div class="glass-row rounded-xl p-4 flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-lg 
                {{ $po->status == 'disetujui' ? 'bg-primary/10 border-primary/20 text-primary' : 
                  ($po->status == 'ditolak' ? 'bg-error/10 border-error/20 text-error' : 
                  ($po->status == 'diajukan' ? 'bg-warning/10 border-warning/20 text-warning' : 
                  ($po->status == 'selesai' ? 'bg-on-surface-variant/10 text-on-surface-variant' : 'bg-secondary/10 border-secondary/20 text-secondary'))) }} 
                border flex items-center justify-center shrink-0 overflow-hidden">
                <span class="material-symbols-outlined text-[24px]">
                    {{ $po->status == 'disetujui' ? 'check_circle' : 
                      ($po->status == 'ditolak' ? 'cancel' : 
                      ($po->status == 'diajukan' ? 'pending_actions' : 
                      ($po->status == 'selesai' ? 'task_alt' : 'drafts'))) }}
                </span>
            </div>
            
            <div class="flex-1 min-w-0">
                <h4 class="text-on-surface font-semibold truncate text-base group-hover:text-primary transition-colors">{{ $po->po_number }}</h4>
                <div class="flex items-center flex-wrap gap-3 text-xs text-on-surface-variant mt-1">
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $po->created_at->format('d M Y') }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">person</span> Pemohon: {{ $po->requestedBy->name }}</span>
                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">format_list_bulleted</span> {{ $po->items->count() }} Item</span>
                </div>
            </div>
            
            <div class="w-32 shrink-0 text-right pr-4">
                <p class="text-[10px] text-on-surface-variant uppercase font-code-label mb-1">Status</p>
                <p class="text-sm font-bold capitalize 
                    {{ $po->status == 'disetujui' ? 'text-primary' : 
                      ($po->status == 'ditolak' ? 'text-error' : 
                      ($po->status == 'diajukan' ? 'text-warning' : 
                      ($po->status == 'selesai' ? 'text-on-surface-variant' : 'text-secondary'))) }}">
                    {{ $po->status }}
                </p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0 border-l border-black/10 pl-4">
                <a href="{{ route('purchase_orders.show', $po) }}" class="p-2 text-outline hover:text-primary rounded-lg hover:bg-primary/10 transition-colors" title="Detail">
                    <span class="material-symbols-outlined">visibility</span>
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-on-surface-variant">
            <span class="material-symbols-outlined text-6xl mb-4 opacity-50">shopping_cart</span>
            <p>Belum ada data Purchase Order.</p>
        </div>
        @endforelse
    </div>
    
    @if($purchaseOrders->hasPages())
    <div class="p-4 border-t border-black/5 bg-white/30">
        {{ $purchaseOrders->links() }}
    </div>
    @endif
</div>
@endsection
