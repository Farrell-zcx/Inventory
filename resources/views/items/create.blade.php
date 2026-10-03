@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('header_actions')
<a href="{{ route('items.index') }}" class="glass-panel px-4 py-2 rounded-lg text-on-surface border border-black/10 hover:bg-black/5 text-sm transition-colors shadow-sm flex items-center gap-2">
    <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
</a>
@endsection

@section('content')
<div class="w-full max-w-3xl mx-auto glass-panel rounded-xl border border-black/5 overflow-hidden shadow-lg p-6 relative">
    <div class="absolute -top-20 -right-20 w-64 h-64 bg-primary-container/20 rounded-full blur-3xl pointer-events-none"></div>

    <form action="{{ route('items.store') }}" method="POST" class="flex flex-col gap-5 relative z-10">
        @csrf
        
        <div class="mb-5">
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Nama Barang</label>
            <input type="text" name="name" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors" value="{{ old('name') }}" required>
            @error('name') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider">Kategori</label>
                    <button type="button" class="text-[10px] bg-secondary/10 text-secondary hover:bg-secondary/20 px-2 py-1 rounded transition-colors font-code-label uppercase" onclick="addCategory()">+ Baru</button>
                </div>
                <select name="category_id" id="category_id" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors appearance-none" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider">Lokasi</label>
                    <button type="button" class="text-[10px] bg-secondary/10 text-secondary hover:bg-secondary/20 px-2 py-1 rounded transition-colors font-code-label uppercase" onclick="addLocation()">+ Baru</button>
                </div>
                <select name="location_id" id="location_id" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors appearance-none" required>
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                @error('location_id') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Kondisi</label>
            <select name="condition" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors appearance-none" required>
                <option value="baik" {{ old('condition') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak_ringan" {{ old('condition') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak_berat" {{ old('condition') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
            @error('condition') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="mb-5">
                <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Satuan</label>
                <input list="unit-options" name="unit" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors" value="{{ old('unit', 'pcs') }}" placeholder="Contoh: pcs, box, kardus..." required>
                <datalist id="unit-options">
                    <option value="pcs">pcs</option>
                    <option value="box">box</option>
                    <option value="kardus">kardus</option>
                    <option value="rim">rim</option>
                    <option value="pack">pack</option>
                    <option value="roll">roll</option>
                    <option value="meter">meter</option>
                    <option value="kg">kg</option>
                    <option value="liter">liter</option>
                </datalist>
                @error('unit') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="mt-4 flex justify-end">
            <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-lg hover:bg-primary-fixed shadow-[0_0_15px_rgba(57,255,20,0.3)] transition-all">
                Simpan Barang
            </button>
        </div>
    </form>
</div>

<!-- Modal Kategori -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black/50 z-50 justify-center items-center backdrop-blur-sm">
    <div class="glass-panel w-full max-w-sm rounded-xl p-6 m-4 shadow-2xl relative">
        <h3 class="text-lg font-semibold text-on-surface mb-4">Tambah Kategori Baru</h3>
        <div>
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Nama Kategori</label>
            <input type="text" id="new_category_name" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors" placeholder="Contoh: Elektronik">
        </div>
        <div class="flex gap-3 justify-end mt-6">
            <button type="button" class="px-4 py-2 rounded-lg text-on-surface border border-black/10 hover:bg-black/5 text-sm transition-colors" onclick="closeCategoryModal()">Batal</button>
            <button type="button" id="btnSaveCategory" class="px-4 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary-fixed transition-colors text-sm" onclick="saveCategory()">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Lokasi -->
<div id="locationModal" class="hidden fixed inset-0 bg-black/50 z-50 justify-center items-center backdrop-blur-sm">
    <div class="glass-panel w-full max-w-sm rounded-xl p-6 m-4 shadow-2xl relative">
        <h3 class="text-lg font-semibold text-on-surface mb-4">Tambah Lokasi Baru</h3>
        <div>
            <label class="block font-code-label text-code-label text-on-surface-variant uppercase tracking-wider mb-2">Nama Lokasi</label>
            <input type="text" id="new_location_name" class="w-full bg-white/60 border border-black/10 rounded-lg p-3 text-sm text-on-surface focus:outline-none focus:border-secondary transition-colors" placeholder="Contoh: Gudang A">
        </div>
        <div class="flex gap-3 justify-end mt-6">
            <button type="button" class="px-4 py-2 rounded-lg text-on-surface border border-black/10 hover:bg-black/5 text-sm transition-colors" onclick="closeLocationModal()">Batal</button>
            <button type="button" id="btnSaveLocation" class="px-4 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary-fixed transition-colors text-sm" onclick="saveLocation()">Simpan</button>
        </div>
    </div>
</div>

<script>
    function addCategory() {
        const modal = document.getElementById('categoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('new_category_name').focus();
    }

    function closeCategoryModal() {
        const modal = document.getElementById('categoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('new_category_name').value = '';
    }

    function saveCategory() {
        const nameInput = document.getElementById('new_category_name');
        const saveBtn = document.getElementById('btnSaveCategory');
        const name = nameInput.value.trim();
        
        if (!name) {
            alert("Nama kategori tidak boleh kosong!");
            return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';

        fetch("{{ route('categories.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: name })
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan';
            if (data.success) {
                const select = document.getElementById('category_id');
                const option = document.createElement('option');
                option.value = data.category.id;
                option.text = data.category.name;
                option.selected = true;
                select.add(option);
                closeCategoryModal();
            } else {
                alert("Gagal menambahkan kategori!");
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan';
            alert("Terjadi kesalahan sistem: " + err.message);
        });
    }

    function addLocation() {
        const modal = document.getElementById('locationModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('new_location_name').focus();
    }

    function closeLocationModal() {
        const modal = document.getElementById('locationModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('new_location_name').value = '';
    }

    function saveLocation() {
        const nameInput = document.getElementById('new_location_name');
        const saveBtn = document.getElementById('btnSaveLocation');
        const name = nameInput.value.trim();
        
        if (!name) {
            alert("Nama lokasi tidak boleh kosong!");
            return;
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Menyimpan...';

        fetch("{{ route('locations.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: name })
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan';
            if (data.success) {
                const select = document.getElementById('location_id');
                const option = document.createElement('option');
                option.value = data.location.id;
                option.text = data.location.name;
                option.selected = true;
                select.add(option);
                closeLocationModal();
            } else {
                alert("Gagal menambahkan lokasi!");
            }
        })
        .catch(err => {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Simpan';
            alert("Terjadi kesalahan sistem: " + err.message);
        });
    }
</script>
@endsection
