@extends('layouts.app') 

@section('title', 'User Management - Maintenance X') 
@section('page_title', 'User Management') 

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-900">User Management & Permissions</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola pengguna dan atur centang hak akses modul untuk tiap role.</p>
        </div>
        <button 
            type="button" 
            onclick="document.getElementById('addUserModal').classList.remove('hidden'); document.getElementById('addUserModal').classList.add('flex');"
            class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 shrink-0"
        >
            <span class="text-lg leading-none">+</span> Tambah User
        </button>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs text-slate-400 uppercase font-semibold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-5">User</th>
                        <th class="py-3.5 px-5">Role</th>
                        <th class="py-3.5 px-5">Hak Akses Modul</th>
                        <th class="py-3.5 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        @php
                            $roleStyle = [
                                'SUPERADMIN' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'ADMIN'      => 'bg-blue-50 text-blue-700 border-blue-200',
                                'SUPERVISOR' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'MANAGER'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'ENGINEER'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            ][strtoupper($u->role)] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                        @endphp

                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs border border-slate-200">
                                        {{ strtoupper(substr($u->username, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-slate-900 block">{{ $u->username }}</span>
                                        <span class="text-xs text-slate-400">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5">
                                <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold tracking-wider rounded border {{ $roleStyle }}">
                                    {{ strtoupper($u->role) }}
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                @if(strtoupper($u->role) === 'SUPERADMIN')
                                    <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-md border border-purple-200">Full Access (All Modul)</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($u->permissions ?? [] as $perm)
                                            <span class="px-2 py-0.5 text-[11px] font-medium rounded bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $perm }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Tidak ada akses</span>
                                        @endforelse
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        type="button" 
                                        onclick="openEditModal({{ $u->id }}, '{{ $u->role }}', {{ json_encode($u->permissions ?? []) }})"
                                        class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 text-xs font-semibold rounded-lg transition"
                                    >
                                        Edit Akses
                                    </button>

                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Hapus user ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold rounded-lg transition">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400 text-sm">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal Edit Hak Akses -->
<div id="editAccessModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden relative">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-lg">Atur Hak Akses User</h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form id="editAccessForm" method="POST" action="" class="p-6 space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Role User</label>
                <select id="editRole" name="role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-600 text-sm">
                    <option value="SUPERADMIN">SUPERADMIN</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="SUPERVISOR">SUPERVISOR</option>
                    <option value="MANAGER">MANAGER</option>
                    <option value="ENGINEER">ENGINEER</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Ceklis Hak Akses Modul</label>
                <div class="space-y-2.5 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    @foreach($availablePermissions as $key => $label)
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $key }}"
                                id="perm_{{ $key }}"
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >
                            <span class="text-sm text-slate-700 font-medium">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md transition">
                    Simpan Hak Akses
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="addUserModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl border border-slate-200 overflow-hidden relative">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-lg">Tambah User Baru</h3>
            <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>
        <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                <select name="role" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm">
                    <option value="ENGINEER">ENGINEER</option>
                    <option value="SUPERVISOR">SUPERVISOR</option>
                    <option value="MANAGER">MANAGER</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="SUPERADMIN">SUPERADMIN</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Default Akses Modul</label>
                <div class="space-y-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    @foreach($availablePermissions as $key => $label)
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" class="rounded text-blue-600">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-semibold text-sm rounded-xl shadow-md">Simpan User</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(userId, role, userPermissions) {
        const form = document.getElementById('editAccessForm');
        form.action = `/users/${userId}/permissions`;
        
        document.getElementById('editRole').value = role;

        // Reset semua checkbox
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);

        // Centang checkbox yang dimiliki user
        if (Array.isArray(userPermissions)) {
            userPermissions.forEach(perm => {
                const cb = document.getElementById(`perm_${perm}`);
                if (cb) cb.checked = true;
            });
        }

        document.getElementById('editAccessModal').classList.remove('hidden');
        document.getElementById('editAccessModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editAccessModal').classList.remove('flex');
        document.getElementById('editAccessModal').classList.add('hidden');
    }
</script>
@endsection