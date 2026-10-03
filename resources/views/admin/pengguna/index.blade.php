@extends('layouts.app')

@section('title', 'Kelola Pengguna')

@section('content')
<div class="glass-card rounded-xl p-6 relative overflow-hidden group mb-6">
    <div class="relative z-10 flex flex-col justify-between items-start">
        <h2 class="font-display-lg-mobile text-display-lg-mobile text-on-surface tracking-tight mb-1">Kelola Pengguna</h2>
        <p class="font-code-label text-code-label text-on-surface-variant flex items-center gap-2">
            Tetapkan peran spesifik untuk pengguna pada aplikasi Inventory.
        </p>
    </div>
</div>

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.duration.500ms class="bg-primary/20 text-primary border border-primary/30 p-4 rounded-lg mb-6">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition.duration.500ms class="bg-error/20 text-error border border-error/30 p-4 rounded-lg mb-6">
    {{ session('error') }}
</div>
@endif

<div class="glass-card rounded-xl p-6 mb-6">
    <div class="flex items-start gap-4 mb-6">
        <div class="p-3 bg-primary-container text-on-primary-container rounded-xl shadow-sm border border-primary/10">
            <span class="material-symbols-outlined text-[24px]">person_add</span>
        </div>
        <div>
            <h3 class="font-headline-md text-headline-md text-on-surface mb-1">Pre-Provisioning Karyawan Baru</h3>
            <p class="text-sm text-on-surface-variant">Daftarkan email SSO Karyawan baru dan berikan hak akses agar mereka bisa melakukan login pertama kalinya ke Inventory.</p>
        </div>
    </div>
    
    <form action="{{ route('admin.pengguna.storeProvisioning') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end bg-surface-container/30 p-5 rounded-xl border border-outline-variant/20 shadow-inner">
            <div class="lg:col-span-6">
                <label class="block text-xs font-code-label text-on-surface-variant uppercase tracking-wider mb-2">Email SSO</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70 text-[20px]">mail</span>
                    <input type="email" name="email" required placeholder="email@perusahaan.com" class="w-full bg-surface border border-outline-variant/40 text-on-surface rounded-lg pl-10 pr-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary transition-all shadow-sm font-medium">
                </div>
            </div>
            <div class="lg:col-span-3">
                <label class="block text-xs font-code-label text-on-surface-variant uppercase tracking-wider mb-2">Role Akses</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70 text-[20px]">shield_person</span>
                    <select name="role" required class="w-full bg-surface border border-outline-variant/40 text-on-surface rounded-lg pl-10 pr-4 py-2.5 focus:border-primary focus:ring-1 focus:ring-primary transition-all shadow-sm appearance-none font-medium cursor-pointer">
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant/70 pointer-events-none text-[20px]">unfold_more</span>
                </div>
            </div>
            <div class="lg:col-span-3">
                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-primary text-on-primary h-[46px] rounded-lg hover:bg-primary/90 transition-all font-semibold shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 active:shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">add_task</span>
                    <span>Daftarkan</span>
                </button>
            </div>
        </div>
    </form>
</div>


<div class="glass-card rounded-xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-outline-variant/30 text-on-surface-variant font-code-label uppercase text-sm">
                    <th class="p-3">Nama Lengkap</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role Saat Ini</th>
                    <th class="p-3">Update Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingUsers as $pending)
                <tr class="border-b border-outline-variant/10 bg-surface-container/20 hover:bg-surface/50 transition-colors">
                    <td class="p-3 text-on-surface-variant italic text-sm">
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                            Menunggu Login
                        </span>
                        <span class="inline-block ml-2 px-2 py-0.5 bg-tertiary-container/50 border border-tertiary/30 text-tertiary text-[10px] rounded-full uppercase tracking-wider font-bold">Pending</span>
                    </td>
                    <td class="p-3 text-on-surface-variant font-medium">{{ $pending->email }}</td>
                    <td class="p-3 text-primary">{{ ucfirst($pending->role) }}</td>
                    <td class="p-3">
                        <span class="text-xs text-on-surface-variant/70 italic">Daftarkan ulang untuk ubah role</span>
                    </td>
                </tr>
                @endforeach

                @foreach($users as $user)
                <tr class="border-b border-outline-variant/10 hover:bg-surface/50 transition-colors">
                    <td class="p-3 text-on-surface">{{ $user->name }}</td>
                    <td class="p-3 text-on-surface-variant">{{ $user->email }}</td>
                    <td class="p-3 text-primary">{{ ucfirst($user->role) }}</td>
                    <td class="p-3">
                        <form action="{{ route('admin.pengguna.updateRole', $user->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <select name="role" class="bg-surface border border-outline-variant/30 text-on-surface rounded p-1 text-sm">
                                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <button type="submit" class="bg-secondary text-on-secondary px-3 py-1 rounded text-sm hover:bg-secondary/90 transition-colors">Simpan</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

