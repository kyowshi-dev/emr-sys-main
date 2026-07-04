{{--
    Patient Enrollment Section — iClinicSys FORM 1
    Visual section included from handout.blade.php (separate partial file).
--}}
@php
    use Illuminate\Support\Str;

    $dob = $patient?->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth) : null;
    $dobFormatted = $dob?->format('m/d/Y') ?? '';
    $isFemale = ($patient->sex ?? '') === 'Female';
    $isMale = ($patient->sex ?? '') === 'Male';
    $civilStatus = $patient->civil_status ?? '';
    $education = $patient->educational_attainment ?? '';
    $employment = Str::lower($patient->employment_status ?? '');
    $relationship = $patient->family_relationship ?? '';
    $isPhilhealth = ($patient->is_philhealth_member ?? 'n') === 'y';
    $isPcb = ($patient->is_pcb_member ?? 'n') === 'y';
    $hasNhts = (bool) ($patient->has_nhts ?? false);
    $has4ps = (bool) ($patient->has_4ps ?? false);
    $philhealthCategory = $patient->membership_category ?? '';
    $statusType = $patient->status_type ?? '';
    $contactNumber = $patient->household_contact_number ?? '';
    $householdNo = $patient->household_record_id ?? $patient->household_id ?? '';
@endphp

