@extends('layouts.superadmin')
@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Perbarui informasi pengguna')

@section('content')

<div class="max-w-2xl animate-page">
    {{-- Back Button --}}
    <a href="{{ route('superadmin.users.index') }}"
       class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-violet-600 transition-colors mb-5">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
        </svg>
        Kembali ke daftar user
    </a>

    <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-card">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center">
                <span class="text-violet-600 text-lg font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-800">{{ $user->name }}</h3>
                <p class="text-xs text-slate-400">Bergabung {{ $user->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('superadmin.users.update', $user) }}" class="space-y-5"
              x-data="userEditData()">
            @csrf @method('PUT')

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all">
                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all">
                @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Role & Department --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-1.5">Role</label>
                    <select name="role" id="role" required x-model="role"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>
                                {{ match($r) {
                                    'staff'       => 'Staff',
                                    'ka_sie'      => 'Ka. Seksi',
                                    'ka_dept'     => 'Ka. Departemen',
                                    'ka_div'      => 'Ka. Divisi',
                                    'fin_dir'     => 'Fin. Director',
                                    'man_dir'     => 'Man. Director',
                                    'prod_dir'    => 'Prod. Director',
                                    'pres_dir'    => 'Pres. Director',
                                    default       => $r
                                } }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="dept_id" class="block text-sm font-medium text-slate-700 mb-1.5">Departemen</label>
                    <select name="dept_id" id="dept_id" x-model="dept"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
                        <option value="">Tanpa Departemen</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('dept_id', $user->dept_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->dept_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('dept_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div x-show="role === 'staff' || role === 'ka_sie'" x-cloak>
                    <label for="section" class="block text-sm font-medium text-slate-700 mb-1.5">Seksi <span class="text-rose-500">*</span></label>
                    
                    <template x-if="role === 'staff'">
                        <select name="section" id="section_select" :required="role === 'staff'" x-model="oldSection"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
                            <option value="">Pilih Seksi</option>
                            <template x-for="sec in availableSections()" :key="sec">
                                <option :value="sec" x-text="sec"></option>
                            </template>
                        </select>
                    </template>
                    
                    <template x-if="role === 'ka_sie'">
                        <input type="text" name="section" id="section_input" x-model="oldSection" :required="role === 'ka_sie'"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all"
                               placeholder="Input nama seksi">
                    </template>
                    
                    <p x-show="role === 'staff' && availableSections().length === 0 && dept" class="text-xs text-amber-500 mt-1">Belum ada seksi di departemen ini (Buat akun Ka. Seksi dulu).</p>
                    @error('section') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    
                    <div class="mt-4 p-2 bg-gray-100 text-xs text-red-500 font-mono hidden">
                        DEBUG INFO: <br>
                        Role: <span x-text="role"></span><br>
                        Dept: <span x-text="dept"></span><br>
                        SectionsData: <span x-text="JSON.stringify(sectionsData)"></span><br>
                        AvailableSections: <span x-text="JSON.stringify(availableSections())"></span>
                    </div>
                </div>
            </div>

            {{-- Password (Optional) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all"
                           placeholder="Kosongkan jika tidak diubah">
                    @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all"
                           placeholder="Ulangi password baru">
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-600 hover:to-indigo-700
                               text-white text-sm font-semibold transition-all shadow-lg shadow-violet-500/25">
                    Perbarui User
                </button>
                <a href="{{ route('superadmin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function userEditData() {
    return {
        role: @json(old('role', $user->role)),
        dept: @json((string) old('dept_id', $user->dept_id)),
        oldSection: @json(old('section', $user->section)),
        sectionsData: @json($sectionsByDept ?? []),
        availableSections() {
            if (!this.dept || typeof this.sectionsData !== 'object' || !this.sectionsData[this.dept]) {
                return [];
            }
            return this.sectionsData[this.dept];
        }
    }
}
</script>
@endsection
