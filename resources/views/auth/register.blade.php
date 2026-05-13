@extends('layouts.guest')
@section('title', 'Daftar Akun')

@section('content')
<div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8">
    <div class="flex flex-col items-center mb-8">
        <img src="{{ asset('images/ippi_logo.jpg') }}" alt="IPPI Logo" class="h-16 w-auto object-contain mb-3" />
        <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.2em] text-center">PT INTI PANTJA PRESS INDUSTRI</p>
        <div class="w-12 h-1 bg-indigo-500 rounded-full mt-4 mb-6"></div>
        <h1 class="text-xl font-display text-slate-900">Buat akun baru</h1>
        <p class="text-slate-500 text-xs mt-1 text-center">Daftarkan diri Anda ke E-Budgeting System</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Nama --}}
        <div>
            <label for="name" class="block text-xs font-medium text-slate-600 mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                          border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                          transition-all outline-none @error('name') border-rose-400 bg-rose-50 @enderror"
                   placeholder="Masukkan nama lengkap" />
            @error('name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-medium text-slate-600 mb-1.5">Email Kantor</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                          border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                          transition-all outline-none @error('email') border-rose-400 bg-rose-50 @enderror"
                   placeholder="nama@perusahaan.com" />
            @error('email') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        {{-- Departemen --}}
        <div>
            <label for="dept_id" class="block text-xs font-medium text-slate-600 mb-1.5">Departemen</label>
            <select id="dept_id" name="dept_id" required
                    class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800
                           border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                           transition-all outline-none @error('dept_id') border-rose-400 bg-rose-50 @enderror">
                <option value="">-- Pilih Departemen --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('dept_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->dept_name }}
                    </option>
                @endforeach
            </select>
            @error('dept_id') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-xs font-medium text-slate-600 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required
                   class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                          border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                          transition-all outline-none @error('password') border-rose-400 bg-rose-50 @enderror"
                   placeholder="Min. 8 karakter" />
            @error('password') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-medium text-slate-600 mb-1.5">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="w-full px-3.5 py-2.5 rounded-xl border bg-slate-50 text-sm text-slate-800 placeholder-slate-400
                          border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white
                          transition-all outline-none"
                   placeholder="Ulangi password" />
        </div>

        <button type="submit"
                class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
                       text-white text-sm font-medium transition-all duration-150
                       shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 mt-2">
            Buat Akun
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-slate-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-indigo-600 font-medium hover:text-indigo-800 transition-colors">Masuk di sini</a>
    </p>
</div>
@endsection