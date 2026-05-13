@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8">
    <div class="flex flex-col items-center mb-8">
        <img src="{{ asset('images/ippi_logo.jpg') }}" alt="IPPI Logo" class="h-16 w-auto object-contain mb-3" />
        <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.2em] text-center">PT INTI PANTJA PRESS INDUSTRI</p>
        <div class="w-12 h-1 bg-indigo-500 rounded-full mt-4 mb-6"></div>
        <h1 class="text-xl font-display text-slate-900">Selamat datang kembali</h1>
        <p class="text-slate-500 text-xs mt-1 text-center">Masuk ke akun E-Budgeting System Anda</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                          border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                          transition-all outline-none @error('email') border-rose-400 bg-rose-50 @enderror"
                   placeholder="nama@perusahaan.com" />
            @error('email')
                <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="text-xs font-medium text-slate-600">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-indigo-500 hover:text-indigo-700 transition-colors">Lupa password?</a>
                @endif
            </div>
            <div class="relative">
                <input id="password" type="password" name="password" required
                       class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                              border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                              transition-all outline-none @error('password') border-rose-400 bg-rose-50 @enderror"
                       placeholder="••••••••" />
                <button type="button" id="toggle-password-btn" onclick="togglePassword('password', 'toggle-password-btn')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1">
                    {{-- Eye Icon --}}
                    <svg class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{-- Eye Off Icon --}}
                    <svg class="w-5 h-5 eye-off-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
            <label for="remember_me" class="text-xs text-slate-600">Ingat saya</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                       text-white text-sm font-medium transition-all duration-150
                       shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 mt-2">
            Masuk
        </button>
    </form>

    @if (Route::has('register'))
    <p class="mt-6 text-center text-xs text-slate-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:text-indigo-800 transition-colors">Daftar sekarang</a>
    </p>
    @endif
</div>
@endsection