@extends(Auth::user()->isSuperAdmin() ? 'layouts.superadmin' : 'layouts.app')
@section('title', 'Profil Pengguna')
@section('page-title', 'Profil Pengguna')
@section('page-subtitle', 'Kelola informasi akun dan pengaturan keamanan')

@section('content')
<div class="max-w-3xl space-y-6 animate-page">

    {{-- Back Button --}}
    <div>
        <a href="{{ Auth::user()->isSuperAdmin() ? route('superadmin.dashboard') : (Auth::user()->isDirector() ? route('director.dashboard') : route('dashboard')) }}" 
           class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:border-slate-300 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="font-medium">Kembali ke Dashboard</span>
        </a>
    </div>

    {{-- Update Profile Info --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Informasi Profil</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui nama dan alamat email akun Anda.</p>
        </div>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-xs font-medium text-slate-600 mb-1.5">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                       class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                              border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                              transition-all outline-none @error('name') border-rose-400 bg-rose-50 @enderror" />
                @error('name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-medium text-slate-600 mb-1.5">Email Kantor</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                              border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                              transition-all outline-none @error('email') border-rose-400 bg-rose-50 @enderror" />
                @error('email') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-slate-800">
                            Email Anda belum diverifikasi.
                            <button form="send-verification" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Klik di sini untuk mengirim ulang email verifikasi.
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm text-emerald-600 font-medium">Link verifikasi baru telah dikirim ke alamat email Anda.</p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4 mt-6">
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/25">
                    Simpan Perubahan
                </button>

                @if (session('status') === 'profile-updated')
                    <p class="text-sm text-emerald-600 font-medium">Tersimpan.</p>
                @endif
            </div>
        </form>
    </div>

    {{-- Update Password --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Ubah Password</h2>
            <p class="text-sm text-slate-500 mt-1">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>
        </div>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="update_password_current_password" class="block text-xs font-medium text-slate-600 mb-1.5">Password Saat Ini</label>
                <div class="relative">
                    <input id="update_password_current_password" type="password" name="current_password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                                  border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                                  transition-all outline-none @error('current_password', 'updatePassword') border-rose-400 bg-rose-50 @enderror" />
                    <button type="button" id="toggle-current-password" onclick="togglePassword('update_password_current_password', 'toggle-current-password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1">
                        <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
                @error('current_password', 'updatePassword') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="update_password_password" class="block text-xs font-medium text-slate-600 mb-1.5">Password Baru</label>
                <div class="relative">
                    <input id="update_password_password" type="password" name="password" required
                           class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                                  border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                                  transition-all outline-none @error('password', 'updatePassword') border-rose-400 bg-rose-50 @enderror" />
                    <button type="button" id="toggle-new-password" onclick="togglePassword('update_password_password', 'toggle-new-password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1">
                        <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
                @error('password', 'updatePassword') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-xs font-medium text-slate-600 mb-1.5">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input id="update_password_password_confirmation" type="password" name="password_confirmation" required
                           class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                                  border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                                  transition-all outline-none" />
                    <button type="button" id="toggle-confirm-new-password" onclick="togglePassword('update_password_password_confirmation', 'toggle-confirm-new-password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1">
                        <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-6">
                <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors shadow-lg shadow-indigo-500/25">
                    Update Password
                </button>

                @if (session('status') === 'password-updated')
                    <p class="text-sm text-emerald-600 font-medium">Password berhasil diubah.</p>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(inputId, btnId) {
        const passwordInput = document.getElementById(inputId);
        const toggleBtn = document.getElementById(btnId);
        if(!passwordInput || !toggleBtn) return;
        
        const eyeIcon = toggleBtn.querySelector('.eye-icon');
        const eyeOffIcon = toggleBtn.querySelector('.eye-off-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }
</script>
@endpush
