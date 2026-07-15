{{--
    Shared DOH / iClinicSys form header.
    $formTitle — e.g. PATIENT ENROLMENT RECORD
    $serialDigits — number of digit boxes for Family Serial Number (4 or 12)
--}}
@php
    $formTitle = $formTitle ?? 'FORM';
    $serialDigits = (int) ($serialDigits ?? 4);
    $householdId = $patient->household_record_id ?? $patient->household_id ?? '';
    $serialSource = $householdId !== '' && $householdId !== null
        ? str_pad((string) $householdId, $serialDigits, '0', STR_PAD_LEFT)
        : str_repeat('0', $serialDigits);
    $serialChars = str_split(substr($serialSource, -$serialDigits));
    $facilityCode = config('app.facility_code', '    ');
    $facilityChars = str_split($facilityCode);

    $logoPath = public_path('img/Department_of_Health_(DOH)_PHL.svg.webp');
    if (! file_exists($logoPath)) {
        $logoPath = public_path('img/logo.svg');
    }
    $logoMime = match (pathinfo($logoPath, PATHINFO_EXTENSION)) {
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        default => 'image/jpeg',
    };
    $logoSrc = 'data:'.$logoMime.';base64,'.base64_encode((string) file_get_contents($logoPath));
@endphp

<table class="form-table" style="border-bottom:0;">
    <tr>
        <td rowspan="2" style="width:52%; padding:3px 5px; vertical-align:middle;">
            <div class="doh-header-brand">
                <div class="doh-logo-wrap">
                    <div class="logo-circle">
                        <img src="{{ $logoSrc }}" alt="DOH">
                    </div>
                </div>
                <div class="doh-brand">
                    <p class="rep">Republic of the Philippines</p>
                    <p class="dept">Department of Health</p>
                    <p class="dept-fil">Kagawaran ng Kalusugan</p>
                </div>
            </div>
        </td>
        <td class="label-cell" style="width:20%;">Family Serial Number</td>
        <td style="padding:0; width:28%;">
            <table class="digit-row form-table" style="border:0; height:100%;">
                <tr>
                    @foreach ($serialChars as $digit)
                        <td class="digit-box" style="border-top:0; border-bottom:0;{{ $loop->last ? ' border-right:0;' : '' }}">{{ $digit }}</td>
                    @endforeach
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="label-cell">Facility Code</td>
        <td style="padding:0;">
            <table class="digit-row form-table" style="border:0; height:100%;">
                <tr>
                    @foreach ($facilityChars as $char)
                        <td class="digit-box" style="border-top:0; border-bottom:0;{{ $loop->last ? ' border-right:0;' : '' }}">{{ $char }}</td>
                    @endforeach
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="form-header-block">
    <p class="title-caption">Integrated Clinic Information System (iCLINICSYS)</p>
    <h1>{{ $formTitle }}</h1>
</div>

<div class="instructions">
    @if (($formTitle ?? '') === 'PATIENT ENROLMENT RECORD')
        <strong>Instructions:</strong> For new patient only. Please print legibly and mark appropriate boxes with "X".
        <span style="font-style:normal;">/</span>
        <strong>Tagubilin:</strong> Para sa bagong pasyente lamang. Sulatan nang malinaw at lagyan ng "X" ang naaangkop na kahon.
    @else
        <strong>Instructions:</strong> Please print legibly and mark appropriate boxes with "X".
        <span style="font-style:normal;">/</span>
        <strong>Tagubilin:</strong> Sulatan nang malinaw at lagyan ng "X" ang naaangkop na kahon.
    @endif
</div>
