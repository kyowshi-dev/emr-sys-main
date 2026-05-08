@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6 animate-in opacity-0" x-data="{ blurSensitive: false }">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Consultation history</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">Review timelines, diagnoses, and treatments with patient-safe visibility controls.</p>
        </div>
        <div class="inline-flex items-center gap-3 px-4 py-2 rounded-xl border self-start" style="background: var(--bg-surface); border-color: var(--border);">
            <span class="text-xs font-semibold uppercase tracking-wide" style="color: var(--ink-muted);">Sensitive data</span>
            <button type="button"
                    @click="blurSensitive = !blurSensitive"
                    :aria-pressed="blurSensitive.toString()"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2"
                    :style="blurSensitive ? 'background: var(--accent); --tw-ring-color: var(--accent);' : 'background: var(--border); --tw-ring-color: var(--primary);'">
                <span class="sr-only">Toggle blur sensitive details</span>
                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform duration-200"
                      :class="blurSensitive ? 'translate-x-5' : 'translate-x-0.5'"></span>
            </button>
            <span class="text-xs" :style="blurSensitive ? 'color: var(--accent);' : 'color: var(--ink-muted);'" x-text="blurSensitive ? 'Blurred' : 'Visible'"></span>
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

    <form method="GET" action="{{ route('consultations.index') }}" class="rounded-xl border p-4 lg:p-5 animate-in opacity-0 delay-4 space-y-4" style="background: var(--bg-surface); border-color: var(--border);">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-display font-semibold text-lg" style="color: var(--ink);">Filter consultations</h2>
            <a href="{{ route('consultations.index') }}" class="text-xs font-semibold transition hover:underline" style="color: var(--primary);">Reset filters</a>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-4">
            <div class="lg:col-span-6 min-w-0">
                <label for="query" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Search patient or diagnosis</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center" style="color: var(--ink-subtle);">🔍</span>
                    <input type="text" id="query" name="query" value="{{ request('query') }}"
                           placeholder="Search by patient, ID, or diagnosis..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 transition"
                           style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                </div>
            </div>
            <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3 lg:gap-4 items-end">
                <div class="sm:col-span-1 lg:col-span-2">
                    <label for="date_from" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">From</label>
                    <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="dd/mm/yyyy"
                           class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                </div>
                <div class="sm:col-span-1 lg:col-span-2">
                    <label for="date_to" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">To</label>
                    <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="dd/mm/yyyy"
                           class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                </div>
                <div class="sm:col-span-2 lg:col-span-2">
                    <label for="sort" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Sort by</label>
                    <select id="sort" name="sort" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                        <option value="newest" @selected($currentSort === 'newest')>Newest First</option>
                        <option value="oldest" @selected($currentSort === 'oldest')>Oldest First</option>
                        <option value="patient_name" @selected($currentSort === 'patient_name')>Patient Name (A-Z)</option>
                        <option value="status" @selected($currentSort === 'status')>Status</option>
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="w-full px-4 py-2 rounded-xl text-white text-sm font-semibold transition hover:opacity-90" style="background: var(--primary);">Apply</button>
                </div>
            </div>
        </div>
        @if (request()->filled('query') || request()->filled('date_from') || request()->filled('date_to') || request()->filled('sort'))
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
                @if (request('sort'))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background: var(--teal-soft); color: var(--primary);">Sort: {{ str_replace('_', ' ', request('sort')) }}</span>
                @endif
            </div>
        @endif
    </form>

    <div class="flex items-center justify-between">
        <h2 class="font-display font-semibold text-lg" style="color: var(--ink);">Consultation timeline</h2>
        <p class="text-xs lg:text-sm" style="color: var(--ink-muted);">{{ $consultations->count() }} record(s) shown</p>
    </div>

    <div class="space-y-3 lg:space-y-4 animate-in opacity-0 delay-5">
        @forelse ($consultations as $consultation)
            @php
                $diagnoses = $diagnosisByConsultation[$consultation->id] ?? [];
                $treatments = $treatmentByConsultation[$consultation->id] ?? [];
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
                            </div>
                            <p class="text-xs lg:text-sm mb-1" style="color: var(--ink-muted);">{{ \Carbon\Carbon::parse($consultation->created_at)->format('M d, Y \a\t h:i A') }}</p>
                            <p class="text-xs lg:text-sm mb-1" style="color: var(--ink-muted);"><span class="font-medium" style="color: var(--ink);">Doctor:</span> {{ $consultation->worker_first_name }} {{ $consultation->worker_last_name }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" style="@if ($consultation->status === 'completed') background: var(--teal-soft); color: var(--primary); @elseif ($consultation->status === 'referred') background: var(--accent-soft); color: var(--accent); @else background: rgba(0,0,0,0.06); color: var(--ink-muted); @endif">
                                {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                            </span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('consultations.show', $consultation->id) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition hover:opacity-85" style="background: var(--teal-soft); color: var(--primary);">View details</a>
                                <a href="{{ route('consultations.edit', $consultation->id) }}" class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap transition hover:opacity-85" style="background: var(--accent-soft); color: var(--accent);">Edit</a>
                            </div>
                        </div>
                    </div>
                    @if (!empty($diagnoses))
                        <p class="text-xs lg:text-sm">
                            <span class="font-medium" style="color: var(--ink-muted);">Diagnosis:</span>
                            <span class="flex flex-wrap gap-1 mt-1">
                                @foreach ($diagnoses as $d)
                                    <span class="inline-block px-2 py-0.5 rounded-lg text-xs font-medium" :class="{ 'blur-sensitive': blurSensitive }" style="background: var(--teal-soft); color: var(--primary);">{{ $d }}</span>
                                @endforeach
                            </span>
                        </p>
                    @endif
                    @if (!empty($treatments))
                        <p class="text-xs lg:text-sm" style="color: var(--ink-muted);"><span class="font-medium" style="color: var(--ink);">Treatment:</span> <span class="ml-1">{{ implode('. ', $treatments) }}</span></p>
                    @else
                        <p class="text-xs lg:text-sm italic" style="color: var(--ink-subtle);">No treatment recorded.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="rounded-xl border p-6 lg:p-8 text-center text-sm" style="background: var(--bg-surface); border-color: var(--border); color: var(--ink-muted);">No consultations match your criteria.</div>
        @endforelse
    </div>
    @if (method_exists($consultations, 'links'))
        <div class="pt-2">
            {{ $consultations->links() }}
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
</style>
@endsection
