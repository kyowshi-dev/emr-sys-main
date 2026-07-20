<div class="rounded-xl border px-3.5 py-3 mb-5 text-xs leading-relaxed" style="border-color: var(--border); background: var(--teal-soft); color: var(--primary);">
    <i class="fa-solid fa-eye mr-1.5" aria-hidden="true"></i>
    Preview the referral summary below. Tap any field to edit inline — changes sync back to Step 1 automatically.
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">
    <div class="space-y-4">
        <div class="outward-referral-preview-field rounded-md border p-3.5 transition-colors focus-within:ring-2" style="border-color: var(--border); background: var(--bg-surface); --tw-ring-color: var(--primary);">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Destination facility</span>
                <span class="text-[10px] font-medium inline-flex items-center gap-1" style="color: var(--ink-subtle);">
                    <i class="fa-solid fa-pen text-[9px]" aria-hidden="true"></i> Tap to edit
                </span>
            </div>
            <div class="relative">
                <input
                    type="text"
                    id="outward_preview_referred_to"
                    data-preview-field="referred_to"
                    data-preview-source="outward_referred_to"
                    list="outwardReferralDestinationsPreview"
                    autocomplete="off"
                    class="w-full rounded-md border-0 bg-transparent px-0 py-1 text-sm font-medium focus:outline-none focus:ring-0 border-b border-transparent focus:border-[var(--border)]"
                    style="color: var(--ink);"
                >
                <datalist id="outwardReferralDestinationsPreview">
                    @foreach ($destinationFacilities as $facility)
                        <option value="{{ $facility }}"></option>
                    @endforeach
                </datalist>
            </div>
        </div>

        <div class="outward-referral-preview-field rounded-md border p-3.5 transition-colors focus-within:ring-2" style="border-color: var(--border); background: var(--bg-surface); --tw-ring-color: var(--primary);">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Reasons for referral</span>
                <span class="text-[10px] font-medium inline-flex items-center gap-1" style="color: var(--ink-subtle);">
                    <i class="fa-solid fa-pen text-[9px]" aria-hidden="true"></i> Tap to edit
                </span>
            </div>
            <div id="outward_preview_reasons_empty" class="hidden text-sm italic py-1" style="color: var(--ink-subtle);">No reasons selected</div>
            <div class="space-y-2" id="outward_preview_reasons_list">
                @foreach ($referralReasonOptions as $value => $label)
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input
                            type="checkbox"
                            data-preview-field="referral_reasons"
                            data-preview-source="referral_reasons"
                            data-reason-value="{{ $value }}"
                            value="{{ $value }}"
                            class="mt-0.5 h-4 w-4 shrink-0 rounded border"
                            style="border-color: var(--border); accent-color: var(--primary);"
                        >
                        <span class="text-sm leading-snug" style="color: var(--ink);">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="outward-referral-preview-field rounded-md border p-3.5 transition-colors focus-within:ring-2" style="border-color: var(--border); background: var(--bg-surface); --tw-ring-color: var(--primary);">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Specific details</span>
                <span class="text-[10px] font-medium inline-flex items-center gap-1" style="color: var(--ink-subtle);">
                    <i class="fa-solid fa-pen text-[9px]" aria-hidden="true"></i> Tap to edit
                </span>
            </div>
            <textarea
                id="outward_preview_referral_reason_details"
                data-preview-field="referral_reason_details"
                data-preview-source="outward_referral_reason_details"
                rows="3"
                placeholder="No additional details provided."
                class="w-full rounded-md border-0 bg-transparent px-0 py-1 text-sm leading-relaxed resize-y min-h-[4rem] focus:outline-none focus:ring-0 border-b border-transparent focus:border-[var(--border)]"
                style="color: var(--ink);"
            ></textarea>
        </div>
    </div>

    <div class="space-y-4">
        <div class="outward-referral-preview-field rounded-md border p-3.5 transition-colors focus-within:ring-2" style="border-color: var(--border); background: var(--bg-surface); --tw-ring-color: var(--primary);">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Pertinent history of illness</span>
                <span class="text-[10px] font-medium inline-flex items-center gap-1" style="color: var(--ink-subtle);">
                    <i class="fa-solid fa-pen text-[9px]" aria-hidden="true"></i> Tap to edit
                </span>
            </div>
            <textarea
                id="outward_preview_pertinent_history"
                data-preview-field="pertinent_history"
                data-preview-source="outward_pertinent_history"
                rows="4"
                placeholder="No history recorded."
                class="w-full rounded-md border-0 bg-transparent px-0 py-1 text-sm leading-relaxed resize-y min-h-[5.25rem] focus:outline-none focus:ring-0 border-b border-transparent focus:border-[var(--border)]"
                style="color: var(--ink);"
            ></textarea>
        </div>

        <div class="outward-referral-preview-field rounded-md border p-3.5 transition-colors focus-within:ring-2" style="border-color: var(--border); background: var(--bg-surface); --tw-ring-color: var(--primary);">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Actions taken</span>
                <span class="text-[10px] font-medium inline-flex items-center gap-1" style="color: var(--ink-subtle);">
                    <i class="fa-solid fa-pen text-[9px]" aria-hidden="true"></i> Tap to edit
                </span>
            </div>
            <textarea
                id="outward_preview_actions_taken"
                data-preview-field="actions_taken"
                data-preview-source="outward_actions_taken"
                rows="4"
                placeholder="No actions recorded."
                class="w-full rounded-md border-0 bg-transparent px-0 py-1 text-sm leading-relaxed resize-y min-h-[5.25rem] focus:outline-none focus:ring-0 border-b border-transparent focus:border-[var(--border)]"
                style="color: var(--ink);"
            ></textarea>
        </div>
    </div>
</div>