<section class="iclinic-form bg-white text-black" aria-label="Patient Enrolment Record">
    @include('consultations.handout.partials._doh-header', [
        'formTitle' => 'PATIENT ENROLMENT RECORD',
        'serialDigits' => 4,
        'patient' => $patient,
    ])

    {{-- Section I: Patient Information --}}
    <div class="bg-gray-500 text-white font-bold text-[9px] px-1 py-0.5 border border-black border-b-0 uppercase tracking-wide">
        I. Patient Information (Impormasyon ng Pasyente)
    </div>

    <div class="border border-black text-[9px] leading-tight">
        <div class="grid grid-cols-12 border-b border-black">
            <div class="col-span-8 border-r border-black box-cell">
                <p class="field-label">Last Name <span class="field-help">(Apelyido)</span></p>
                <p class="field-value font-semibold">{{ ucfirst($patient->last_name ?? '') }}</p>
            </div>
            <div class="col-span-4 box-cell">
                <p class="field-label">Suffix <span class="field-help">(e.g. Jr., Sr., II, III)</span></p>
                <p class="field-value">{{ ucfirst($patient->suffix ?? '') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-12 border-b border-black">
            <div class="col-span-6 border-r border-black box-cell">
                <p class="field-label">First Name <span class="field-help">(Pangalan)</span></p>
                <p class="field-value font-semibold">{{ ucwords($patient->first_name ?? '') }}</p>
            </div>
            <div class="col-span-6 box-cell">
                <p class="field-label">Maiden Name <span class="field-help">(for married women)</span></p>
                <p class="field-value">&nbsp;</p>
            </div>
        </div>

        <div class="grid grid-cols-12 border-b border-black">
            <div class="col-span-6 border-r border-black box-cell">
                <p class="field-label">Middle Name <span class="field-help">(Gitnang Pangalan)</span></p>
                <p class="field-value">{{ $patient->middle_name ?? '' }}</p>
            </div>
            <div class="col-span-6 box-cell">
                <p class="field-label">Mother's Name <span class="field-help">(Pangalan ng Ina)</span></p>
                <p class="field-value">{{ ucwords($patient->mother_name ?? '') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-12">
            <div class="col-span-8 border-r border-black">
                <div class="grid grid-cols-12 border-b border-black">
                    <div class="col-span-5 border-r border-black box-cell">
                        <p class="field-label mb-0.5">Sex <span class="field-help">(Kasarian)</span></p>
                        <div class="flex flex-wrap gap-x-1">
                            @include('consultations.handout.partials._mark', ['checked' => $isFemale, 'label' => 'Female (Babae)'])
                            @include('consultations.handout.partials._mark', ['checked' => $isMale, 'label' => 'Male (Lalaki)'])
                        </div>
                    </div>
                    <div class="col-span-7 box-cell">
                        <p class="field-label">Birth Date <span class="field-help">(mm/dd/yyyy)</span></p>
                        <p class="field-value">{{ $dobFormatted }}</p>
                    </div>
                </div>

                <div class="border-b border-black box-cell">
                    <p class="field-label">Birthplace <span class="field-help">(Lugar ng Kapanganakan)</span></p>
                    <p class="field-value">{{ ucwords($patient->birth_place ?? '') }}</p>
                </div>

                <div class="border-b border-black box-cell">
                    <p class="field-label">Blood Type</p>
                    <p class="field-value">{{ $patient->blood_type ?? '' }}</p>
                </div>

                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">Civil Status</p>
                    <div class="checkbox-row">
                        @include('consultations.handout.partials._mark', ['checked' => $civilStatus === 'Single', 'label' => 'Single'])
                        @include('consultations.handout.partials._mark', ['checked' => $civilStatus === 'Married', 'label' => 'Married'])
                        @include('consultations.handout.partials._mark', ['checked' => false, 'label' => 'Annulled'])
                        @include('consultations.handout.partials._mark', ['checked' => in_array($civilStatus, ['Widowed', 'Widow/er'], true), 'label' => 'Widow/er'])
                        @include('consultations.handout.partials._mark', ['checked' => $civilStatus === 'Separated', 'label' => 'Separated'])
                        @include('consultations.handout.partials._mark', ['checked' => in_array($civilStatus, ['Common Law', 'Co-Habitation'], true), 'label' => 'Co-Habitation'])
                    </div>
                </div>

                <div class="border-b border-black box-cell">
                    <p class="field-label">Spouse's Name <span class="field-help">(Asawa)</span></p>
                    <p class="field-value">{{ $patient->spouse_name ?? '' }}</p>
                </div>

                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">Educational Attainment</p>
                    <div class="grid grid-cols-2 gap-x-1">
                        @include('consultations.handout.partials._mark', ['checked' => in_array($education, ['None', 'No Formal Education'], true), 'label' => 'No Formal Education'])
                        @include('consultations.handout.partials._mark', ['checked' => $education === 'High School', 'label' => 'High School'])
                        @include('consultations.handout.partials._mark', ['checked' => $education === 'College', 'label' => 'College'])
                        @include('consultations.handout.partials._mark', ['checked' => $education === 'Elementary', 'label' => 'Elementary'])
                        @include('consultations.handout.partials._mark', ['checked' => $education === 'Vocational', 'label' => 'Vocational'])
                        @include('consultations.handout.partials._mark', ['checked' => $education === 'Post Graduate', 'label' => 'Post Graduate'])
                    </div>
                </div>

                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">Employment Status</p>
                    <div class="grid grid-cols-2 gap-x-1">
                        @include('consultations.handout.partials._mark', ['checked' => str_contains($employment, 'student'), 'label' => 'Student'])
                        @include('consultations.handout.partials._mark', ['checked' => str_contains($employment, 'employ'), 'label' => 'Employed'])
                        @include('consultations.handout.partials._mark', [
                            'checked' => in_array($employment, ['none', 'unemployed', 'none/unemployed', '']) || str_contains($employment, 'unemploy'),
                            'label' => 'None/Unemployed'
                        ])
                        @include('consultations.handout.partials._mark', ['checked' => str_contains($employment, 'unknown'), 'label' => 'Unknown'])
                        @include('consultations.handout.partials._mark', ['checked' => str_contains($employment, 'retir'), 'label' => 'Retired'])
                    </div>
                </div>

                <div class="box-cell">
                    <p class="field-label mb-0.5">Family Member <span class="field-help">(Kasapi ng Pamilya)</span></p>
                    <div class="grid grid-cols-2 gap-x-1">
                        @include('consultations.handout.partials._mark', ['checked' => $relationship === 'Father', 'label' => 'Father'])
                        @include('consultations.handout.partials._mark', ['checked' => $relationship === 'Son', 'label' => 'Son'])
                        @include('consultations.handout.partials._mark', ['checked' => $relationship === 'Mother', 'label' => 'Mother'])
                        @include('consultations.handout.partials._mark', ['checked' => $relationship === 'Daughter', 'label' => 'Daughter'])
                        @include('consultations.handout.partials._mark', ['checked' => $relationship === 'Others', 'label' => 'Others'])
                    </div>
                </div>
            </div>

            <div class="col-span-4">
                <div class="border-b border-black box-cell min-h-[72px]">
                    <p class="field-label">Residential Address <span class="field-help">(Tirahan)</span></p>
                    <p class="field-value text-[10px] whitespace-pre-wrap">{{ $patient->residential_address ?? '' }}</p>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label">Contact Number</p>
                    <p class="field-value">{{ $contactNumber }}</p>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">DSWD NHTS?</p>
                    <div class="flex gap-2">
                        @include('consultations.handout.partials._mark', ['checked' => $hasNhts, 'label' => 'Yes'])
                        @include('consultations.handout.partials._mark', ['checked' => ! $hasNhts, 'label' => 'No'])
                    </div>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label">Facility Household No.</p>
                    <p class="field-value">{{ $householdNo ? 'HH'.str_pad($householdNo, 4, '0', STR_PAD_LEFT) : '' }}</p>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">4Ps Member?</p>
                    <div class="flex gap-2">
                        @include('consultations.handout.partials._mark', ['checked' => $has4ps, 'label' => 'Yes'])
                        @include('consultations.handout.partials._mark', ['checked' => ! $has4ps, 'label' => 'No'])
                    </div>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label">Household No.</p>
                    <p class="field-value">{{ $householdNo }}</p>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">PhilHealth Member?</p>
                    <div class="flex gap-2">
                        @include('consultations.handout.partials._mark', ['checked' => $isPhilhealth, 'label' => 'Yes'])
                        @include('consultations.handout.partials._mark', ['checked' => ! $isPhilhealth, 'label' => 'No'])
                    </div>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">Status Type</p>
                    <div class="flex flex-col gap-0.5">
                        @include('consultations.handout.partials._mark', ['checked' => $statusType === 'Member', 'label' => 'Member'])
                        @include('consultations.handout.partials._mark', ['checked' => $statusType === 'Dependent', 'label' => 'Dependent'])
                    </div>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label">PhilHealth No.</p>
                    <p class="field-value">{{ $patient->philhealth_no ?? '' }}</p>
                </div>
                <div class="border-b border-black box-cell">
                    <p class="field-label mb-0.5">PhilHealth Category</p>
                    <div class="flex flex-col gap-0.5">
                        @foreach (['FE - Private', 'FE - Government', 'IE', 'Others'] as $category)
                            @include('consultations.handout.partials._mark', ['checked' => $philhealthCategory === $category, 'label' => $category])
                        @endforeach
                    </div>
                </div>
                <div class="box-cell">
                    <p class="field-label mb-0.5">Primary Care Benefit (PCB) Member?</p>
                    <div class="flex gap-2">
                        @include('consultations.handout.partials._mark', ['checked' => $isPcb, 'label' => 'Yes'])
                        @include('consultations.handout.partials._mark', ['checked' => ! $isPcb, 'label' => 'No'])
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section II: Patient's Consent --}}
    <div class="bg-gray-500 text-white font-bold text-[9px] px-1 py-0.5 border border-black border-b-0 border-t-0 uppercase tracking-wide">
        II. Patient's Consent (Pahintulot ng Pasyente)
    </div>

    <div class="border border-black text-[8px] leading-snug">
        <div class="grid grid-cols-2">
            <div class="border-r border-black">
                <p class="bg-gray-300 font-bold text-center text-[9px] py-0.5 border-b border-black">IN ENGLISH</p>
                <div class="p-1 space-y-1 text-justify">
                    <p>I hereby voluntarily give my consent to the collection, use, storage, and processing of my personal health information by the Department of Health and its authorized health facilities under the iClinicSys program.</p>
                    <p>I understand that my information shall be used for treatment, health care delivery, program monitoring, and reporting in accordance with applicable laws and issuances on data privacy.</p>
                    <p>I acknowledge that I have been informed of my rights regarding my health information and that I may withdraw this consent subject to legal and operational limitations.</p>
                </div>
            </div>
            <div>
                <p class="bg-gray-300 font-bold text-center text-[9px] py-0.5 border-b border-black">SA FILIPINO</p>
                <div class="p-1 space-y-1 text-justify">
                    <p>Kusang loob kong pinahihintulutan ang pagkolekta, paggamit, pag-iimbak, at pagproseso ng aking personal na impormasyong pangkalusugan ng Kagawaran ng Kalusugan at mga awtorisadong pasilidad nito sa ilalim ng iClinicSys.</p>
                    <p>Nauunawaan ko na gagamitin ang aking impormasyon para sa paggamot, paghahatid ng serbisyong pangkalusugan, pagmo-monitor ng programa, at pag-uulat alinsunod sa mga batas at alituntunin ukol sa privacy ng datos.</p>
                    <p>Kinikilala ko na ipinaliwanag sa akin ang aking mga karapatan hinggil sa aking impormasyong pangkalusugan at maaari kong bawiin ang pahintulot na ito alinsunod sa mga legal at operasyonal na limitasyon.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 border-t border-black">
            <div class="border-r border-black p-2 min-h-[48px]">
                <p class="font-bold text-[9px] uppercase">Signature of Patient / Date</p>
                <p class="text-[8px] italic">Pirma ng Pasyente / Petsa</p>
                <div class="mt-6 border-t border-black border-dotted"></div>
            </div>
            <div class="p-2 min-h-[48px]">
                <p class="font-bold text-[9px] uppercase">Name of CHU/RHU Representative</p>
                <p class="text-[8px] italic">Kinatawan ng CHU / RHU</p>
                <div class="mt-6 border-t border-black border-dotted"></div>
            </div>
        </div>
    </div>

    <div class="border border-black border-t-0 bg-gray-400 text-[8px] px-1 py-0.5 text-right font-semibold">
        Clinic Information System | FORM 1
    </div>
</section>
