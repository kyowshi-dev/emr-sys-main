@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6 animate-in opacity-0 pb-24" x-data="{ showRetakeVitals: false, showReferralFields: false }">
    @if (session('success'))
        <div class="rounded-xl border px-4 py-3 text-sm" style="background: var(--teal-soft); border-color: var(--border); color: var(--primary);">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="rounded-xl border px-4 py-3 text-sm" style="background: var(--accent-soft); border-color: var(--border); color: var(--accent);">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border bg-gray-100 p-4 lg:p-5" style="border-color: var(--border); box-shadow: var(--shadow-sm);">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="font-display text-2xl font-semibold lg:text-3xl" style="color: var(--ink);">Consultation Details</h1>
            <div class="text-right text-xs lg:text-sm" style="color: var(--ink-muted);">
                Consultation #{{ $consultation->id }}<br>
                {{ \Carbon\Carbon::parse($consultation->created_at)->format('M j, Y g:i A') }}
            </div>
        </div>
        <div class="mt-2 flex flex-wrap items-center gap-3">
            @php
                $statusLabel = ucfirst(str_replace('_', ' ', $consultation->status));
                $statusStyle = match ($consultation->status) {
                    'completed' => 'background: var(--teal-soft); color: var(--primary);',
                    'referred' => 'background: var(--accent-soft); color: var(--accent);',
                    default => 'background: rgba(0,0,0,0.06); color: var(--ink-muted);',
                };
            @endphp
            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" style="{{ $statusStyle }}">{{ $statusLabel }}</span>
            <a href="{{ route('patients.show', $patient->id) }}" class="text-xs font-medium text-emerald-900 hover:underline lg:text-sm">Back to patient</a>
            <a href="{{ route('consultations.index') }}" class="text-xs font-medium text-emerald-900 hover:underline lg:text-sm">History</a>
            @if (in_array($consultation->status, ['completed', 'referred'], true) && auth()->user()->canPrintHandout())
                <a href="{{ route('consultations.handout', $consultation->id) }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition hover:opacity-90"
                   style="background: var(--primary);">
                    <i class="fa-solid fa-print" aria-hidden="true"></i> Print handout
                </a>
            @endif
        </div>
        @if ($consultation->status === 'pending_validation' && ($canAcknowledgeIntake ?? false))
            <div class="mt-3 rounded-xl border px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                 style="background: var(--accent-soft); border-color: var(--border);">
                <div>
                    <p class="text-sm font-semibold" style="color: var(--ink);">Awaiting nurse intake validation</p>
                    <p class="text-xs mt-0.5" style="color: var(--ink-muted);">Confirm triage details before sending this case to the doctor queue.</p>
                </div>
                <form action="{{ route('consultations.acknowledge-intake', $consultation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-xl px-4 py-2 text-sm font-semibold text-white transition hover:opacity-90" style="background: var(--accent);">
                        Acknowledge intake
                    </button>
                </form>
            </div>
        @elseif (in_array($consultation->status, ['triage', 'pending_validation'], true))
            <div class="mt-3 rounded-xl border px-4 py-3" style="background: rgba(0,0,0,0.03); border-color: var(--border);">
                <p class="text-sm font-semibold" style="color: var(--ink);">
                    @if ($consultation->status === 'triage')
                        Triage intake in progress
                    @else
                        Awaiting nurse intake validation
                    @endif
                </p>
                <p class="text-xs mt-0.5" style="color: var(--ink-muted);">Clinical review opens after nurse acknowledgment and doctor queue routing.</p>
            </div>
        @endif
    </div>

    @include('consultations.partials.patient-ribbon', [
        'patient' => $patient,
        'consultation' => $consultation,
        'latestVitals' => $latestVitals,
    ])

    @php
        $clinicalReviewOpen = in_array($consultation->status, ['pending_doctor', 'in_progress'], true);
    @endphp

    <main class="space-y-4">
        <section class="rounded-xl border bg-gray-100 p-4 lg:p-5" style="border-color: var(--border);">
            <details>
                <summary class="list-none cursor-pointer">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h3 class="font-display text-lg font-semibold" style="color: var(--ink);">Vitals</h3>
                            <p class="text-xs" style="color: var(--ink-muted);">
                                Latest Reading: BP {{ $latestVitals?->bp_systolic ?? '—' }}/{{ $latestVitals?->bp_diastolic ?? '—' }} ·
                                Temp {{ $latestVitals?->temperature_c ?? '—' }}°C ·
                                Wt {{ $latestVitals?->weight_kg ?? '—' }}kg ·
                                Ht {{ $latestVitals?->height_cm ?? '—' }}cm
                            </p>
                        </div>
                        <span class="rounded-full bg-emerald-900 px-2 py-1 text-xs text-white">{{ $allVitals->count() }} version{{ $allVitals->count() !== 1 ? 's' : '' }}</span>
                    </div>
                </summary>

                <div class="mt-4 overflow-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-emerald-900/10" style="color: var(--ink-muted);">
                                <th class="px-2 py-2 text-left">Captured</th>
                                <th class="px-2 py-2 text-left">Phase</th>
                                <th class="px-2 py-2 text-left">BP</th>
                                <th class="px-2 py-2 text-left">Temp</th>
                                <th class="px-2 py-2 text-left">By</th>
                                <th class="px-2 py-2 text-left">Notes</th>
                                <th class="px-2 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allVitals as $vitalVersion)
                                <tr class="border-b" style="border-color: var(--border); color: var(--ink);">
                                    <td class="px-2 py-2">{{ \Carbon\Carbon::parse($vitalVersion->created_at)->format('M j g:i A') }}</td>
                                    <td class="px-2 py-2 uppercase">{{ $vitalVersion->phase ?? 'triage' }}</td>
                                    <td class="px-2 py-2">{{ $vitalVersion->bp_systolic ?? '—' }}/{{ $vitalVersion->bp_diastolic ?? '—' }}</td>
                                    <td class="px-2 py-2">{{ $vitalVersion->temperature_c ?? '—' }}°C</td>
                                    <td class="px-2 py-2">{{ trim(($vitalVersion->captured_by_first_name ?? '').' '.($vitalVersion->captured_by_last_name ?? '')) ?: 'N/A' }}</td>
                                    <td class="px-2 py-2" style="color: var(--ink-muted);">{{ $vitalVersion->notes ?? '—' }}</td>
                                    <td class="px-2 py-2">
                                        <div class="flex items-center gap-2">
                                            <details>
                                                <summary class="cursor-pointer text-[11px] font-semibold text-emerald-900 hover:underline">Edit</summary>
                                                <div class="mt-2 w-[18rem] rounded-lg border bg-white p-2" style="border-color: var(--border);">
                                                    <form action="{{ route('consultations.vitals.update', ['consultationId' => $consultation->id, 'vitalId' => $vitalVersion->id]) }}" method="POST" class="space-y-2">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <input type="number" name="bp_systolic" min="0" max="300" step="1" value="{{ $vitalVersion->bp_systolic }}" placeholder="SYS" class="rounded border px-2 py-1 text-[11px] focus:outline-none focus:ring-2 focus:ring-emerald-900/30">
                                                            <input type="number" name="bp_diastolic" min="0" max="200" step="1" value="{{ $vitalVersion->bp_diastolic }}" placeholder="DIA" class="rounded border px-2 py-1 text-[11px] focus:outline-none focus:ring-2 focus:ring-emerald-900/30">
                                                            <input type="number" name="temperature" min="30" max="45" step="0.1" value="{{ $vitalVersion->temperature_c }}" placeholder="Temp" class="rounded border px-2 py-1 text-[11px] focus:outline-none focus:ring-2 focus:ring-emerald-900/30">
                                                            <input type="number" name="weight" min="0" max="500" step="0.1" value="{{ $vitalVersion->weight_kg }}" placeholder="Weight" class="rounded border px-2 py-1 text-[11px] focus:outline-none focus:ring-2 focus:ring-emerald-900/30">
                                                            <input type="number" name="height" min="0" max="300" step="0.1" value="{{ $vitalVersion->height_cm }}" placeholder="Height" class="col-span-2 rounded border px-2 py-1 text-[11px] focus:outline-none focus:ring-2 focus:ring-emerald-900/30">
                                                        </div>
                                                        <textarea name="notes" rows="2" placeholder="Notes" class="w-full rounded border px-2 py-1 text-[11px] focus:outline-none focus:ring-2 focus:ring-emerald-900/30">{{ $vitalVersion->notes ?? '' }}</textarea>
                                                        <button type="submit" class="w-full rounded bg-emerald-900 px-2 py-1 text-[11px] font-semibold text-white">Update</button>
                                                    </form>
                                                </div>
                                            </details>
                                            <form action="{{ route('consultations.vitals.delete', ['consultationId' => $consultation->id, 'vitalId' => $vitalVersion->id]) }}" method="POST" onsubmit="return confirm('Delete this vitals version? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-[11px] font-semibold text-red-600 hover:underline disabled:cursor-not-allowed disabled:opacity-50" @if (($vitalVersion->phase ?? null) === 'triage' || $allVitals->count() <= 1) disabled @endif>Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 border-t pt-4" style="border-color: var(--border);">
                    @if ($clinicalReviewOpen)
                    <button type="button" @click="showRetakeVitals = !showRetakeVitals" class="rounded-lg bg-emerald-900 px-3 py-2 text-xs font-semibold text-white">
                        <span x-show="!showRetakeVitals">Re-take vitals</span>
                        <span x-show="showRetakeVitals" style="display: none;">Hide re-take form</span>
                    </button>

                    <form x-show="showRetakeVitals" x-transition style="display: none;" action="{{ route('consultations.vitals.retake', $consultation->id) }}" method="POST" class="mt-3 space-y-2">
                        @csrf
                        <div class="grid grid-cols-2 gap-2 md:grid-cols-5">
                            <input type="number" name="bp_systolic" min="0" max="300" step="1" placeholder="SYS" class="rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);">
                            <input type="number" name="bp_diastolic" min="0" max="200" step="1" placeholder="DIA" class="rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);">
                            <input type="number" name="temperature" min="30" max="45" step="0.1" placeholder="Temp °C" class="rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);">
                            <input type="number" name="weight" min="0" max="500" step="0.1" placeholder="Weight kg" class="rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);">
                            <input type="number" name="height" min="0" max="300" step="0.1" placeholder="Height cm" class="rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);">
                        </div>
                        <textarea name="notes" rows="2" placeholder="Optional notes" class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);"></textarea>
                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl bg-emerald-900 px-4 py-2 text-sm font-semibold text-white">Save new vitals version</button>
                        </div>
                    </form>
                    @else
                    <p class="text-xs" style="color: var(--ink-muted);">Clinical vitals retake is available once the case is in the doctor queue.</p>
                    @endif
                </div>
            </details>
        </section>

        <section class="rounded-xl border bg-gray-100 p-4 lg:p-5" style="border-color: var(--border);">
            <div class="flex items-center justify-between gap-2">
                <h3 class="font-display font-semibold text-lg" style="color: var(--ink);">Medical Diagnosis</h3>
                @if(isset($diagnoses) && $diagnoses->count() > 0)
                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-900 text-white">{{ $diagnoses->count() }} saved</span>
                @endif
            </div>

            @if(isset($diagnoses) && $diagnoses->count() > 0)
                <div class="mt-3 space-y-2">
                    @foreach($diagnoses as $d)
                        <div class="rounded-lg border p-2 text-sm flex items-center justify-between gap-2" style="border-color: var(--border);">
                            <div>
                                @if ($d->diagnosis_code)
                                    <span class="font-semibold" style="color: var(--ink);">{{ $d->diagnosis_code }}</span>
                                    <span style="color: var(--ink-muted);">- {{ $d->diagnosis_name }}</span>
                                @else
                                    <span class="font-semibold" style="color: var(--ink);">{{ $d->diagnosis_name }}</span>
                                @endif
                                @if ($d->is_custom)
                                    <span class="ml-1 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide" style="background: var(--accent-soft); color: var(--accent);">Custom</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm" style="color: var(--ink-muted);">No diagnosis entries yet for this consultation.</p>
            @endif

            @if ($clinicalReviewOpen)
            <form action="{{ route('consultations.diagnosis', $consultation->id) }}" method="POST" x-data="diagnosisSearch()" @set-diagnosis-query.window="setQuery($event.detail.query)" class="space-y-4 mt-4 pt-4 border-t" style="border-color: var(--border);">
                @csrf
                <div class="relative">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <label class="block text-xs font-medium" style="color: var(--ink-muted);">Search ICD-10 / Disease name</label>
                        <button type="button" @click="toggleCustom()" class="text-xs font-medium hover:underline" style="color: var(--primary);" x-text="useCustom ? 'Search master list' : 'Diagnosis Not Found?'"></button>
                    </div>
                    <template x-if="!useCustom">
                        <div>
                            <input type="text" x-model="query" @input.debounce.300ms="search()" placeholder="e.g. Dengue, Hypertension..." class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);" autocomplete="off">
                            <input type="hidden" name="diagnosis_id" :value="selectedId">
                            <div x-show="results.length > 0" class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-lg border bg-white" style="display:none; border-color: var(--border);">
                                <ul>
                                    <template x-for="item in results" :key="item.id">
                                        <li @click="select(item)" class="px-3 py-2 cursor-pointer text-sm border-b hover:bg-gray-50" style="border-color: var(--border); color: var(--ink);" x-text="item.text"></li>
                                    </template>
                                </ul>
                            </div>
                            <div x-show="query.length >= 2 && results.length === 0 && !searching" class="mt-2 rounded-lg border px-3 py-2 text-xs" style="display:none; border-color: var(--border); background: var(--accent-soft); color: var(--ink);">
                                <span>Not in the master list?</span>
                                <button type="button" @click="enableCustomFromQuery()" class="ml-1 font-semibold hover:underline" style="color: var(--accent);">Use "<span x-text="query"></span>" as custom diagnosis</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="useCustom">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="md:col-span-1">
                                <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">ICD code (optional)</label>
                                <input type="text" name="custom_diagnosis_code" x-model="customCode" placeholder="e.g. J06.9" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Diagnosis name</label>
                                <input type="text" name="custom_diagnosis_name" x-model="customName" placeholder="Enter diagnosis name" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);" required>
                            </div>
                        </div>
                    </template>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Remarks</label>
                    <textarea name="remarks" rows="2" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);"></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="!canSubmitDiagnosis" class="rounded-xl bg-emerald-900 px-5 py-2 text-sm font-semibold text-white disabled:opacity-50">Add diagnosis</button>
                </div>
            </form>
            @else
            <p class="mt-4 pt-4 border-t text-sm" style="border-color: var(--border); color: var(--ink-muted);">Diagnosis entry opens after nurse validation routes this case to the doctor queue.</p>
            @endif
        </section>

        <section class="rounded-xl border bg-gray-100 p-4 lg:p-5" style="border-color: var(--border);">
            <div class="flex items-center justify-between gap-2">
                <h3 class="font-display font-semibold text-lg" style="color: var(--ink);">Prescription (Rx)</h3>
                @if(isset($prescriptions) && $prescriptions->count() > 0)
                    <span class="text-xs px-2 py-1 rounded-full bg-emerald-900 text-white">{{ $prescriptions->count() }} saved</span>
                @endif
            </div>

            @if(isset($prescriptions) && $prescriptions->count() > 0)
                <div class="mt-3 overflow-auto border rounded-lg" style="border-color: var(--border);">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-emerald-900/10" style="color: var(--ink-muted);">
                                <th class="text-left px-3 py-2">Medicine</th>
                                <th class="text-left px-3 py-2">Dosage/Frequency</th>
                                <th class="text-left px-3 py-2">Duration</th>
                                <th class="text-left px-3 py-2">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prescriptions as $rx)
                                <tr class="border-b" style="border-color: var(--border); color: var(--ink);">
                                    <td class="px-3 py-2">
                                        {{ $rx->medicine_name }}
                                        @if ($rx->is_custom)
                                            <span class="ml-1 rounded-full px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide" style="background: var(--accent-soft); color: var(--accent);">Custom</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">{{ $rx->dosage }}{{ $rx->frequency ? ' · '.$rx->frequency : '' }}</td>
                                    <td class="px-3 py-2">{{ $rx->duration ?? '—' }}</td>
                                    <td class="px-3 py-2">{{ $rx->quantity ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="mt-3 text-sm" style="color: var(--ink-muted);">No prescription entries yet for this consultation.</p>
            @endif

            @if ($clinicalReviewOpen)
            <form action="{{ route('consultations.prescription', $consultation->id) }}" method="POST" x-data="medicineSearch()" class="space-y-4 mt-4 pt-4 border-t" style="border-color: var(--border);">
                @csrf
                <div class="relative">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <label class="block text-xs font-medium" style="color: var(--ink-muted);">Medicine search</label>
                        <button type="button" @click="toggleCustom()" class="text-xs font-medium hover:underline" style="color: var(--primary);" x-text="useCustom ? 'Search master list' : 'Enter custom medicine'"></button>
                    </div>
                    <template x-if="!useCustom">
                        <div>
                            <input type="text" x-model="query" @input.debounce.300ms="search()" placeholder="e.g. Paracetamol, Amoxicillin..." class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);" autocomplete="off">
                            <input type="hidden" name="medicine_id" :value="selectedId">
                            <div x-show="results.length > 0" class="absolute z-10 w-full mt-1 rounded-lg border max-h-48 overflow-y-auto bg-white" style="display:none; border-color: var(--border);">
                                <ul>
                                    <template x-for="item in results" :key="item.id">
                                        <li @click="select(item)" class="px-3 py-2 cursor-pointer text-sm border-b hover:bg-gray-50" style="border-color: var(--border); color: var(--ink);" x-text="item.text"></li>
                                    </template>
                                </ul>
                            </div>
                            <div x-show="query.length >= 2 && results.length === 0 && !searching" class="mt-2 rounded-lg border px-3 py-2 text-xs" style="display:none; border-color: var(--border); background: var(--accent-soft); color: var(--ink);">
                                <span>Not in the master list?</span>
                                <button type="button" @click="enableCustomFromQuery()" class="ml-1 font-semibold hover:underline" style="color: var(--accent);">Use "<span x-text="query"></span>" as custom medicine</button>
                            </div>
                        </div>
                    </template>
                    <template x-if="useCustom">
                        <div>
                            <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Medicine name</label>
                            <input type="text" name="custom_medicine_name" x-model="customName" placeholder="Enter medicine name and strength" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);" required>
                        </div>
                    </template>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Dosage</label>
                        <input id="rx_dosage" type="text" name="dosage" value="{{ old('dosage') }}" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Frequency</label>
                        <input id="rx_frequency" type="text" name="frequency" value="{{ old('frequency') }}" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Duration</label>
                        <input type="text" name="duration" value="{{ old('duration') }}" placeholder="e.g. 7 days" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30 transition" style="border-color: var(--border); color: var(--ink);">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-2" style="color: var(--ink-muted);">Smart Sig</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="appendSig('rx_frequency', 'Before meals')" class="px-3 py-1 rounded-full text-xs bg-emerald-900/10 text-emerald-900">Before Meals</button>
                        <button type="button" onclick="appendSig('rx_frequency', 'After meals')" class="px-3 py-1 rounded-full text-xs bg-emerald-900/10 text-emerald-900">After Meals</button>
                        <button type="button" onclick="appendSig('rx_frequency', 'At bedtime')" class="px-3 py-1 rounded-full text-xs bg-emerald-900/10 text-emerald-900">At Bedtime</button>
                        <button type="button" onclick="appendSig('rx_dosage', '1 tab')" class="px-3 py-1 rounded-full text-xs bg-emerald-900/10 text-emerald-900">1 tab</button>
                        <button type="button" onclick="appendSig('rx_dosage', '3x a day')" class="px-3 py-1 rounded-full text-xs bg-emerald-900/10 text-emerald-900">3x/day</button>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" :disabled="!canSubmitPrescription" class="rounded-xl bg-emerald-900 px-5 py-2 text-sm font-semibold text-white disabled:opacity-50">Add prescription</button>
                </div>
            </form>
            @else
            <p class="mt-4 pt-4 border-t text-sm" style="border-color: var(--border); color: var(--ink-muted);">Prescription entry opens after nurse validation routes this case to the doctor queue.</p>
            @endif
        </section>

        @if ($clinicalReviewOpen)
        <section class="rounded-xl border bg-gray-100 p-4 lg:p-5" style="border-color: var(--border);">
            <h3 class="font-display text-lg font-semibold" style="color: var(--ink);">Final Disposition</h3>
            <p class="mt-1 text-xs" style="color: var(--ink-muted);">Use this section to decide referral before ending the consultation session.</p>

            <form id="finalizeForm" action="{{ route('consultations.finalize', $consultation->id) }}" method="POST" class="mt-4 space-y-3">
                @csrf
                @if ($canReferExternally)
                    <label class="inline-flex items-center gap-2 text-sm" style="color: var(--ink-muted);">
                        <input type="checkbox" name="refer_to_higher_facility" value="1" @change="showReferralFields = $event.target.checked" class="rounded border-gray-300">
                        Refer to higher facility
                    </label>
                    <div x-show="showReferralFields" x-transition style="display: none;" class="space-y-2">
                        <input type="text" name="referred_to" value="{{ old('referred_to') }}" placeholder="Referred facility" class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);">
                        <textarea name="referral_reason" rows="2" placeholder="Reason for referral" class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/30" style="border-color: var(--border);">{{ old('referral_reason') }}</textarea>
                    </div>
                @else
                    <p class="text-xs" style="color: var(--ink-muted);">Only Nurse and Doctor roles can trigger external referral.</p>
                @endif
            </form>
        </section>
        @endif
    </main>

    @if ($clinicalReviewOpen && ! in_array($consultation->status, ['completed', 'referred'], true))
        <div class="fixed bottom-0 left-0 right-0 z-40 border-t bg-white/95 px-4 py-3 backdrop-blur" style="border-color: var(--border);">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-3">
                <p class="text-xs" style="color: var(--ink-muted);">
                    @if (($diagnoses->count() ?? 0) > 0 && ($prescriptions->count() ?? 0) > 0)
                        Diagnosis and prescription recorded. Finalize to close this visit, or add more entries above.
                    @else
                        Add at least one diagnosis before finalizing. Prescription is optional but recommended when medicines are given.
                    @endif
                </p>
                <button type="submit" form="finalizeForm" class="rounded-xl bg-emerald-900 px-5 py-2 text-sm font-semibold text-white">
                    Finalize &amp; Save Consultation
                </button>
            </div>
        </div>
    @endif
