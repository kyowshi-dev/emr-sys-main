<div class="mb-5 lg:mb-6" aria-live="polite">
    <div class="flex items-center justify-between gap-3 mb-2">
        <p id="outwardReferralWizardStepLabel" class="text-xs lg:text-sm font-semibold" style="color: var(--ink);">Step 1 of 3</p>
        <p id="outwardReferralWizardStepName" class="text-xs" style="color: var(--ink-muted);">Referral Details</p>
    </div>
    <div class="h-1.5 rounded-full overflow-hidden" style="background: var(--teal-soft);">
        <div id="outwardReferralWizardProgressBar" class="h-full rounded-full transition-all duration-300 ease-out" style="width: 33%; background: var(--primary);"></div>
    </div>
</div>

<form id="outwardReferralWizardForm" class="space-y-4 lg:space-y-5" novalidate>
    <input type="hidden" name="patient_id" id="outwardReferralWizardPatientId" value="{{ $patient->id }}">

    <div id="outwardReferralWizardStep1" data-wizard-step="1" class="outward-referral-wizard-step" aria-hidden="false">
        @include('referrals.partials.outward-wizard-step1', [
            'destinationFacilities' => $destinationFacilities ?? [],
            'referralReasonOptions' => $referralReasonOptions ?? [],
        ])
    </div>

    <div id="outwardReferralWizardStep2" data-wizard-step="2" class="outward-referral-wizard-step hidden" aria-hidden="true">
        @include('referrals.partials.outward-wizard-step2', [
            'destinationFacilities' => $destinationFacilities ?? [],
            'referralReasonOptions' => $referralReasonOptions ?? [],
        ])
    </div>

    <div id="outwardReferralWizardStep3" data-wizard-step="3" class="outward-referral-wizard-step hidden" aria-hidden="true">
        @include('referrals.partials.outward-wizard-step3')
    </div>

    <div class="sticky bottom-0 flex flex-wrap items-center justify-between gap-2 lg:gap-3 pt-1 border-t" style="border-color: var(--border); background: white; z-index: 10;">
        <button type="button" id="outwardReferralWizardBackBtn" onclick="outwardReferralWizardGoBack()" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl border font-medium text-xs lg:text-sm transition-colors hover:bg-black/[0.03]" style="border-color: var(--border); color: var(--ink-muted);">
            <i class="fa-solid fa-arrow-left mr-1.5" aria-hidden="true"></i> Back
        </button>
        <div class="flex flex-wrap items-center gap-2 lg:gap-3">
            <button type="button" onclick="closeOutwardReferralWizard()" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl border font-medium text-xs lg:text-sm transition-colors hover:bg-black/[0.03]" style="border-color: var(--border); color: var(--ink-muted);">Cancel</button>
            <button type="button" id="outwardReferralWizardNextBtn" onclick="outwardReferralWizardGoNext()" class="px-5 lg:px-6 py-2 lg:py-2.5 rounded-xl text-white font-semibold text-xs lg:text-sm transition hover:opacity-95" style="background: var(--primary); box-shadow: var(--shadow-sm);">
                Next
            </button>
        </div>
    </div>
</form>
