@extends('layouts.app')

@section('title', 'Transaksi Stok')

@section('header_actions')
<a href="{{ route('stock_transactions.index') }}" class="glass-panel px-4 py-2 rounded-lg text-on-surface border border-black/10 hover:bg-black/5 text-sm transition-colors shadow-sm flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
</a>
@endsection

@section('content')
<div class="w-full max-w-4xl mx-auto glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg p-6 relative" x-data="{ 
    action_type: '{{ old('action_type', 'masuk_beli') }}', 
    unitPrice: {{ old('unit_price', 0) }}, 
    quantity: {{ old('quantity', 1) }}, 
    selectedStock: null,
    itemStocks: {
        @foreach($items as $item)
            '{{ $item->id }}': {{ $item->current_stock }},
        @endforeach
    },
    updateStock(e) { this.selectedStock = this.itemStocks[e.target.value] ?? null; },
    get totalPrice() { return this.unitPrice * this.quantity } 
}">
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    @if ($errors->any())
        <div class="glass-card bg-error/10 border-error/20 p-4 rounded-lg mb-6 relative z-10 text-error text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stock_transactions.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6 relative z-10">
        @csrf
        
        <div class="mb-6">
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-3">Tipe Transaksi</label>
            <div class="flex flex-col md:flex-row gap-2 border-b border-black/10 pb-4">
                <input type="hidden" name="action_type" :value="action_type">

                <button type="button" @click="action_type = 'masuk_beli'"
                     :class="action_type === 'masuk_beli' ? 'bg-secondary/10 text-secondary font-bold border-l-4 md:border-l-0 md:border-b-4 border-secondary' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface border-l-4 md:border-l-0 md:border-b-4 border-transparent'"
                     class="py-3 px-4 flex items-center gap-3 transition-all rounded-r-lg md:rounded-r-none md:rounded-t-lg group flex-1">
                    <span class="material-symbols-outlined" :class="action_type === 'masuk_beli' ? 'text-secondary' : 'text-outline group-hover:text-secondary'" :style="action_type === 'masuk_beli' ? 'font-variation-settings: \'FILL\' 1;' : ''">add_shopping_cart</span>
                    <span class="font-code-label text-sm text-left">Pembelian / Restok</span>
                </button>
                
                <button type="button" @click="action_type = 'masuk_kembali'"
                     :class="action_type === 'masuk_kembali' ? 'bg-primary/10 text-primary font-bold border-l-4 md:border-l-0 md:border-b-4 border-primary' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface border-l-4 md:border-l-0 md:border-b-4 border-transparent'"
                     class="py-3 px-4 flex items-center gap-3 transition-all rounded-r-lg md:rounded-r-none md:rounded-t-lg group flex-1">
                    <span class="material-symbols-outlined" :class="action_type === 'masuk_kembali' ? 'text-primary' : 'text-outline group-hover:text-primary'" :style="action_type === 'masuk_kembali' ? 'font-variation-settings: \'FILL\' 1;' : ''">assignment_return</span>
                    <span class="font-code-label text-sm text-left">Pengembalian Pinjaman</span>
                </button>
                
                <button type="button" @click="action_type = 'keluar'"
                     :class="action_type === 'keluar' ? 'bg-error/10 text-error font-bold border-l-4 md:border-l-0 md:border-b-4 border-error' : 'text-on-surface-variant hover:bg-black/5 hover:text-on-surface border-l-4 md:border-l-0 md:border-b-4 border-transparent'"
                     class="py-3 px-4 flex items-center gap-3 transition-all rounded-r-lg md:rounded-r-none md:rounded-t-lg group flex-1">
                    <span class="material-symbols-outlined" :class="action_type === 'keluar' ? 'text-error' : 'text-outline group-hover:text-error'" :style="action_type === 'keluar' ? 'font-variation-settings: \'FILL\' 1;' : ''">outbox</span>
                    <span class="font-code-label text-sm text-left">Penggunaan / Peminjaman</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Tanggal Transaksi *</label>
                <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Barang / Aset *</label>
                <div class="relative">
                    <select id="item_id" name="item_id" @change="updateStock" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors appearance-none" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->code }} - {{ $item->name }} (Sisa Stok: {{ $item->current_stock }} {{ $item->unit }})
                            </option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">arrow_drop_down</span>
                </div>
            </div>

            <div>
                <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Jumlah *</label>
                <input type="number" id="quantity" name="quantity" min="1" :max="action_type === 'keluar' && selectedStock !== null ? selectedStock : ''" x-model.number="quantity" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors" required>
            </div>
        </div>

        <!-- SEKSI BARANG MASUK (BELI) -->
        <div x-show="action_type === 'masuk_beli'" x-cloak class="glass-card bg-secondary/5 border-secondary/20 p-5 rounded-xl transition-all">
            <h3 class="text-secondary font-semibold mb-4 flex items-center gap-2"><span class="material-symbols-outlined">shopping_cart</span> Detail Pembelian</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-3">
                    <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Supplier Asal</label>
                    <div class="relative">
                        <select id="supplier_id" name="supplier_id" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors appearance-none" x-bind:required="action_type === 'masuk_beli'">
                            <option value="">-- Tanpa Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">arrow_drop_down</span>
                    </div>
                </div>
                <div>
                    <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Harga Satuan (Rp)</label>
                    <input type="number" id="unit_price" name="unit_price" min="0" x-model.number="unitPrice" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Total Harga (Rp)</label>
                    <input type="number" id="total_price" name="total_price" :value="totalPrice" readonly class="w-full bg-surface/50 border border-black/5 rounded-lg p-3 text-sm text-on-surface-variant focus:outline-none cursor-not-allowed">
                    <p class="text-xs text-on-surface-variant mt-1">Dihitung otomatis (Harga Satuan × Jumlah)</p>
                </div>
            </div>
        </div>

        <!-- SEKSI BARANG MASUK (KEMBALI) -->
        <div x-show="action_type === 'masuk_kembali'" x-cloak class="glass-card bg-primary/5 border-primary/20 p-5 rounded-xl transition-all">
            <h3 class="text-primary font-semibold mb-4 flex items-center gap-2"><span class="material-symbols-outlined">south_east</span> Referensi Peminjaman</h3>
            <div class="relative mb-4">
                <select id="reference_id" name="reference_id" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-primary transition-colors appearance-none" x-bind:required="action_type === 'masuk_kembali'">
                    <option value="">-- Pilih Transaksi Peminjaman --</option>
                    @foreach($borrowedTransactions as $borrow)
                        <option value="{{ $borrow->id }}" {{ old('reference_id') == $borrow->id ? 'selected' : '' }}>
                            [ID: {{ $borrow->id }}] {{ $borrow->item->name }} - Dipinjam oleh: {{ $borrow->borrower_name }} ({{ $borrow->quantity }} unit)
                        </option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">arrow_drop_down</span>
            </div>
            
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Kondisi Pengembalian *</label>
            <div class="relative">
                <select id="return_status" name="return_status" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-primary transition-colors appearance-none" x-bind:required="action_type === 'masuk_kembali'">
                    <option value="dikembalikan">Kembali Utuh / Baik</option>
                    <option value="dikembalikan_rusak">Kembali Rusak (Masuk stok, status aset menjadi rusak ringan)</option>
                    <option value="hilang">Hilang (Tidak masuk stok, transaksi lama ditandai hilang)</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-[24px] -translate-y-1/2 text-outline pointer-events-none">arrow_drop_down</span>
            </div>
            
            <p class="text-xs text-on-surface-variant mt-2">Pilih kondisi pengembalian. Jika hilang, stok barang tidak akan bertambah kembali.</p>
        </div>

        <!-- SEKSI BARANG KELUAR -->
        <div x-show="action_type === 'keluar'" x-cloak class="glass-card bg-error/5 border-error/20 p-5 rounded-xl transition-all">
            <h3 class="text-error font-semibold mb-4 flex items-center gap-2"><span class="material-symbols-outlined">north_west</span> Detail Barang Keluar</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Peminjam / Divisi / Tujuan *</label>
                    <input type="text" id="borrower_name" name="borrower_name" value="{{ old('borrower_name') }}" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-error transition-colors" x-bind:required="action_type === 'keluar'">
                </div>
                <div>
                    <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Status Pengeluaran *</label>
                    <div class="relative">
                        <select id="status" name="status" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-error transition-colors appearance-none" x-bind:required="action_type === 'keluar'">
                            <option value="dipinjam" {{ old('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam (Akan dikembalikan)</option>
                            <option value="habis" {{ old('status') == 'habis' ? 'selected' : '' }}>Habis Terpakai (Consumable)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-outline pointer-events-none">arrow_drop_down</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Dokumen / Bukti (Opsional)</label>
            <input type="file" id="document" name="document" class="w-full bg-white/60 border border-black/10 rounded-lg p-2 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
            <p class="text-xs text-on-surface-variant mt-1">Format: PDF/JPG/PNG max 2MB</p>
        </div>

        <div>
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Catatan Tambahan</label>
            <textarea id="notes" name="notes" rows="3" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors">{{ old('notes') }}</textarea>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-lg hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all">
                Simpan Transaksi
            </button>
        </div>
    </form>
</div>
@endsection