</div>

<script>
    function diagnosisSearch() {
        return {
            query: '',
            results: [],
            selectedId: null,
            useCustom: false,
            customName: '',
            customCode: '',
            searching: false,
            get canSubmitDiagnosis() {
                if (this.useCustom) {
                    return this.customName.trim().length >= 2;
                }
                return Boolean(this.selectedId);
            },
            async search() {
                if (this.useCustom) {
                    return;
                }
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }
                this.searching = true;
                const response = await fetch('/search/diagnoses?query=' + encodeURIComponent(this.query));
                this.results = await response.json();
                this.searching = false;
            },
            select(item) {
                this.useCustom = false;
                this.query = item.text;
                this.selectedId = item.id;
                this.customName = '';
                this.customCode = '';
                this.results = [];
            },
            setQuery(term) {
                this.useCustom = false;
                this.query = term;
                this.selectedId = null;
                this.search();
            },
            toggleCustom() {
                this.useCustom = ! this.useCustom;
                this.results = [];
                this.selectedId = null;
                if (! this.useCustom) {
                    this.customName = '';
                    this.customCode = '';
                }
            },
            enableCustomFromQuery() {
                this.useCustom = true;
                this.customName = this.query.trim();
                this.selectedId = null;
                this.results = [];
            },
        };
    }
    function medicineSearch() {
        return {
            query: '',
            results: [],
            selectedId: null,
            useCustom: false,
            customName: '',
            searching: false,
            get canSubmitPrescription() {
                if (this.useCustom) {
                    return this.customName.trim().length >= 2;
                }
                return Boolean(this.selectedId);
            },
            async search() {
                if (this.useCustom) {
                    return;
                }
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }
                this.searching = true;
                const response = await fetch('/search/medicines?query=' + encodeURIComponent(this.query));
                this.results = await response.json();
                this.searching = false;
            },
            select(item) {
                this.useCustom = false;
                this.query = item.text;
                this.selectedId = item.id;
                this.customName = '';
                this.results = [];
            },
            toggleCustom() {
                this.useCustom = ! this.useCustom;
                this.results = [];
                this.selectedId = null;
                if (! this.useCustom) {
                    this.customName = '';
                }
            },
            enableCustomFromQuery() {
                this.useCustom = true;
                this.customName = this.query.trim();
                this.selectedId = null;
                this.results = [];
            },
        };
    }
    function appendSig(targetId, value) {
        const input = document.getElementById(targetId);
        if (! input) {
            return;
        }
        const current = input.value.trim();
        input.value = current ? `${current}; ${value}` : value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }
</script>
@endsection
