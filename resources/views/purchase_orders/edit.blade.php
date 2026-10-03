@extends('layouts.app')

@section('title', 'Edit Purchase Order (PO)')

@section('content')
<div class="max-w-4xl mx-auto relative">
    <div class="absolute -top-10 -left-10 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <form action="{{ route('purchase_orders.update', $purchaseOrder) }}" method="POST" class="glass-panel rounded-2xl border border-black/5 overflow-hidden shadow-2xl relative z-10" id="poForm">
        @csrf
        @method('PUT')
        
        <div class="bg-primary/5 p-6 border-b border-black/5">
            <h2 class="text-xl font-bold text-on-surface">Edit Permintaan Barang: {{ $purchaseOrder->po_number }}</h2>
            <p class="text-on-surface-variant text-sm mt-1">Ubah rincian barang sebelum diajukan. PO saat ini berstatus Draft.</p>
        </div>

        <div class="p-6 md:p-8 space-y-8">
            <div>
                <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                @error('notes') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="border-t border-black/5 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-code-label text-code-label text-on-surface-variant uppercase tracking-wider">Daftar Barang</h3>
                    <button type="button" id="addItemBtn" class="bg-secondary/10 text-secondary hover:bg-secondary hover:text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">add</span> Tambah Baris
                    </button>
                </div>
                
                @error('items') <span class="text-error text-xs mb-4 block">{{ $message }}</span> @enderror
                
                <div id="itemsContainer" class="space-y-4">
                    @foreach($purchaseOrder->items as $index => $poItem)
                    <div class="item-row bg-black/5 p-4 rounded-xl border border-black/5 relative group">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-4">
                                <label class="block text-xs text-on-surface-variant mb-1">Pilih Barang (Master)</label>
                                <select name="items[{{ $index }}][item_id]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm item-select">
                                    <option value="">-- Barang Baru (Manual) --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ $poItem->item_id == $item->id ? 'selected' : '' }}>{{ $item->code }} - {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-xs text-on-surface-variant mb-1">Nama Barang Baru</label>
                                <input type="text" name="items[{{ $index }}][item_name_temp]" class="w-full {{ $poItem->item_id ? 'bg-black/5 cursor-not-allowed' : 'bg-white/80' }} border border-black/10 rounded-lg p-2 text-sm item-name-temp" value="{{ $poItem->item_name_temp }}" placeholder="Hanya jika pilih Barang Baru" {{ $poItem->item_id ? 'readonly' : '' }}>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs text-on-surface-variant mb-1">Jumlah</label>
                                <input type="number" name="items[{{ $index }}][quantity_requested]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm" value="{{ $poItem->quantity_requested }}" min="1" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs text-on-surface-variant mb-1">Estimasi Harga (Satuan)</label>
                                <input type="number" name="items[{{ $index }}][estimated_price]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm" value="{{ $poItem->estimated_price ?? 0 }}" min="0">
                            </div>
                            <div class="md:col-span-9 mt-2 md:mt-0">
                                <label class="block text-xs text-on-surface-variant mb-1">Catatan/Spesifikasi</label>
                                <input type="text" name="items[{{ $index }}][notes]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm" value="{{ $poItem->notes }}" placeholder="Opsional">
                            </div>
                        </div>
                        <button type="button" class="remove-btn absolute top-2 right-2 text-outline hover:text-error opacity-0 group-hover:opacity-100 transition-opacity" title="Hapus Baris">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-black/5 p-6 border-t border-black/5 flex justify-end gap-3">
            <a href="{{ route('purchase_orders.show', $purchaseOrder) }}" class="px-6 py-2 rounded-lg text-on-surface-variant hover:bg-black/5 transition-colors font-medium">Batal</a>
            <button type="submit" class="bg-primary text-white font-bold py-2 px-6 rounded-lg hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">save</span> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('itemsContainer');
    const addBtn = document.getElementById('addItemBtn');
    let rowCount = {{ $purchaseOrder->items->count() }};

    // Data items for new rows
    const itemsOptions = `@foreach($items as $item)<option value="{{ $item->id }}">{{ $item->code }} - {{ $item->name }}</option>@endforeach`;

    addBtn.addEventListener('click', function() {
        const index = rowCount++;
        const row = document.createElement('div');
        row.className = 'item-row bg-black/5 p-4 rounded-xl border border-black/5 relative group mt-4';
        row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-4">
                    <label class="block text-xs text-on-surface-variant mb-1">Pilih Barang (Master)</label>
                    <select name="items[${index}][item_id]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm item-select">
                        <option value="">-- Barang Baru (Manual) --</option>
                        ${itemsOptions}
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs text-on-surface-variant mb-1">Nama Barang Baru</label>
                    <input type="text" name="items[${index}][item_name_temp]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm item-name-temp" placeholder="Hanya jika pilih Barang Baru">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-on-surface-variant mb-1">Jumlah</label>
                    <input type="number" name="items[${index}][quantity_requested]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm" value="1" min="1" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs text-on-surface-variant mb-1">Estimasi Harga (Satuan)</label>
                    <input type="number" name="items[${index}][estimated_price]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm" value="0" min="0">
                </div>
                <div class="md:col-span-9 mt-2 md:mt-0">
                    <label class="block text-xs text-on-surface-variant mb-1">Catatan/Spesifikasi</label>
                    <input type="text" name="items[${index}][notes]" class="w-full bg-white/80 border border-black/10 rounded-lg p-2 text-sm" placeholder="Opsional">
                </div>
            </div>
            <button type="button" class="remove-btn absolute top-2 right-2 text-outline hover:text-error opacity-0 group-hover:opacity-100 transition-opacity" title="Hapus Baris">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        `;
        container.appendChild(row);
        
        attachRowEvents(row);
    });

    function attachRowEvents(row) {
        const select = row.querySelector('.item-select');
        const inputTemp = row.querySelector('.item-name-temp');
        const removeBtn = row.querySelector('.remove-btn');

        select.addEventListener('change', function() {
            if (this.value !== "") {
                inputTemp.value = "";
                inputTemp.setAttribute('readonly', true);
                inputTemp.classList.add('bg-black/5', 'cursor-not-allowed');
            } else {
                inputTemp.removeAttribute('readonly');
                inputTemp.classList.remove('bg-black/5', 'cursor-not-allowed');
            }
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                } else {
                    alert('Minimal harus ada 1 barang!');
                }
            });
        }
    }

    // Attach to existing rows
    document.querySelectorAll('.item-row').forEach(row => attachRowEvents(row));
});
</script>
@endsection
