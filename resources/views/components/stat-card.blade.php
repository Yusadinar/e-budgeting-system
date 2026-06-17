{{--
    Komponen Summary / Stat Card untuk Dashboard
    Props:
      - title      : judul kartu
      - value      : nilai utama (string, sudah diformat)
      - subtitle   : teks kecil di bawah nilai
      - color      : 'emerald' | 'amber' | 'indigo' | 'rose' (default: indigo)
      - delay      : class animasi delay (default: '')
--}}

@props([
    'title'    => '',
    'value'    => '0',
    'subtitle' => '',
    'color'    => 'indigo',
    'delay'    => '',
])

@php
$colorMap = [
    'emerald' => [
        'bg'       => 'bg-emerald-50',
        'icon_bg'  => 'bg-emerald-50',
        'icon_text'=> 'text-emerald-600',
        'value'    => 'text-emerald-600',
        'blob'     => 'bg-emerald-50',
    ],
    'amber' => [
        'bg'       => 'bg-white',
        'icon_bg'  => 'bg-amber-50',
        'icon_text'=> 'text-amber-500',
        'value'    => 'text-amber-500',
        'blob'     => 'bg-amber-50',
    ],
    'indigo' => [
        'bg'       => 'bg-white',
        'icon_bg'  => 'bg-indigo-50',
        'icon_text'=> 'text-indigo-500',
        'value'    => 'text-indigo-600',
        'blob'     => 'bg-indigo-50',
    ],
    'rose' => [
        'bg'       => 'bg-white',
        'icon_bg'  => 'bg-rose-50',
        'icon_text'=> 'text-rose-500',
        'value'    => 'text-rose-600',
        'blob'     => 'bg-rose-50',
    ],
];

$c = $colorMap[$color] ?? $colorMap['indigo'];
@endphp

<div @class([
    'relative rounded-2xl border border-slate-100 p-5 shadow-card hover:shadow-card-hover transition-shadow duration-200 overflow-hidden',
    $c['bg'],
    $delay,
])>

    {{-- Decorative blob --}}
    <div class="absolute top-0 right-0 w-24 h-24 {{ $c['blob'] }} rounded-full -translate-y-8 translate-x-8 opacity-60 pointer-events-none"></div>

    <div class="relative">
        {{-- Header row: title + icon --}}
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wide leading-tight">
                {{ $title }}
            </span>
            <span class="w-12 h-12 rounded-xl {{ $c['icon_bg'] }} flex items-center justify-center shrink-0">
                {{-- Icon slot --}}
                <span class="{{ $c['icon_text'] }}">
                    {{ $slot }}
                </span>
            </span>
        </div>

        {{-- Main value --}}
        <p class="text-2xl font-display {{ $c['value'] }} leading-tight">
            {{ $value }}
        </p>

        {{-- Subtitle --}}
        @if($subtitle)
        <p class="text-xs text-slate-400 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
</div>