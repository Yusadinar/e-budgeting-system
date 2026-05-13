{{--
    Komponen Nav Item untuk Sidebar
    Props:
      - href   : URL tujuan
      - active : boolean, apakah item ini sedang aktif
      - label  : teks label menu
      - slot   : icon SVG
--}}

@props([
    'href'   => '#',
    'active' => false,
    'label'  => '',
])

<a href="{{ $href }}"
   @class([
       'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150',
       'bg-indigo-500 text-white shadow-lg shadow-indigo-500/25' => $active,
       'text-slate-400 hover:text-white hover:bg-white/[0.07]'  => ! $active,
   ])>

    {{-- Icon slot --}}
    {{ $slot }}

    {{-- Label --}}
    <span class="truncate">{{ $label }}</span>

    {{-- Active indicator dot --}}
    @if($active)
    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-white/60 shrink-0"></span>
    @endif

</a>