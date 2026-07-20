<div class="grid grid-cols-1 md:grid-cols-2 gap-5 lg:gap-6">
    {{-- Left column --}}
    <div class="space-y-5">
        <div>
            <label for="outward_referred_to" class="block text-xs font-medium mb-1.5" style="color: var(--ink-muted);">
                Destination facility <span style="color: #b91c1c;">*</span>
            </label>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs" style="color: var(--ink-subtle);" aria-hidden="true"></i>
                <input
                    type="text"
                    name="referred_to"
                    id="outward_referred_to"
                    list="outwardReferralDestinations"
                    autocomplete="off"
                    placeholder="Search or select receiving facility…"
                    class="w-full rounded-md border py-2.5 pl-9 pr-3 text-sm focus:outline-none focus:ring-2"
                    style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);"
                    required
                >
                <datalist id="outwardReferralDestinations">
                    @foreach ($destinationFacilities as $facility)
                        <option value="{{ $facility }}"></option>
                    @endforeach
                </datalist>
            </div>
            <p class="mt-1.5 text-[11px] leading-relaxed" style="color: var(--ink-subtle);">
                Select the RHU, district hospital, or higher-level facility that will receive this referral.
            </p>
        </div>

        <fieldset>
            <legend class="block text-xs font-medium mb-2" style="color: var(--ink-muted);">
                Reasons for referral <span style="color: #b91c1c;">*</span>
            </legend>
            <div class="space-y-2.5 rounded-md border p-3.5" style="border-color: var(--border); background: var(--bg-surface);">
                @foreach ($referralReasonOptions as $value => $label)
                    <label class="flex items-start gap-2.5 cursor-pointer group">
                        <input
                            type="checkbox"
                            name="referral_reasons[]"
                            value="{{ $value }}"
                            class="mt-0.5 h-4 w-4 shrink-0 rounded border"
                            style="border-color: var(--border); accent-color: var(--primary);"
                        >
                        <span class="text-sm leading-snug group-hover:opacity-90" style="color: var(--ink);">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <div class="mt-3">
                <label for="outward_referral_reason_details" class="block text-xs font-medium mb-1.5" style="color: var(--ink-muted);">
                    Specific details
                </label>
                <textarea
                    name="referral_reason_details"
                    id="outward_referral_reason_details"
                    rows="3"
                    placeholder="Add clinical justification, urgency level, or other DOH-required notes…"
                    class="w-full rounded-md border px-3 py-2.5 text-sm focus:outline-none focus:ring-2 resize-y min-h-[5rem]"
                    style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);"
                ></textarea>
            </div>
        </fieldset>
    </div>

    {{-- Right column --}}
    <div class="space-y-5 flex flex-col min-h-0">
        <div class="flex flex-col h-full min-h-0">
            <label for="outward_pertinent_history" class="block text-xs font-medium mb-1.5" style="color: var(--ink-muted);">
                Pertinent history of illness <span style="color: #b91c1c;">*</span>
            </label>
            <textarea
                name="pertinent_history"
                id="outward_pertinent_history"
                rows="1"
                placeholder="e.g., Patient presented with high-grade fever (39°C) for 4 days, persistent dry cough, and loss of appetite. No rashes observed."
                class="w-full flex-1 rounded-md border px-3 py-2.5 text-sm leading-relaxed focus:outline-none focus:ring-2 resize-none min-h-0"
                style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);"
                required
            ></textarea>
            <p class="mt-1.5 text-[11px] leading-relaxed" style="color: var(--ink-subtle);">
                Document onset, duration, progression of symptoms, and relevant negatives or positives.
            </p>
        </div>

        <div class="flex flex-col h-full min-h-0">
            <label for="outward_actions_taken" class="block text-xs font-medium mb-1.5" style="color: var(--ink-muted);">
                Actions taken
            </label>
            <textarea
                name="actions_taken"
                id="outward_actions_taken"
                rows="6"
                placeholder="e.g., Administered Paracetamol 500mg, cold compress applied, vitals monitored, and hydration encouraged."
                class="w-full flex-1 rounded-md border px-3 py-2.5 text-sm leading-relaxed focus:outline-none focus:ring-2 resize-none min-h-0"
                style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);"
            ></textarea>
            <p class="mt-1.5 text-[11px] leading-relaxed" style="color: var(--ink-subtle);">
                Record first aid, nursing care, medicines given, and monitoring done before referral.
            </p>
        </div>
    </div>
</div>
