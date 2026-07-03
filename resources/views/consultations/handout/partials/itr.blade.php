{{--
    Individual Treatment Record (ITR) Section — iClinicSys FORM 2
    Visual section included from handout.blade.php (separate partial file).
--}}
@php
    use Illuminate\Support\Str;

    $consultDate = $consultationAt ?? \Carbon\Carbon::parse($consultation->updated_at ?? $consultation->created_at);
    $isAm = $consultDate->format('A') === 'AM';
    $mode = $consultation->mode_of_transaction ?? '';
    $natureOfVisit = $consultation->nature_of_visit ?? '';
    $isReferral = $mode === 'Referral' || (bool) ($consultation->refer_to_higher_facility ?? false);
    $bp = ($vitals->bp_systolic ?? null) && ($vitals->bp_diastolic ?? null)
        ? $vitals->bp_systolic.'/'.$vitals->bp_diastolic
        : '';
    $temperature = $vitals->temperature_c ?? null;
    $height = $vitals->height_cm ?? null;
    $weight = $vitals->weight_kg ?? null;
    $provider = $attendingProvider ?? '';

    $diagnosisText = $diagnoses->map(function ($dx) {
        $line = $dx->diagnosis_name;
        if ($dx->diagnosis_code) {
            $line .= ' ('.$dx->diagnosis_code.')';
        }
        if ($dx->remarks) {
            $line .= ' — '.$dx->remarks;
        }

        return $line;
    })->implode("\n");

    $medicationText = $prescriptions->map(function ($rx) {
        $parts = array_filter([
            $rx->medicine_name,
            $rx->dosage,
            $rx->frequency,
            $rx->duration,
            $rx->quantity ? 'Qty '.$rx->quantity : null,
        ]);

        return implode(' · ', $parts);
    })->implode("\n");

    $labFindings = $labRequests->map(function ($lab) {
        $line = $lab->lab_test_name;
        if ($lab->results) {
            $line .= ': '.$lab->results;
        } elseif ($lab->notes) {
            $line .= ' — '.$lab->notes;
        }

        return $line;
    })->implode("\n");

    $labTests = $labRequests->pluck('lab_test_name')->implode("\n");

    $consultationTypes = [
        'General', 'Prenatal', 'Dental Care', 'Child Care', 'Child Nutrition', 'Injury', 'Adult Immunization',
        'Family Planning', 'Postpartum', 'Tuberculosis', 'Child Immunization', 'Sick Children', 'Firecracker Injury',
    ];
@endphp

