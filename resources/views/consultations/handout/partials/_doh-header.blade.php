{{--
    Shared DOH / iClinicSys form header.
    $formTitle — e.g. PATIENT ENROLMENT RECORD
    $serialDigits — number of digit boxes for Family Serial Number (4 or 12)
--}}
@php
    $formTitle = $formTitle ?? 'FORM';
    $serialDigits = $serialDigits ?? 4;
    $householdId = $patient->household_record_id ?? $patient->household_id ?? '';
    $serial = str_pad((string) $householdId, $serialDigits, '0', STR_PAD_LEFT);
    $serialChars = str_split(substr($serial, -$serialDigits));
    $facilityCode = config('app.facility_code', 'BHCIS001');
@endphp

<div class="grid grid-cols-12 border border-black border-b-0">
    {{-- DOH branding --}}
    <div class="col-span-7 flex gap-1.5 p-1 border-r border-black">
        <div class="w-10 h-10 shrink-0 border border-black rounded-full flex items-center justify-center overflow-hidden bg-white">
            <svg viewBox="0 0 40 40" class="w-9 h-9" aria-hidden="true">
                <circle cx="20" cy="20" r="18" fill="none" stroke="#1a5c2e" stroke-width="2"/>
                <text x="20" y="17" text-anchor="middle" font-size="5" font-weight="bold" fill="#1a5c2e">DOH</text>
                <text x="20" y="24" text-anchor="middle" font-size="3.5" fill="#333">PH</text>
            </svg>
        </div>
        <div class="leading-tight">
            <p class="text-[8px]">Republic of the Philippines</p>
            <p class="text-[11px] font-bold text-[#1a5c2e] leading-none">Department of Health</p>
            <p class="text-[9px] italic">Kagawaran ng Kalusugan</p>
        </div>
    </div>

    {{-- Family Serial Number + Facility Code --}}
    <div class="col-span-5 grid grid-rows-2">
        <div class="grid grid-cols-12 border-b border-black">
            <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 text-[8px] font-bold leading-tight flex items-center">
                Family Serial Number
            </div>
            <div class="col-span-7 flex">
                @foreach ($serialChars as $digit)
                    <span class="flex-1 border-r border-black last:border-r-0 text-center text-[10px] font-semibold py-0.5">{{ $digit }}</span>
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-12">
            <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 text-[8px] font-bold leading-tight flex items-center">
                Facility Code
            </div>
            <div class="col-span-7 p-0.5 text-[10px] font-semibold tracking-wider">
                @if ($serialDigits > 4)
                    @foreach (str_split(str_pad($facilityCode, 10, ' ', STR_PAD_RIGHT)) as $char)
                        <span class="inline-block w-[9%] text-center border-r border-black last:border-r-0">{{ trim($char) }}</span>
                    @endforeach
                @else
                    {{ $facilityCode }}
                @endif
            </div>
        </div>
    </div>
</div>

<div class="border border-black border-b-0 text-center py-1">
    <p class="text-[10px] tracking-wide">Integrated Clinic Information System (iCLINICSYS)</p>
    <h1 class="text-sm font-bold tracking-wide uppercase">{{ $formTitle }}</h1>
</div>

<div class="border border-black border-b-0 px-1 py-0.5 text-[8px] italic leading-tight">
    @if (($formTitle ?? '') === 'PATIENT ENROLMENT RECORD')
        <strong>Instructions:</strong> For new patient only. Please print legibly and mark appropriate boxes with "X".
        <span class="not-italic">/</span>
        <strong>Tagubilin:</strong> Para sa bagong pasyente lamang. Sulatan nang malinaw at lagyan ng "X" ang naaangkop na kahon.
    @else
        <strong>Instructions:</strong> Please print legibly and mark appropriate boxes with "X".
        <span class="not-italic">/</span>
        <strong>Tagubilin:</strong> Sulatan nang malinaw at lagyan ng "X" ang naaangkop na kahon.
    @endif
</div>
