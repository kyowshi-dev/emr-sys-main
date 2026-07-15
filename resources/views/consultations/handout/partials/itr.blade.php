{{--
    Individual Treatment Record (ITR) Section — iClinicSys FORM 2
--}}
@php
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

<section class="iclinic-form" aria-label="Individual Treatment Record">
    @include('consultations.handout.partials._doh-header', [
        'formTitle' => 'INDIVIDUAL TREATMENT RECORD',
        'serialDigits' => 12,
        'patient' => $patient,
    ])

    <table class="form-table" style="border-top:0;">
        <tr>
            <td colspan="12" class="section-header">I. Patient Information (Impormasyon ng Pasyente)</td>
        </tr>
        <tr>
            <td colspan="6">
                <p class="field-label">Last Name <span class="field-help">(Apelyido)</span></p>
                <p class="field-value text-bold">{{ $patient->last_name ?? '' }}</p>
            </td>
            <td colspan="4">
                <p class="field-label">Suffix <span class="field-help">(e.g. Jr., Sr., II, III)</span></p>
                <p class="field-value">{{ $patient->suffix ?? '' }}</p>
            </td>
            <td colspan="2">
                <p class="field-label">Age <span class="field-help">(Edad)</span></p>
                <p class="field-value text-bold">{{ $age ?? '' }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <p class="field-label">First Name <span class="field-help">(Pangalan)</span></p>
                <p class="field-value text-bold">{{ $patient->first_name ?? '' }}</p>
            </td>
            <td colspan="7">
                <p class="field-label">Residential Address <span class="field-help">(Tirahan)</span></p>
                <p class="field-value">{{ $patient->residential_address ?? '' }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <p class="field-label">Middle Name <span class="field-help">(Gitnang Pangalan)</span></p>
                <p class="field-value">{{ $patient->middle_name ?? '' }}</p>
            </td>
        </tr>

        <tr>
            <td colspan="12" class="section-header" style="border-top:0;">II. For CHU / RHU Personnel Only (Para sa Kinatawan ng CHU / RHU Lamang)</td>
        </tr>
        <tr>
            {{-- Left: transaction + vitals (5/12) --}}
            <td colspan="5" style="padding:0; vertical-align:top;">
                <table class="form-table nested-table" style="border:0;">
                    <tr>
                        <td class="label-cell" style="width:42%; border-top:0; border-left:0;">Mode of<br>Transaction</td>
                        <td style="border-top:0; border-right:0;">
                            <div class="marks-stack">
                                @include('consultations.handout.partials._mark', ['checked' => $mode === 'Walk-in', 'label' => 'Walk-in', 'inline' => false])
                                @include('consultations.handout.partials._mark', ['checked' => $mode === 'Visited', 'label' => 'Visited', 'inline' => false])
                                @include('consultations.handout.partials._mark', ['checked' => $mode === 'Referral', 'label' => 'Referral', 'inline' => false])
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="border-left:0;">Date of Consultation</td>
                        <td style="border-right:0;">
                            <span class="field-value">{{ $consultDate->format('m/d/Y') }}</span>
                            <span class="field-help">(mm/dd/yyyy)</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="border-left:0;">Consultation Time</td>
                        <td style="border-right:0;">
                            <span class="field-value">{{ $consultDate->format('g:i') }}</span>
                            <span class="am-pm-box">
                                <span @class(['active' => $isAm])>AM</span>
                                <span class="sep">/</span>
                                <span @class(['active' => ! $isAm])>PM</span>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="border-left:0;">Blood Pressure</td>
                        <td style="border-right:0; padding:0;">
                            <table class="form-table nested-table" style="border:0;">
                                <tr>
                                    <td style="width:50%; border-top:0; border-left:0; border-bottom:0;">{{ $bp }}</td>
                                    <td class="label-cell" style="width:28%;">Temperature</td>
                                    <td style="border-top:0; border-right:0; border-bottom:0;">{{ $temperature !== null ? $temperature.'°C' : '' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="border-left:0;">Height (cm)</td>
                        <td style="border-right:0; padding:0;">
                            <table class="form-table nested-table" style="border:0;">
                                <tr>
                                    <td style="width:50%; border-top:0; border-left:0; border-bottom:0;">{{ $height }}</td>
                                    <td class="label-cell" style="width:28%;">Weight (kg)</td>
                                    <td style="border-top:0; border-right:0; border-bottom:0;">{{ $weight }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="border-left:0; border-bottom:0;">Name of Attending Provider</td>
                        <td class="field-value text-bold" style="border-right:0; border-bottom:0;">{{ $provider }}</td>
                    </tr>
                </table>
            </td>

            {{-- Middle: referral block (3/12) --}}
            <td colspan="3" style="padding:0; vertical-align:top;">
                <table class="form-table nested-table" style="border:0; height:100%;">
                    <tr>
                        <td class="label-cell-sm text-center" style="border-top:0;">For REFERRAL Transaction only</td>
                    </tr>
                    <tr>
                        <td style="min-height:24px;">
                            <p class="field-label" style="font-size:7pt;">REFERRED FROM</p>
                            <p class="field-value-sm">{{ $isReferral ? ($consultation->referred_from ?? '') : '' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="min-height:24px;">
                            <p class="field-label" style="font-size:7pt;">REFERRED TO</p>
                            <p class="field-value-sm">{{ $isReferral ? ($consultation->referred_to ?? 'Higher facility') : '' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="min-height:48px;">
                            <p class="field-label" style="font-size:7pt;">Reason(s) for Referral</p>
                            <p class="field-value-sm whitespace-pre">{{ $isReferral ? ($consultation->referral_reason ?? '') : '' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="min-height:20px; border-bottom:0;">
                            <p class="field-label" style="font-size:7pt;">Referred by</p>
                            <p class="field-value-sm">{{ $isReferral ? $provider : '' }}</p>
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Right: nature of visit + consultation type (4/12) --}}
            <td colspan="4" style="padding:0; vertical-align:top;">
                <table class="form-table nested-table" style="border:0; height:100%;">
                    <tr>
                        <td class="label-cell" style="width:38%; border-top:0; border-right:0;">Nature of Visit</td>
                        <td style="border-top:0; border-right:0;">
                            <div class="marks-stack">
                                @include('consultations.handout.partials._mark', ['checked' => in_array($natureOfVisit, ['Checkup', 'New Consultation/Case'], true), 'label' => 'New Consultation/Case', 'inline' => false])
                                @include('consultations.handout.partials._mark', ['checked' => $natureOfVisit === 'New Admission', 'label' => 'New Admission', 'inline' => false])
                                @include('consultations.handout.partials._mark', ['checked' => in_array($natureOfVisit, ['Follow-up', 'Follow-up Visit', 'Follow-up visit'], true), 'label' => 'Follow-up visit', 'inline' => false])
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell" style="border-right:0; font-size:7pt;">Type of Consultation /<br>Purpose of visit</td>
                        <td style="border-right:0;">
                            <div class="marks-2col">
                                @foreach ($consultationTypes as $type)
                                    @include('consultations.handout.partials._mark', [
                                        'checked' => false,
                                        'label' => $type,
                                        'inline' => false,
                                    ])
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="border-bottom:0; border-right:0; min-height:56px; vertical-align:top;">
                            <p class="field-label" style="font-size:7pt;">Chief Complaints</p>
                            <p class="field-value-sm whitespace-pre">{{ $consultation->complaint_text ?? '' }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="8" style="padding:0; min-height:56px;">
                <table class="form-table nested-table" style="border:0; height:100%;">
                    <tr>
                        <td class="label-cell" style="width:14%; border-top:0; border-left:0; border-bottom:0; font-size:7pt;">Diagnosis:</td>
                        <td class="field-value-sm whitespace-pre" style="border-top:0; border-right:0; border-bottom:0;">{{ $diagnosisText ?: '—' }}</td>
                    </tr>
                </table>
            </td>
            <td colspan="4" style="border-bottom:0;">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="8" style="padding:0; min-height:64px;">
                <table class="form-table nested-table" style="border:0; height:100%;">
                    <tr>
                        <td class="label-cell" style="width:14%; border-top:0; border-left:0; border-bottom:0; font-size:7pt;">Medication /<br>Treatment:</td>
                        <td class="field-value-sm whitespace-pre" style="border-top:0; border-right:0; border-bottom:0;">{{ $medicationText ?: ($consultation->notes ?? '—') }}</td>
                    </tr>
                </table>
            </td>
            <td colspan="4" style="padding:0; vertical-align:top;">
                <table class="form-table nested-table" style="border:0; height:100%;">
                    <tr>
                        <td class="label-cell-sm text-center" style="border-top:0; border-right:0; font-size:7pt;">Name of Health Care Provider:</td>
                    </tr>
                    <tr>
                        <td class="field-value-sm text-bold" style="border-right:0; border-bottom:0;">{{ $provider }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="8" style="padding:0; min-height:56px;">
                <table class="form-table nested-table" style="border:0; height:100%;">
                    <tr>
                        <td class="label-cell" style="width:14%; border-top:0; border-left:0; border-bottom:0; font-size:7pt;">Laboratory<br>Findings /<br>Impression:</td>
                        <td class="field-value-sm whitespace-pre" style="border-top:0; border-right:0; border-bottom:0;">{{ $labFindings ?: '—' }}</td>
                    </tr>
                </table>
            </td>
            <td colspan="4" style="padding:0; vertical-align:top;">
                <table class="form-table nested-table" style="border:0; height:100%;">
                    <tr>
                        <td class="label-cell-sm text-center" style="border-top:0; border-right:0; font-size:7pt;">Performed Laboratory Test:</td>
                    </tr>
                    <tr>
                        <td class="field-value-sm whitespace-pre" style="border-right:0; border-bottom:0;">{{ $labTests ?: '—' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="form-footer form-footer-flex">
        <span>Clinic Information System</span>
        <span>| FORM 2 |</span>
        <span>Page 1</span>
    </div>
</section>
