{{-- iClinicSys checkbox / option mark (read-only display) --}}
@php
    $checked = $checked ?? false;
    $label = $label ?? '';
    $subLabel = $subLabel ?? null;
    $inline = $inline ?? true;
@endphp
<div @class(['flex gap-0.5', 'items-start' => true, 'inline-flex mr-2' => $inline, 'mb-0.5' => ! $inline])>
    <span class="w-3 h-3 shrink-0 border border-black inline-flex items-center justify-center text-[8px] font-bold leading-none mt-px">
        {{ $checked ? 'X' : '' }}
    </span>
    <span class="text-[9px] leading-tight">
        {{ $label }}
        @if ($subLabel)
            <span class="block text-[8px] font-normal">{{ $subLabel }}</span>
        @endif
    </span>
</div>
