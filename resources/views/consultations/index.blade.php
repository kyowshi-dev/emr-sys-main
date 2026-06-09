@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6 animate-in opacity-0"
     x-data="{ blurSensitive: false, advancedOpen: @json(request()->filled('date_from') || request()->filled('date_to') || (request()->filled('sort') && request('sort') !== 'newest')) }">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">
                {{ ($showQueue ?? false) ? 'Clinical queue' : 'Consultation history' }}
            </h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">
                @if ($showQueue ?? false)
                    Patients awaiting validation or doctor review. Filter by urgency or zone.
                @else
                    Review timelines, diagnoses, and treatments with patient-safe visibility controls.
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 lg:gap-4">
        <div class="rounded-xl border p-4 lg:p-5 animate-in opacity-0 delay-1" style="background: var(--bg-surface); border-color: var(--border);">
            <p class="text-xs uppercase tracking-wide font-semibold" style="color: var(--ink-muted);">Total consultations</p>
            <p class="mt-2 font-display text-2xl lg:text-3xl font-semibold" style="color: var(--ink);">{{ $totalConsultations }}</p>
            <p class="text-xs mt-1" style="color: var(--ink-subtle);">All recorded patient encounters.</p>
        </div>
        <div class="rounded-xl border p-4 lg:p-5 animate-in opacity-0 delay-2" style="background: var(--bg-surface); border-color: var(--border);">
            <p class="text-xs uppercase tracking-wide font-semibold" style="color: var(--ink-muted);">This week</p>
            <p class="mt-2 font-display text-2xl lg:text-3xl font-semibold" style="color: var(--ink);">{{ $thisWeekCount }}</p>
            <p class="text-xs mt-1" style="color: var(--ink-subtle);">Recent consultations logged this week.</p>
        </div>
        <div class="rounded-xl border p-4 lg:p-5 animate-in opacity-0 delay-3" style="background: var(--bg-surface); border-color: var(--border);">
            <p class="text-xs uppercase tracking-wide font-semibold" style="color: var(--ink-muted);">Completed</p>
            <p class="mt-2 font-display text-2xl lg:text-3xl font-semibold" style="color: var(--primary);">{{ $completedCount }}</p>
            <p class="text-xs mt-1" style="color: var(--ink-subtle);">Consultations with finalized outcomes.</p>
        </div>
    </div>

    <div class="sticky top-0 z-20 rounded-xl border p-4 lg:p-5 space-y-4 animate-in opacity-0 delay-4"
         style="background: var(--bg-surface-elevated); border-color: var(--border); box-shadow: var(--shadow-md);">
        <form method="GET" action="{{ route('consultations.index') }}" id="consultations-filter-form" class="space-y-4">
            @if ($showQueue ?? false)
                <input type="hidden" name="queue" value="1">
            @endif
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-display font-semibold text-lg" style="color: var(--ink);">Filter consultations</h2>
                <div class="flex flex-wrap items-center gap-3 text-xs font-semibold">
                    @if ($isClinicalRole ?? false)
                        <a href="{{ route('consultations.index', ['queue' => 1]) }}" class="transition hover:underline {{ ($showQueue ?? false) ? '' : 'opacity-70' }}" style="color: var(--primary);">Queue</a>
                        <a href="{{ route('consultations.index') }}" class="transition hover:underline {{ ($showQueue ?? false) ? 'opacity-70' : '' }}" style="color: var(--primary);">Full history</a>
                    @endif
                    <a href="{{ ($showQueue ?? false) ? route('consultations.index', ['queue' => 1]) : route('consultations.index') }}" class="transition hover:underline" style="color: var(--primary);">Reset filters</a>
                </div>
            </div>

            <div class="flex flex-col xl:flex-row xl:flex-wrap xl:items-end gap-4">
                <div class="flex-1 min-w-0 xl:min-w-[280px] xl:max-w-xl">
                    <label for="query" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Search patient or diagnosis</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1 min-w-0">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none" style="color: var(--ink-subtle);">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </span>
                            <input type="text" id="query" name="query" value="{{ request('query') }}"
                                   placeholder="Search by patient, ID, or diagnosis..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 transition"
                                   style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                        </div>
                        <button type="submit" class="shrink-0 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition hover:opacity-90" style="background: var(--primary);" title="Search">Search</button>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 sm:gap-4 shrink-0">
                    <div class="inline-flex items-center gap-3 px-3 py-2 rounded-xl border" style="background: var(--bg-surface); border-color: var(--border);">
                        <span class="text-xs font-semibold uppercase tracking-wide whitespace-nowrap" style="color: var(--ink-muted);">Sensitive data</span>
                        <button type="button"
                                @click="blurSensitive = !blurSensitive"
                                :aria-pressed="blurSensitive.toString()"
                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2"
                                :style="blurSensitive ? 'background: var(--accent); --tw-ring-color: var(--accent);' : 'background: var(--border); --tw-ring-color: var(--primary);'">
                            <span class="sr-only">Toggle masking for diagnosis and treatment</span>
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform duration-200"
                                  :class="blurSensitive ? 'translate-x-5' : 'translate-x-0.5'"></span>
                        </button>
                        <span class="text-xs whitespace-nowrap" :style="blurSensitive ? 'color: var(--accent);' : 'color: var(--ink-muted);'" x-text="blurSensitive ? 'Masked' : 'Visible'"></span>
                    </div>

                    <button type="button"
                            @click="advancedOpen = !advancedOpen"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold transition hover:bg-black/[0.03]"
                            style="border-color: var(--border); color: var(--ink);"
                            :aria-expanded="advancedOpen.toString()">
                        <span>Advanced filters</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': advancedOpen }" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div x-show="advancedOpen"
                 x-collapse
                 class="overflow-hidden"
                 style="display: none;">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 pt-2 border-t" style="border-color: var(--border);">
                    <div>
                        <label for="date_from" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">From</label>
                        <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="dd/mm/yyyy"
                               class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                    </div>
                    <div>
                        <label for="date_to" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">To</label>
                        <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="dd/mm/yyyy"
                               class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                    </div>
                    @if ($showQueue ?? false)
                        <div>
                            <label for="urgency" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Urgency</label>
                            <select id="urgency" name="urgency" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                                <option value="">All</option>
                                <option value="critical" @selected(request('urgency') === 'critical')>Critical vitals only</option>
                            </select>
                        </div>
                        <div>
                            <label for="zone_id" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Zone</label>
                            <select id="zone_id" name="zone_id" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                                <option value="">All zones</option>
                                @foreach ($zones ?? [] as $zone)
                                    <option value="{{ $zone->id }}" @selected((string) request('zone_id') === (string) $zone->id)>Zone {{ $zone->zone_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Status</label>
                            <select id="status" name="status" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                                <option value="">All in queue</option>
                                <option value="pending_validation" @selected(request('status') === 'pending_validation')>Pending validation</option>
                                <option value="pending_doctor" @selected(request('status') === 'pending_doctor')>Pending doctor</option>
                                <option value="in_progress" @selected(request('status') === 'in_progress')>In progress</option>
                                <option value="triage" @selected(request('status') === 'triage')>Triage</option>
                            </select>
                        </div>
                    @endif
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="sort" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Sort by</label>
                        <select id="sort" name="sort" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                            <option value="newest" @selected($currentSort === 'newest')>Newest First</option>
                            <option value="oldest" @selected($currentSort === 'oldest')>Oldest First</option>
                            <option value="patient_name" @selected($currentSort === 'patient_name')>Patient Name (A-Z)</option>
                            <option value="status" @selected($currentSort === 'status')>Status</option>
                        </select>
                    </div>
                    <div class="flex items-end sm:col-span-2 lg:col-span-1">
                        <button type="submit" class="w-full px-4 py-2 rounded-xl text-white text-sm font-semibold transition hover:opacity-90" style="background: var(--primary);">Apply</button>
                    </div>
                </div>
            </div>

            @if (request()->filled('query') || request()->filled('date_from') || request()->filled('date_to') || request()->filled('urgency') || request()->filled('zone_id') || request()->filled('status') || (request()->filled('sort') && request('sort') !== 'newest'))
                <div class="pt-3 border-t flex flex-wrap items-center gap-2" style="border-color: var(--border);">
                    <span class="text-xs font-medium" style="color: var(--ink-muted);">Active filters:</span>
                    @if (request('query'))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: var(--teal-soft); color: var(--primary);">Search: {{ request('query') }}</span>
                    @endif
                    @if (request('date_from'))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: var(--teal-soft); color: var(--primary);">From: {{ request('date_from') }}</span>
                    @endif
                    @if (request('date_to'))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: var(--teal-soft); color: var(--primary);">To: {{ request('date_to') }}</span>
                    @endif
                    @if (request('urgency') === 'critical')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Critical vitals</span>
                    @endif
                    @if (request('zone_id'))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: var(--teal-soft); color: var(--primary);">Zone filter active</span>
                    @endif
                    @if (request('status'))
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: var(--teal-soft); color: var(--primary);">Status: {{ str_replace('_', ' ', request('status')) }}</span>
                    @endif
                    @if (request()->filled('sort') && request('sort') !== 'newest')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: var(--teal-soft); color: var(--primary);">Sort: {{ str_replace('_', ' ', request('sort')) }}</span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <div>
        <h2 class="font-display font-semibold text-lg" style="color: var(--ink);">Consultation timeline</h2>
    </div>

    <div class="space-y-3 lg:space-y-4 animate-in opacity-0 delay-5 min-h-[12rem]">
        @forelse ($consultations as $consultation)
            @php
                $diagnoses = $diagnosisByConsultation[$consultation->id] ?? [];
                $treatments = $treatmentByConsultation[$consultation->id] ?? [];
                $latestVitals = $latestVitalsByConsultation[$consultation->id] ?? null;
                $isCritical = \App\Support\VitalsThresholds::isCritical($latestVitals);
            @endphp
            <div class="rounded-xl border p-4 lg:p-5 transition-all duration-200 hover:shadow-md hover:scale-[1.005]" style="background: var(--bg-surface); border-color: var(--border);">
                <div class="flex flex-col gap-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1 lg:mb-2">
                                <span class="font-semibold text-sm lg:text-base" :class="{ 'blur-sensitive': blurSensitive }" style="color: var(--ink);">
                                    {{ $consultation->patient_last_name }}, {{ $consultation->patient_first_name }}
                                    <span class="font-medium" style="color: var(--primary);">(PT{{ str_pad($consultation->patient_id, 3, '0', STR_PAD_LEFT) }})</span>
                                </span>
                                @if ($isCritical)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wide bg-red-100 text-red-800">
                                        <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Critical
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs lg:text-sm mb-1" style="color: var(--ink-muted);">{{ \Carbon\Carbon::parse($consultation->created_at)->format('M d, Y \a\t h:i A') }}@if ($consultation->zone_number ?? null) · Zone {{ $consultation->zone_number }}@endif</p>
                            @if (($showQueue ?? false) && ($consultation->complaint_text ?? null))
                                <p class="text-xs italic line-clamp-2" style="color: var(--ink-subtle);">{{ $consultation->complaint_text }}</p>
                            @endif
                            <p class="text-xs lg:text-sm mb-1" style="color: var(--ink-muted);"><span class="font-medium" style="color: var(--ink);">Doctor:</span> {{ $consultation->worker_first_name }} {{ $consultation->worker_last_name }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            @if ($consultation->status === 'completed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" style="background: var(--teal-soft); color: var(--primary);">
                                    {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                                </span>
                            @elseif ($consultation->status === 'pending_validation')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" style="background: var(--accent-soft); color: var(--accent);">
                                    Pending validation
                                </span>
                            @elseif ($consultation->status === 'referred')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-950 ring-1 ring-amber-400">
                                    {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" style="background: rgba(0,0,0,0.06); color: var(--ink-muted);">
                                    {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                                </span>
                            @endif
                            <div class="flex items-center gap-2">
                                <a href="{{ route('consultations.edit', $consultation->id) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition hover:opacity-85" style="background: var(--accent-soft); color: var(--accent);">Edit</a>
                                <a href="{{ route('consultations.show', $consultation->id) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition hover:opacity-85" style="background: var(--teal-soft); color: var(--primary);">{{ ($showQueue ?? false) ? 'Open case' : 'View details' }}</a>
                                @if (in_array($consultation->status, ['completed', 'referred'], true))
                                    <a href="{{ route('consultations.handout', $consultation->id) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition hover:opacity-85" style="background: rgba(0,0,0,0.06); color: var(--ink-muted);"><i class="fa-solid fa-print"></i> Handout</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if (!empty($diagnoses))
                        <p class="text-xs lg:text-sm">
                            <span class="font-medium" style="color: var(--ink-muted);">Diagnosis:</span>
                            <span class="flex flex-wrap gap-1 mt-1">
                                @foreach ($diagnoses as $d)
                                    <span class="inline-block px-2 py-0.5 rounded-lg text-xs font-medium" :class="{ 'blur-sensitive-phi': blurSensitive }" style="background: var(--teal-soft); color: var(--primary);">{{ $d }}</span>
                                @endforeach
                            </span>
                        </p>
                    @endif
                    @if (!empty($treatments))
                        <p class="text-xs lg:text-sm" style="color: var(--ink-muted);">
                            <span class="font-medium" style="color: var(--ink);">Treatment:</span>
                            <span class="ml-1 select-none" :class="{ 'blur-sensitive-phi': blurSensitive }">{{ implode('. ', $treatments) }}</span>
                        </p>
                    @else
                        <p class="text-xs lg:text-sm italic" style="color: var(--ink-subtle);">No treatment recorded.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border p-8 lg:p-12 text-center" style="background: var(--bg-surface); border-color: var(--border);">
                <div class="flex justify-center mb-4"><i class="fa-solid fa-circle-notch text-4xl" style="color: var(--ink-subtle);"></i></div>
                <p class="text-lg font-semibold" style="color: var(--ink);">No consultations found</p>
                <p class="text-sm mt-2 mb-4" style="color: var(--ink-muted);">No consultations match your search and filter criteria. Try adjusting your filters or clear them to see all records.</p>
                <a href="{{ route('consultations.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition hover:opacity-90" style="background: var(--primary);"><i class="fa-solid fa-arrow-rotate-left"></i> Clear all filters</a>
            </div>
        @endforelse
    </div>

    @if ($consultations->total() > 0)
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 min-h-[3rem]">
            <p class="text-xs order-2 sm:order-1" style="color: var(--ink-muted);">
                Showing <span class="font-medium" style="color: var(--ink);">{{ $consultations->firstItem() }}</span>–<span class="font-medium" style="color: var(--ink);">{{ $consultations->lastItem() }}</span> of <span class="font-medium" style="color: var(--ink);">{{ $consultations->total() }}</span> records
            </p>
            <div class="order-1 sm:order-2 flex justify-center sm:justify-end w-full sm:w-auto">
                {{ $consultations->onEachSide(1)->links() }}
            </div>
        </div>
    @endif
</div>

<style>
.blur-sensitive {
    filter: blur(4px);
    transition: filter 0.2s ease;
}
.blur-sensitive:hover {
    filter: none;
}
.blur-sensitive-phi {
    filter: blur(4px);
    transition: filter 0.2s ease;
}
</style>
@endsection
