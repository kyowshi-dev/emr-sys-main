<div class="rounded-xl border px-4 py-4 mb-5 text-sm leading-relaxed" style="border-color: var(--border); background: var(--teal-soft); color: var(--primary);">
    <i class="fa-solid fa-circle-check mr-1.5" aria-hidden="true"></i>
    Please review the referral summary below. Once confirmed, the consultation and referral will be saved.
</div>

<div class="rounded-xl border overflow-hidden" style="border-color: var(--border); background: var(--bg-surface);">
    <div class="px-4 py-3 border-b" style="border-color: var(--border); background: var(--bg-surface-elevated);">
        <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Patient</p>
        <p id="outward_confirm_patient_name" class="mt-1 font-semibold text-base" style="color: var(--ink);">—</p>
        <p id="outward_confirm_patient_meta" class="text-xs mt-0.5" style="color: var(--ink-muted);">—</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-0 md:divide-x" style="border-color: var(--border);">
        <div class="p-4 space-y-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Destination facility</p>
                <p id="outward_confirm_referred_to" class="mt-1 text-sm font-medium" style="color: var(--ink);">—</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Reasons for referral</p>
                <ul id="outward_confirm_reasons" class="mt-2 space-y-1 text-sm list-disc list-inside" style="color: var(--ink);"></ul>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Specific details</p>
                <p id="outward_confirm_reason_details" class="mt-1 text-sm whitespace-pre-line" style="color: var(--ink);">—</p>
            </div>
        </div>
        <div class="p-4 space-y-4">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Pertinent history</p>
                <p id="outward_confirm_pertinent_history" class="mt-1 text-sm whitespace-pre-line" style="color: var(--ink);">—</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Actions taken</p>
                <p id="outward_confirm_actions_taken" class="mt-1 text-sm whitespace-pre-line" style="color: var(--ink);">—</p>
            </div>
            <div class="rounded-lg border px-3 py-2.5 text-xs" style="border-color: var(--border); background: var(--bg-surface-elevated); color: var(--ink-muted);">
                <p class="font-semibold mb-1" style="color: var(--ink);">Vitals captured</p>
                <p id="outward_confirm_vitals" class="leading-relaxed">—</p>
            </div>
        </div>
    </div>
</div>

<p class="mt-4 text-xs leading-relaxed" style="color: var(--ink-muted);">
    After confirmation, you will be prompted to print the referral slip for the patient.
</p>
