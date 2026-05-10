@php
    $tempAlert = (float) ($latestVitals?->temperature_c ?? 0) > 37.5;
    $bpAlert = ((int) ($latestVitals?->bp_systolic ?? 0) > 140) || ((int) ($latestVitals?->bp_diastolic ?? 0) > 90);
@endphp

<section class="sticky top-0 z-40 rounded-xl border bg-emerald-50 px-4 py-3 shadow-sm" style="border-color: var(--border);">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-3 lg:items-center">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-900">Patient Context</p>
            <p class="font-display text-lg font-semibold" style="color: var(--ink);">
                {{ $patient->last_name }}, {{ $patient->first_name }}
            </p>
            <p class="text-xs" style="color: var(--ink-muted);">
                {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} · {{ $patient->sex }} ·
                PhilHealth {{ ($patient->is_philhealth_member ?? 'n') === 'y' ? 'Member' : 'Non-member' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-900">Chief Complaint</p>
            <p class="text-sm italic" style="color: var(--ink-muted);">
                {{ $consultation->complaint_text ?? 'No complaint recorded' }}
            </p>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-900">Alert Vitals</p>
            <p class="text-sm font-semibold {{ $bpAlert ? 'text-red-600' : 'text-emerald-900' }}">
                BP {{ $latestVitals?->bp_systolic ?? '—' }}/{{ $latestVitals?->bp_diastolic ?? '—' }}
            </p>
            <p class="text-sm font-semibold {{ $tempAlert ? 'text-red-600' : 'text-emerald-900' }}">
                Temp {{ $latestVitals?->temperature_c ?? '—' }}°C
            </p>
        </div>
    </div>
</section>
