@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6" x-data="{ blurSensitive: true }">
    <div>
        <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Consultation history</h1>
        <p class="text-sm mt-1" style="color: var(--ink-muted);">View and search past consultations.</p>
    </div>

    <form method="GET" action="{{ route('consultations.index') }}" class="rounded-xl border p-4" style="background: var(--bg-surface); border-color: var(--border);">
        <div class="flex flex-col md:flex-row md:items-end gap-3">
            <div class="flex-1">
                <label for="query" class="sr-only">Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center" style="color: var(--ink-subtle);">🔍</span>
                    <input type="text" id="query" name="query" value="{{ request('query') }}"
                           placeholder="Search by patient, ID, or diagnosis..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 transition"
                           style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                </div>
            </div>
            <div class="flex flex-wrap items-end gap-2 lg:gap-3">
                <div class="flex-1 min-w-[120px]">
                    <label for="date_from" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">From</label>
                    <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="dd/mm/yyyy"
                           class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label for="date_to" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">To</label>
                    <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="dd/mm/yyyy"
                           class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl text-white text-sm font-semibold transition whitespace-nowrap" style="background: var(--primary);">Search</button>
            </div>
        </div>
    </form>

    <div class="flex justify-end">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" x-model="blurSensitive" class="rounded border" style="border-color: var(--border);">
            <span class="text-sm" style="color: var(--ink-muted);">Blur Sensitive Info</span>
        </label>
    </div>

    <div class="space-y-3 lg:space-y-4">
        @forelse ($consultations as $consultation)
            @php
                $diagnoses = $diagnosisByConsultation[$consultation->id] ?? [];
                $treatments = $treatmentByConsultation[$consultation->id] ?? [];
            @endphp
            <div class="rounded-xl border p-4 lg:p-5 transition-colors hover:bg-black/[0.02]" style="background: var(--bg-surface); border-color: var(--border);">
                <div class="flex flex-col gap-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1 lg:mb-2">
                                <span class="font-semibold text-sm lg:text-base" :class="{ 'blur-sensitive': blurSensitive }" style="color: var(--ink);">
                                    {{ $consultation->patient_last_name }}, {{ $consultation->patient_first_name }}
                                    <span class="font-medium" style="color: var(--primary);">(PT{{ str_pad($consultation->patient_id, 3, '0', STR_PAD_LEFT) }})</span>
                                </span>
                            </div>
                            <p class="text-xs lg:text-sm mb-1" style="color: var(--ink-muted);">{{ \Carbon\Carbon::parse($consultation->created_at)->format('Y-m-d \a\t h:i A') }}</p>
                            <p class="text-xs lg:text-sm mb-1" style="color: var(--ink-muted);"><span class="font-medium" style="color: var(--ink);">Doctor:</span> {{ $consultation->worker_first_name }} {{ $consultation->worker_last_name }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold" style="@if ($consultation->status === 'completed') background: var(--teal-soft); color: var(--primary); @elseif ($consultation->status === 'referred') background: var(--accent-soft); color: var(--accent); @else background: rgba(0,0,0,0.06); color: var(--ink-muted); @endif">
                                {{ ucfirst(str_replace('_', ' ', $consultation->status)) }}
                            </span>
                            <a href="{{ route('consultations.show', $consultation->id) }}" class="inline-flex items-center gap-1 text-xs lg:text-sm font-semibold whitespace-nowrap transition hover:underline" style="color: var(--primary);">View</a>
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

    <div class="flex flex-wrap gap-6 py-4 px-5 rounded-xl border" style="background: var(--teal-soft); border-color: var(--border);">
        <div>
            <span class="text-xs lg:text-sm block" style="color: var(--ink-muted);">Total</span>
            <span class="font-display font-semibold text-xl lg:text-2xl" style="color: var(--ink);">{{ $totalConsultations }}</span>
        </div>
        <div>
            <span class="text-xs lg:text-sm block" style="color: var(--ink-muted);">This week</span>
            <span class="font-display font-semibold text-xl lg:text-2xl" style="color: var(--ink);">{{ $thisWeekCount }}</span>
        </div>
        <div>
            <span class="text-xs lg:text-sm block" style="color: var(--ink-muted);">Completed</span>
            <span class="font-display font-semibold text-xl lg:text-2xl" style="color: var(--primary);">{{ $completedCount }}</span>
        </div>
    </div>
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
