<div class="p-4 lg:p-6">
    <div class="flex items-start justify-between gap-3 mb-4 lg:mb-5">
        <div>
            <h2 class="font-display font-semibold text-lg lg:text-xl" style="color: var(--ink);">New Consultation</h2>
            <p class="text-xs lg:text-sm mt-1" style="color: var(--ink-muted);">
                Attending to <span class="font-semibold" style="color: var(--ink);">{{ $patient->last_name }}, {{ $patient->first_name }}@if($patient->suffix) {{ $patient->suffix }}@endif</span>
                (PT{{ str_pad($patient->id, 3, '0', STR_PAD_LEFT) }})
            </p>
            <p class="text-xs lg:text-sm mt-0.5" style="color: var(--ink-muted);">{{ $patient->age }} y/o · {{ $patient->residential_address }}</p>
        </div>
        <button type="button" onclick="closeConsultationCreateModal()" class="shrink-0 p-2 rounded-lg transition-colors hover:bg-black/[0.04]" style="color: var(--ink-muted);" aria-label="Close">
            <i class="fa-solid fa-times" aria-hidden="true"></i>
        </button>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('consultations.store', $patient->id) }}" method="POST" class="space-y-4 lg:space-y-5">
        @csrf
        <input type="hidden" name="modal_patient_id" value="{{ $patient->id }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5">
            <div class="rounded-xl border p-4 lg:p-5" style="background: var(--bg-surface); border-color: var(--border);">
                <h3 class="font-semibold mb-3 lg:mb-4 pb-2 border-b text-sm lg:text-base" style="color: var(--ink); border-color: var(--border);">1. Visit details</h3>

                <div class="mb-3 lg:mb-4">
                    <label for="mode_of_transaction" class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Mode of transaction <span style="color: #b91c1c;">*</span></label>
                    <select name="mode_of_transaction" id="mode_of_transaction" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);" required>
                        <option value="Walk-in" @selected(old('mode_of_transaction') === 'Walk-in')>Walk-in</option>
                        <option value="Visited" @selected(old('mode_of_transaction') === 'Visited')>Visited</option>
                        <option value="Referral" @selected(old('mode_of_transaction') === 'Referral')>Referral</option>
                    </select>
                </div>

                <div id="referred_from_container" class="mb-3 lg:mb-4" style="display: none;">
                    <label for="referred_from" class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Referred from</label>
                    <input type="text" name="referred_from" id="referred_from" value="{{ old('referred_from') }}" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);" placeholder="e.g. Rural Health Unit, Private Clinic">
                </div>

                <div class="mb-3 lg:mb-4">
                    <label for="nature_of_visit" class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Nature of visit <span style="color: #b91c1c;">*</span></label>
                    <select name="nature_of_visit" id="nature_of_visit" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);" required>
                        <option value="Checkup" @selected(old('nature_of_visit') === 'New Consultation/Case')>New Consultation/Case</option>
                        <option value="Follow-up" @selected(old('nature_of_visit') === 'Follow-up Visit')>Follow-up Visit</option>
                    </select>
                </div>

                <div>
                    <label for="chief_complaint" class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Chief Complaints</label>
                    <textarea name="chief_complaint" id="chief_complaint" rows="3" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);" placeholder="e.g. Fever 3 days, cough">{{ old('chief_complaint') }}</textarea>
                </div>
            </div>

            <div class="rounded-xl border p-4 lg:p-5" style="background: var(--bg-surface); border-color: var(--border);">
                <h3 class="font-semibold mb-3 lg:mb-4 pb-2 border-b text-sm lg:text-base" style="color: var(--ink); border-color: var(--border);">2. Triage Baseline Vitals</h3>

                <div class="grid grid-cols-2 gap-3 lg:gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Blood pressure (mmHg)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="bp_systolic" value="{{ old('bp_systolic') }}" min="0" max="300" placeholder="120" class="w-full px-3 lg:px-4 py-2 rounded-lg border text-center text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                            <span style="color: var(--ink-subtle);">/</span>
                            <input type="number" name="bp_diastolic" value="{{ old('bp_diastolic') }}" min="0" max="200" placeholder="80" class="w-full px-3 lg:px-4 py-2 rounded-lg border text-center text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                        </div>
                    </div>

                    <div>
                        <label for="temperature" class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Temperature (°C) <span style="color: #b91c1c;">*</span></label>
                        <input type="number" step="0.1" name="temperature" id="temperature" value="{{ old('temperature') }}" min="30" max="45" placeholder="36.5" class="w-full px-3 lg:px-4 py-2 rounded-lg border text-center text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);" required>
                    </div>

                    <div>
                        <label for="weight" class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight" id="weight" value="{{ old('weight') }}" min="0" max="500" placeholder="—" class="w-full px-3 lg:px-4 py-2 rounded-lg border text-center text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                    </div>

                    <div>
                        <label for="height" class="block text-xs lg:text-sm font-medium mb-1" style="color: var(--ink-muted);">Height (cm)</label>
                        <input type="number" step="0.1" name="height" id="height" value="{{ old('height') }}" min="0" max="300" placeholder="—" class="w-full px-3 lg:px-4 py-2 rounded-lg border text-center text-sm focus:outline-none focus:ring-2" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 lg:gap-3 pt-1">
            <button type="button" onclick="closeConsultationCreateModal()" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl border font-medium text-xs lg:text-sm transition-colors hover:bg-black/[0.03]" style="border-color: var(--border); color: var(--ink-muted);">Cancel</button>
            <button type="submit" class="px-5 lg:px-6 py-2 lg:py-2.5 rounded-xl text-white font-semibold text-xs lg:text-sm transition hover:opacity-95" style="background: var(--primary); box-shadow: var(--shadow-sm);">
                Save & submit for validation
            </button>
        </div>
    </form>
</div>
