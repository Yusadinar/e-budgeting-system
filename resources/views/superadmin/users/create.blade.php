@extends('layouts.superadmin')
@section('title', 'Tambah User')
@section('page-title', 'Tambah User')
@section('page-subtitle', 'Buat akun pengguna baru')

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
        <h3 class="text-lg font-semibold text-slate-800 mb-6">Informasi User Baru</h3>

        <form method="POST" action="{{ route('superadmin.users.store') }}" class="space-y-5"
              x-data="userCreateData()">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all"
                       placeholder="Masukkan nama lengkap">
                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all"
                       placeholder="user@example.com">
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
                            <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>
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
                            <option value="{{ $d->id }}" {{ old('dept_id') == $d->id ? 'selected' : '' }}>
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
                </div>
            </div>

            {{-- Cost Center Assignment --}}
            <div>
                <label for="cost_center_id" class="block text-sm font-medium text-slate-700 mb-1.5">Cost Center <span class="text-xs text-slate-400">(Opsional)</span></label>
                <select name="cost_center_id" id="cost_center_id" x-model="costCenterId"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 bg-white">
                    <option value="">— Tanpa Cost Center —</option>
                    @foreach(\App\Models\CostCenter::orderBy('department_group')->orderBy('cost_center_name')->get() as $cc)
                        <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>
                            [{{ $cc->plant }}] {{ $cc->department_group }} — {{ $cc->cost_center_name }}{{ $cc->cost_center_code ? ' ('.$cc->cost_center_code.')' : '' }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Jika dipilih, budget user akan dihitung dari cost center ini.</p>
                @error('cost_center_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all"
                           placeholder="Minimal 8 karakter">
                    @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500/30 focus:border-violet-400 transition-all"
                           placeholder="Ulangi password">
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-500 to-indigo-600 hover:from-violet-600 hover:to-indigo-700
                               text-white text-sm font-semibold transition-all shadow-lg shadow-violet-500/25">
                    Simpan User
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
function userCreateData() {
    return {
        role: @json(old('role')),
        dept: @json((string) old('dept_id')),
        oldSection: @json(old('section')),
        costCenterId: @json((string) old('cost_center_id')),
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
