@extends('layouts.superadmin')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen pengguna sistem e-budgeting')

@section('content')

{{-- Header Actions --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 animate-page">
    <div>
        <h2 class="text-lg font-display text-slate-900">Daftar User</h2>
        <p class="text-sm text-slate-500 mt-0.5">{{ $users->total() }} pengguna terdaftar</p>
    </div>
    <a href="{{ route('superadmin.users.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-600 hover:to-indigo-700
              text-white text-sm font-semibold transition-all shadow-lg shadow-violet-500/25 hover:shadow-violet-500/35">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah User
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card mb-5 animate-page-delay-1">
    <form method="GET" action="{{ route('superadmin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                   class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all placeholder:text-slate-300">
        </div>
        <select name="role"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
            <option value="">Semua Role</option>
            @foreach($roles as $role)
                <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                    {{ match($role) {
                        'staff'       => 'Staff',
                        'ka_sie'      => 'Ka. Seksi',
                        'ka_dept'     => 'Ka. Dept',
                        'ka_div'      => 'Ka. Div',
                        'fin_dir'     => 'Fin. Director',
                        'man_dir'     => 'Man. Director',
                        'prod_dir'    => 'Prod. Director',
                        'pres_dir'    => 'Pres. Director',
                        default       => $role
                    } }}
                </option>
            @endforeach
        </select>
        <select name="dept_id"
                class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-600 focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
            <option value="">Semua Departemen</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('dept_id') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->dept_name }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['search', 'role', 'dept_id']))
            <a href="{{ route('superadmin.users.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-500 text-sm font-medium hover:bg-slate-50 transition-colors text-center">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Desktop Table --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-card overflow-hidden animate-page-delay-2 hidden sm:block">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50/60 border-b border-slate-100">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">#</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Email</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Role</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Departemen</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Terdaftar</th>
                    <th class="text-center py-3 px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $i => $user)
                <tr class="hover:bg-violet-50/30 transition-colors">
                    <td class="py-3 px-4 text-slate-400 text-xs">{{ $users->firstItem() + $i }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center shrink-0">
                                <span class="text-violet-600 text-xs font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <span class="font-medium text-slate-800">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-slate-500">{{ $user->email }}</td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                            {{ match($user->role) {
                                'ka_div'                 => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                                'ka_dept'                => 'bg-sky-50 text-sky-700 border border-sky-200',
                                'ka_sie'                 => 'bg-teal-50 text-teal-700 border border-teal-200',
                                'fin_dir', 'man_dir', 'prod_dir', 'pres_dir' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                default                  => 'bg-slate-50 text-slate-600 border border-slate-200',
                            } }}">
                            {{ match($user->role) {
                                'staff'       => 'Staff',
                                'ka_sie'      => 'Ka. Seksi',
                                'ka_dept'     => 'Ka. Dept',
                                'ka_div'      => 'Ka. Div',
                                'fin_dir'     => 'Fin. Director',
                                'man_dir'     => 'Man. Director',
                                'prod_dir'    => 'Prod. Director',
                                'pres_dir'    => 'Pres. Director',
                                default       => $user->role
                            } }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-slate-500 text-xs">
                        {{ $user->department?->dept_name ?? '—' }}
                        @if($user->section)
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $user->section }}</div>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-slate-400 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('superadmin.users.edit', $user) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-violet-600 hover:bg-violet-50 transition-all" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            </a>
                            <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" onsubmit="return confirm('Yakin ingin menghapus user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-12 text-center text-slate-400 text-sm">Tidak ada user ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $users->links() }}</div>
    @endif
</div>

{{-- Mobile Card View --}}
<div class="sm:hidden space-y-3 animate-page-delay-2">
    @forelse($users as $user)
    <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-card">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center shrink-0">
                <span class="text-violet-600 text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold shrink-0
                {{ match($user->role) {
                    'ka_div'                 => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
                    'ka_dept'                => 'bg-sky-50 text-sky-700 border border-sky-200',
                    'ka_sie'                 => 'bg-teal-50 text-teal-700 border border-teal-200',
                    'fin_dir', 'man_dir', 'prod_dir', 'pres_dir' => 'bg-amber-50 text-amber-700 border border-amber-200',
                    default                  => 'bg-slate-50 text-slate-600 border border-slate-200',
                } }}">
                {{ match($user->role) {
                    'staff'       => 'Staff',
                    'ka_sie'      => 'Ka. Seksi',
                    'ka_dept'     => 'Ka. Dept',
                    'ka_div'      => 'Ka. Div',
                    'fin_dir'     => 'Fin. Director',
                    'man_dir'     => 'Man. Director',
                    'prod_dir'    => 'Prod. Director',
                    'pres_dir'    => 'Pres. Director',
                    default       => $user->role
                } }}
            </span>
        </div>
        <div class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-50">
            <div>
                <span>{{ $user->department?->dept_name ?? '—' }}</span>
                @if($user->section)
                    <span class="block text-[10px]">{{ $user->section }}</span>
                @endif
                <span class="block mt-0.5">{{ $user->created_at->format('d M Y') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('superadmin.users.edit', $user) }}" class="p-2 rounded-lg text-violet-500 bg-violet-50 active:bg-violet-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                </a>
                <form method="POST" action="{{ route('superadmin.users.destroy', $user) }}" onsubmit="return confirm('Yakin hapus {{ $user->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 rounded-lg text-rose-500 bg-rose-50 active:bg-rose-100">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-100 p-8 text-center text-slate-400 text-sm">Tidak ada user ditemukan</div>
    @endforelse
    @if($users->hasPages())
    <div class="mt-4">{{ $users->links() }}</div>
    @endif
</div>

@endsection