<section class="iclinic-form bg-white text-black" aria-label="Individual Treatment Record">
    @include('consultations.handout.partials._doh-header', [
        'formTitle' => 'INDIVIDUAL TREATMENT RECORD',
        'serialDigits' => 12,
        'patient' => $patient,
    ])

    {{-- Section I: Patient Information (summary) --}}
    <div class="bg-gray-500 text-white font-bold text-[9px] px-1 py-0.5 border border-black border-b-0 uppercase tracking-wide">
        I. Patient Information (Impormasyon ng Pasyente)
    </div>

    <div class="border border-black text-[9px] leading-tight">
        <div class="grid grid-cols-12 border-b border-black">
            <div class="col-span-6 border-r border-black p-0.5">
                <p class="font-bold">Last Name <span class="font-normal">(Apelyido)</span></p>
                <p class="min-h-[14px] text-[10px] font-semibold">{{ $patient->last_name ?? '' }}</p>
            </div>
            <div class="col-span-4 border-r border-black p-0.5">
                <p class="font-bold">Suffix <span class="font-normal">(e.g. Jr., Sr., II, III)</span></p>
                <p class="min-h-[14px] text-[10px]">{{ $patient->suffix ?? '' }}</p>
            </div>
            <div class="col-span-2 p-0.5">
                <p class="font-bold">Age <span class="font-normal">(Edad)</span></p>
                <p class="min-h-[14px] text-[10px] font-semibold">{{ $age ?? '' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-12 border-b border-black">
            <div class="col-span-5 border-r border-black p-0.5">
                <p class="font-bold">First Name <span class="font-normal">(Pangalan)</span></p>
                <p class="min-h-[14px] text-[10px] font-semibold">{{ $patient->first_name ?? '' }}</p>
            </div>
            <div class="col-span-7 p-0.5">
                <p class="font-bold">Residential Address <span class="font-normal">(Tirahan)</span></p>
                <p class="min-h-[14px] text-[10px]">{{ $patient->residential_address ?? '' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-12">
            <div class="col-span-12 p-0.5">
                <p class="font-bold">Middle Name <span class="font-normal">(Gitnang Pangalan)</span></p>
                <p class="min-h-[14px] text-[10px]">{{ $patient->middle_name ?? '' }}</p>
            </div>
        </div>
    </div>

    {{-- Section II: CHU/RHU Personnel --}}
    <div class="bg-gray-500 text-white font-bold text-[9px] px-1 py-0.5 border border-black border-b-0 border-t-0 uppercase tracking-wide">
        II. For CHU / RHU Personnel Only (Para sa Kinatawan ng CHU / RHU Lamang)
    </div>

    <div class="border border-black text-[9px] leading-tight">
        <div class="grid grid-cols-12">
            {{-- Left: transaction + vitals --}}
            <div class="col-span-5 border-r border-black">
                <div class="grid grid-cols-12 border-b border-black">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold leading-tight">
                        Mode of<br>Transaction
                    </div>
                    <div class="col-span-7 p-0.5 space-y-0.5">
                        @include('consultations.handout.partials._mark', ['checked' => $mode === 'Walk-in', 'label' => 'Walk-in', 'inline' => false])
                        @include('consultations.handout.partials._mark', ['checked' => $mode === 'Visited', 'label' => 'Visited', 'inline' => false])
                        @include('consultations.handout.partials._mark', ['checked' => $mode === 'Referral', 'label' => 'Referral', 'inline' => false])
                    </div>
                </div>

                <div class="grid grid-cols-12 border-b border-black">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold">Date of Consultation</div>
                    <div class="col-span-7 p-0.5 text-[10px] font-semibold">{{ $consultDate->format('m/d/Y') }} <span class="font-normal text-[8px]">(mm/dd/yyyy)</span></div>
                </div>

                <div class="grid grid-cols-12 border-b border-black" x-data="{ period: '{{ $isAm ? 'AM' : 'PM' }}' }">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold">Consultation Time</div>
                    <div class="col-span-7 p-0.5 flex items-center gap-1 flex-wrap">
                        <span class="text-[10px] font-semibold">{{ $consultDate->format('g:i') }}</span>
                        <span class="inline-flex border border-black text-[8px]">
                            <button type="button" class="px-1 py-0" :class="period === 'AM' ? 'bg-gray-300 font-bold' : ''" @click="period = 'AM'">AM</button>
                            <span class="border-l border-black px-0.5">/</span>
                            <button type="button" class="px-1 py-0" :class="period === 'PM' ? 'bg-gray-300 font-bold' : ''" @click="period = 'PM'">PM</button>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-12 border-b border-black">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold">Blood Pressure</div>
                    <div class="col-span-3 border-r border-black p-0.5 text-[10px]">{{ $bp }}</div>
                    <div class="col-span-2 bg-gray-300 border-r border-black p-0.5 font-bold">Temperature</div>
                    <div class="col-span-2 p-0.5 text-[10px]">{{ $temperature !== null ? $temperature.'°C' : '' }}</div>
                </div>

                <div class="grid grid-cols-12 border-b border-black">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold">Height (cm)</div>
                    <div class="col-span-3 border-r border-black p-0.5 text-[10px]">{{ $height }}</div>
                    <div class="col-span-2 bg-gray-300 border-r border-black p-0.5 font-bold">Weight (kg)</div>
                    <div class="col-span-2 p-0.5 text-[10px]">{{ $weight }}</div>
                </div>

                <div class="grid grid-cols-12">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold leading-tight">Name of Attending Provider</div>
                    <div class="col-span-7 p-0.5 text-[10px] font-semibold">{{ $provider }}</div>
                </div>
            </div>

            {{-- Right: referral block --}}
            <div class="col-span-3 border-r border-black">
                <div class="bg-gray-300 font-bold text-center text-[8px] py-0.5 border-b border-black">
                    For REFERRAL Transaction only
                </div>
                <div class="border-b border-black p-0.5 min-h-[28px]">
                    <p class="font-bold text-[8px]">REFERRED FROM</p>
                    <p class="text-[9px]">{{ $isReferral ? ($consultation->referred_from ?? '') : '' }}</p>
                </div>
                <div class="border-b border-black p-0.5 min-h-[28px]">
                    <p class="font-bold text-[8px]">REFERRED TO</p>
                    <p class="text-[9px]">{{ $isReferral ? ($consultation->referred_to ?? 'Higher facility') : '' }}</p>
                </div>
                <div class="border-b border-black p-0.5 min-h-[56px]">
                    <p class="font-bold text-[8px]">Reason(s) for Referral</p>
                    <p class="text-[9px] whitespace-pre-wrap">{{ $isReferral ? ($consultation->referral_reason ?? '') : '' }}</p>
                </div>
                <div class="p-0.5 min-h-[24px]">
                    <p class="font-bold text-[8px]">Referred by</p>
                    <p class="text-[9px]">{{ $isReferral ? $provider : '' }}</p>
                </div>
            </div>

            {{-- Chief complaints --}}
            <div class="col-span-4 flex flex-col min-h-[168px]">
                <div class="bg-gray-300 border-b border-black p-0.5 font-bold text-[8px] text-center uppercase tracking-wide">
                    Chief Complaints
                </div>
                <div class="flex-1 p-1 text-[9px] whitespace-pre-wrap">{{ $consultation->complaint_text ?? '' }}</div>
            </div>
        </div>

        {{-- Nature of visit + consultation type --}}
        <div class="grid grid-cols-12 border-t border-black">
            <div class="col-span-4 border-r border-black">
                <div class="grid grid-cols-12 border-b border-black">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold leading-tight">Nature of Visit</div>
                    <div class="col-span-7 p-0.5 space-y-0.5">
                        @include('consultations.handout.partials._mark', ['checked' => in_array($natureOfVisit, ['Checkup', 'New Consultation/Case'], true), 'label' => 'New Consultation/Case', 'inline' => false])
                        @include('consultations.handout.partials._mark', ['checked' => $natureOfVisit === 'New Admission', 'label' => 'New Admission', 'inline' => false])
                        @include('consultations.handout.partials._mark', ['checked' => in_array($natureOfVisit, ['Follow-up', 'Follow-up Visit', 'Follow-up visit'], true), 'label' => 'Follow-up visit', 'inline' => false])
                    </div>
                </div>
                <div class="grid grid-cols-12">
                    <div class="col-span-5 bg-gray-300 border-r border-black p-0.5 font-bold leading-tight text-[8px]">
                        Type of Consultation /<br>Purpose of visit
                    </div>
                    <div class="col-span-7 p-0.5">
                        <div class="grid grid-cols-2 gap-x-0.5">
                            @foreach ($consultationTypes as $type)
                                @include('consultations.handout.partials._mark', [
                                    'checked' => false,
                                    'label' => $type,
                                    'inline' => false,
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Diagnosis --}}
            <div class="col-span-8 flex min-h-[72px]">
                <div class="bg-gray-300 border-r border-black p-0.5 font-bold text-[8px] w-16 shrink-0 flex items-start pt-1">
                    Diagnosis:
                </div>
                <div class="flex-1 p-1 text-[9px] whitespace-pre-wrap">{{ $diagnosisText ?: '—' }}</div>
            </div>
        </div>

        {{-- Medication / Treatment --}}
        <div class="grid grid-cols-12 border-t border-black min-h-[80px]">
            <div class="col-span-8 border-r border-black flex">
                <div class="bg-gray-300 border-r border-black p-0.5 font-bold text-[8px] w-16 shrink-0 flex items-start pt-1 leading-tight">
                    Medication /<br>Treatment:
                </div>
                <div class="flex-1 p-1 text-[9px] whitespace-pre-wrap">{{ $medicationText ?: ($consultation->notes ?? '—') }}</div>
            </div>
            <div class="col-span-4 flex flex-col">
                <div class="bg-gray-300 border-b border-black p-0.5 font-bold text-[8px] text-center">
                    Name of Health Care Provider:
                </div>
                <div class="flex-1 p-1 text-[9px] font-semibold">{{ $provider }}</div>
            </div>
        </div>

        {{-- Laboratory --}}
        <div class="grid grid-cols-12 border-t border-black min-h-[72px]">
            <div class="col-span-8 border-r border-black flex">
                <div class="bg-gray-300 border-r border-black p-0.5 font-bold text-[8px] w-16 shrink-0 flex items-start pt-1 leading-tight">
                    Laboratory<br>Findings /<br>Impression:
                </div>
                <div class="flex-1 p-1 text-[9px] whitespace-pre-wrap">{{ $labFindings ?: '—' }}</div>
            </div>
            <div class="col-span-4 flex flex-col">
                <div class="bg-gray-300 border-b border-black p-0.5 font-bold text-[8px] text-center leading-tight">
                    Performed Laboratory Test:
                </div>
                <div class="flex-1 p-1 text-[9px] whitespace-pre-wrap">{{ $labTests ?: '—' }}</div>
            </div>
        </div>
    </div>

    <div class="border border-black border-t-0 bg-gray-400 text-[8px] px-1 py-0.5 flex justify-between font-semibold">
        <span>Clinic Information System</span>
        <span>| FORM 2 |</span>
        <span>Page 1</span>
    </div>
</section>
