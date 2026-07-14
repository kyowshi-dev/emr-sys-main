{{--
    Shared DOH / iClinicSys form header.
    $formTitle — e.g. PATIENT ENROLMENT RECORD
    $serialDigits — number of digit boxes for Family Serial Number (4 or 12)
--}}
@php
    $formTitle = $formTitle ?? 'FORM';
    $serialDigits = $serialDigits ?? 4;
    $householdId = $patient->household_record_id ?? $patient->household_id ?? '';
    $serial = str_pad('0', $serialDigits, '0', STR_PAD_LEFT);
    $serialChars = str_split(substr($serial, -$serialDigits));
    $facilityCode = config('app.facility_code', 'DOH000000000038890');
@endphp

<div class="grid grid-cols-12 border border-black border-b-0 iclinic-form-header">
    {{-- DOH branding --}}
    <div class="col-span-7 flex gap-2 p-2 border-r border-black min-h-[64px]">
        <div class="w-11 h-11 shrink-0 border border-black rounded-full flex items-center justify-center overflow-hidden bg-white iclinic-logo">
            <img src="{{ asset('img/Department_of_Health_(DOH)_PHL.svg.webp') }}" alt="Department of Health Logo" class="w-9 h-9">
        </div>
        <div class="leading-tight">
            <p class="text-[8px] m-0">Republic of the Philippines</p>
            <p class="text-[11px] font-bold text-[#1a5c2e] leading-none m-0">Department of Health</p>
            <p class="text-[9px] italic leading-none m-0">Kagawaran ng Kalusugan</p>
        </div>
    </div>

    {{-- Family Serial Number + Facility Code --}}
    <div class="col-span-5 grid grid-rows-[auto_auto]">
        <div class="grid grid-cols-12 border-b border-black">
            <div class="col-span-5 bg-gray-300 border-r border-black p-1 text-[8px] font-bold leading-tight flex items-center">
                Family Serial Number
            </div>
            <div class="col-span-7 grid grid-cols-4">
                
            </div>
        </div>

        <div class="grid grid-cols-12">
            <div class="col-span-5 bg-gray-300 border-r border-black p-1 text-[8px] font-bold leading-tight flex items-center">
                Facility Code
            </div>
            <div class="col-span-7 grid grid-cols-12">
                
            </div>
        </div>
    </div>
</div>

<div class="border border-black border-b-0 text-center py-1 form-header">
    <p class="text-[10px] tracking-wide title-caption">Integrated Clinic Information System (iCLINICSYS)</p>
    <h1 class="text-[12px] font-bold tracking-wide uppercase m-0">{{ $formTitle }}</h1>
</div>

<div class="border border-black border-b-0 px-1 py-1 text-[8px] italic leading-tight instructions">
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
