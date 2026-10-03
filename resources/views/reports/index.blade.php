@extends('layouts.app')

@section('title', 'Laporan & Analytics')

@section('content')
<div class="max-w-7xl mx-auto relative">
    <div class="absolute -top-10 -right-10 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 -left-10 w-64 h-64 bg-secondary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="glass-panel rounded-2xl border border-black/5 overflow-hidden shadow-2xl relative z-10 print:shadow-none print:border-none print:bg-white">
        
        <!-- Header & Tabs (Hidden on Print) -->
        <div class="border-b border-black/5 bg-primary/5 print:hidden">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 gap-4">
                <div>
                    <h2 class="text-xl font-bold text-on-surface">Pusat Laporan</h2>
                    <p class="text-on-surface-variant text-sm mt-1">Lihat dan unduh mutasi stok, transaksi, serta riwayat permintaan barang.</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">print</span> Print PDF
                    </button>
                    
                    @if($tab == 'stock')
                        <a href="{{ route('reports.export.stock', array_merge(request()->except('format'), ['format' => 'pdf'])) }}" class="bg-error/10 text-error hover:bg-error hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Export PDF
                        </a>
                        <a href="{{ route('reports.export.stock', array_merge(request()->except('format'), ['format' => 'excel'])) }}" class="bg-primary/10 text-primary hover:bg-primary hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">table</span> Export Excel
                        </a>
                    @elseif($tab == 'transactions')
                        <a href="{{ route('reports.export.transactions', array_merge(request()->except('format'), ['format' => 'pdf'])) }}" class="bg-error/10 text-error hover:bg-error hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Export PDF
                        </a>
                        <a href="{{ route('reports.export.transactions', array_merge(request()->except('format'), ['format' => 'excel'])) }}" class="bg-primary/10 text-primary hover:bg-primary hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">table</span> Export Excel
                        </a>
                    @elseif($tab == 'po')
                        <a href="{{ route('reports.export.po', array_merge(request()->except('format'), ['format' => 'pdf'])) }}" class="bg-error/10 text-error hover:bg-error hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span> Export PDF
                        </a>
                        <a href="{{ route('reports.export.po', array_merge(request()->except('format'), ['format' => 'excel'])) }}" class="bg-primary/10 text-primary hover:bg-primary hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">table</span> Export Excel
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex gap-6 px-6 border-b border-black/5 overflow-x-auto">
                <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'stock'])) }}" class="py-3 font-code-label text-code-label uppercase tracking-wider relative {{ $tab == 'stock' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                    Laporan Stok
                    @if($tab == 'stock') <div class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-lg"></div> @endif
                </a>
                <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'transactions'])) }}" class="py-3 font-code-label text-code-label uppercase tracking-wider relative {{ $tab == 'transactions' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                    Riwayat Transaksi
                    @if($tab == 'transactions') <div class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-lg"></div> @endif
                </a>
                <a href="{{ route('reports.index', array_merge(request()->all(), ['tab' => 'po'])) }}" class="py-3 font-code-label text-code-label uppercase tracking-wider relative {{ $tab == 'po' ? 'text-primary font-bold' : 'text-on-surface-variant hover:text-primary' }}">
                    Riwayat Purchase Order
                    @if($tab == 'po') <div class="absolute bottom-0 left-0 w-full h-1 bg-primary rounded-t-lg"></div> @endif
                </a>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <!-- Filter Section (Hidden on Print) -->
            <form action="{{ route('reports.index') }}" method="GET" class="mb-8 p-4 bg-black/5 rounded-xl border border-black/5 print:hidden">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                @if($tab == 'stock')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Kategori</label>
                        <select name="category_id" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Lokasi</label>
                        <select name="location_id" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-6 py-2 rounded-lg font-medium transition-colors w-full h-[38px]">Terapkan Filter</button>
                    </div>
                </div>
                @endif

                @if($tab == 'transactions')
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Tipe</label>
                        <select name="type" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                            <option value="">Semua Tipe</option>
                            <option value="masuk" {{ request('type') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                            <option value="keluar" {{ request('type') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Barang</label>
                        <select name="item_id" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                            <option value="">Semua Barang</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Tgl Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Tgl Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-4 py-2 rounded-lg font-medium transition-colors w-full h-[38px]">Filter</button>
                    </div>
                </div>
                @endif

                @if($tab == 'po')
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Status PO</label>
                        <select name="status" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Tgl Mulai</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-on-surface-variant mb-1 uppercase font-code-label">Tgl Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-4 py-2 rounded-lg font-medium transition-colors w-full h-[38px]">Filter</button>
                    </div>
                </div>
                @endif
            </form>

            <!-- Report Content (Visible on Print) -->
            <div class="hidden print:block mb-8">
                <h1 class="text-3xl font-black text-center mb-2">INVENTORY - GLOBAL CONTROL CENTER</h1>
                <h2 class="text-xl font-bold text-center uppercase tracking-widest text-gray-600 mb-6 border-b-2 border-black pb-4">
                    Laporan {{ $tab == 'stock' ? 'Stok Barang' : ($tab == 'transactions' ? 'Riwayat Transaksi' : 'Purchase Orders') }}
                </h2>
                <div class="flex justify-between text-sm mb-4">
                    <p><strong>Tanggal Cetak:</strong> {{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</p>
                    <p><strong>Dicetak oleh:</strong> {{ auth()->user()->name }}</p>
                </div>
            </div>

            <!-- Data Tables -->
            <div class="overflow-x-auto rounded-xl border border-black/5 bg-white" id="polling-container" hx-get="{{ route('reports.index', array_merge(request()->except('format'), ['tab' => $tab])) }}" hx-trigger="every 5s" hx-select="#polling-container" hx-swap="outerHTML">
                @if($tab == 'stock')
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-black/5 font-code-label text-code-label text-on-surface-variant uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 border-b border-black/5">Kode Aset</th>
                            <th class="px-6 py-4 border-b border-black/5">Nama Barang</th>
                            <th class="px-6 py-4 border-b border-black/5">Kategori</th>
                            <th class="px-6 py-4 border-b border-black/5">Lokasi</th>
                            <th class="px-6 py-4 border-b border-black/5 text-right">Stok Saat Ini</th>
                            <th class="px-6 py-4 border-b border-black/5">Kondisi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse($items as $item)
                        <tr class="hover:bg-black/5 transition-colors">
                            <td class="px-6 py-4 text-secondary font-bold">{{ $item->code }}</td>
                            <td class="px-6 py-4 text-on-surface font-medium">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $item->category ? $item->category->name : '-' }}</td>
                            <td class="px-6 py-4 text-on-surface-variant">{{ $item->location ? $item->location->name : '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-lg font-bold {{ $item->current_stock > 0 ? 'text-primary' : 'text-error' }}">{{ $item->current_stock }}</span>
                                <span class="text-xs text-on-surface-variant">{{ $item->unit }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($item->condition == 'baik') <span class="text-primary font-medium">Baik</span>
                                @elseif($item->condition == 'rusak_ringan') <span class="text-warning font-medium">Rusak Ringan</span>
                                @else <span class="text-error font-medium">Rusak Berat</span> @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-on-surface-variant italic">Tidak ada data stok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @endif

                @if($tab == 'transactions')
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-black/5 font-code-label text-code-label text-on-surface-variant uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-4 border-b border-black/5">Tanggal</th>
                            <th class="px-4 py-4 border-b border-black/5">Barang</th>
                            <th class="px-4 py-4 border-b border-black/5">Tipe & Status</th>
                            <th class="px-4 py-4 border-b border-black/5 text-right">Jumlah</th>
                            <th class="px-4 py-4 border-b border-black/5">Peminjam/Supplier</th>
                            <th class="px-4 py-4 border-b border-black/5">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse($transactions as $t)
                        <tr class="hover:bg-black/5 transition-colors">
                            <td class="px-4 py-4 text-on-surface-variant">{{ $t->transaction_date->format('d M Y') }}</td>
                            <td class="px-4 py-4 text-on-surface font-medium">{{ $t->item ? $t->item->name : 'N/A' }}</td>
                            <td class="px-4 py-4">
                                <span class="font-bold uppercase tracking-wider text-[10px] px-2 py-0.5 rounded {{ $t->type == 'masuk' ? 'bg-primary/20 text-primary' : 'bg-error/20 text-error' }}">{{ $t->type }}</span>
                                @if($t->status) <span class="ml-2 text-xs italic text-on-surface-variant">{{ $t->status }}</span> @endif
                            </td>
                            <td class="px-4 py-4 text-right font-bold {{ $t->type == 'masuk' ? 'text-primary' : 'text-error' }}">{{ $t->type == 'masuk' ? '+' : '-' }}{{ $t->quantity }}</td>
                            <td class="px-4 py-4 text-on-surface-variant">{{ $t->type == 'keluar' ? $t->borrower_name : ($t->supplier ? $t->supplier->name : '-') }}</td>
                            <td class="px-4 py-4 text-xs text-on-surface-variant truncate max-w-xs">{{ $t->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-on-surface-variant italic">Tidak ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @endif

                @if($tab == 'po')
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-black/5 font-code-label text-code-label text-on-surface-variant uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-4 border-b border-black/5">Nomor PO</th>
                            <th class="px-4 py-4 border-b border-black/5">Tanggal</th>
                            <th class="px-4 py-4 border-b border-black/5">Status</th>
                            <th class="px-4 py-4 border-b border-black/5">Pemohon</th>
                            <th class="px-4 py-4 border-b border-black/5">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @forelse($purchaseOrders as $po)
                        <tr class="hover:bg-black/5 transition-colors">
                            <td class="px-4 py-4 font-bold text-secondary">{{ $po->po_number }}</td>
                            <td class="px-4 py-4 text-on-surface-variant">{{ $po->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-4">
                                <span class="font-bold uppercase tracking-wider text-[10px] px-2 py-0.5 rounded
                                    {{ $po->status == 'disetujui' ? 'bg-primary/20 text-primary' : 
                                      ($po->status == 'ditolak' ? 'bg-error/20 text-error' : 
                                      ($po->status == 'diajukan' ? 'bg-warning/20 text-warning' : 
                                      ($po->status == 'selesai' ? 'bg-on-surface-variant/20 text-on-surface-variant' : 'bg-secondary/20 text-secondary'))) }}">
                                    {{ $po->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-on-surface">{{ $po->requestedBy ? $po->requestedBy->name : 'N/A' }}</td>
                            <td class="px-4 py-4 text-xs text-on-surface-variant">
                                {{ $po->items->count() }} macam barang (Total: {{ $po->items->sum('quantity_requested') }})
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-on-surface-variant italic">Tidak ada Purchase Order.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @endif
            </div>

            <!-- Footer for Print -->
            <div class="hidden print:block mt-12">
                <div class="flex justify-end pr-12">
                    <div class="text-center">
                        <p class="mb-16 text-sm">Mengetahui,</p>
                        <p class="font-bold border-b border-black px-4">{{ auth()->user()->name }}</p>
                        <p class="text-xs uppercase mt-1">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: landscape;
        margin: 0mm; /* Menghilangkan header (Tanggal) & footer (URL) bawaan browser */
    }
    body {
        height: auto !important;
        overflow: visible !important;
        padding: 15mm 20mm; /* Mengembalikan margin untuk konten (atas/bawah 1.5cm, kiri/kanan 2cm) */
        background: white !important;
        color: black !important;
    }
    aside, header, nav, .flash-messages {
        display: none !important;
    }
    main {
        margin-left: 0 !important;
        padding-top: 0 !important;
        overflow: visible !important;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
    .print\:shadow-none {
        box-shadow: none !important;
    }
    .print\:border-none {
        border: none !important;
    }
    .print\:bg-white {
        background: white !important;
    }
}
</style>
<script>
function downloadReport(url, defaultFilename, btn) {
    // Simpan teks asli tombol
    const originalText = btn.innerHTML;
    
    // Ubah state tombol menjadi loading
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengunduh...';
    btn.disabled = true;

    // Gunakan navigasi native browser untuk mengunduh file
    // Ini jauh lebih aman dan stabil karena langsung di-handle oleh Download Manager browser
    // tanpa memuat file ke dalam RAM (menghindari hang/stuck)
    window.location.href = url;

    // Kembalikan state tombol setelah 3 detik (asumsi download sudah mulai)
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 3000);
}
</script>
@endsection
