@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6">
    <div class="animate-in opacity-0 delay-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $roleLabel }} Dashboard</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">Track patient queue and complete consultations efficiently.</p>
        </div>
        <a href="{{ route('consultations.index', ['queue' => 1]) }}"
           class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-xl text-xs font-semibold text-white transition-[transform,box-shadow] duration-200 hover:shadow-md hover:scale-[1.01]"
           style="background-color: #1B4332; box-shadow: var(--shadow-sm);">
            Open consultation queue
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="animate-in opacity-0 delay-2 p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Consultations today</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $consultationsToday }}</p>
            <p class="text-xs mt-2" style="color: var(--ink-muted);">All visits logged today</p>
        </div>

        <div class="animate-in opacity-0 delay-3 p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--accent);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Pending queue</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $pendingConsultations }}</p>
            <p class="text-xs mt-2" style="color: var(--ink-muted);">Cases waiting for clinical review</p>
        </div>

        <div class="animate-in opacity-0 delay-4 p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Completed today</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $completedConsultationsToday }}</p>
            <p class="text-xs mt-2" style="color: var(--ink-muted);">Consultations closed today</p>
        </div>

        <div class="animate-in opacity-0 delay-5 p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--accent);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Follow-ups today</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $followUpConsultationsToday }}</p>
            <p class="text-xs mt-2" style="color: var(--ink-muted);">Visits tagged as follow-up</p>
        </div>
    </div>

    @if (($role ?? '') === 'nurse' && ($pendingValidationCount ?? 0) > 0)
        <div class="animate-in opacity-0 delay-5 rounded-xl border p-4 lg:p-5"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--accent);">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div>
                    <h2 class="font-display font-semibold text-lg" style="color: var(--ink);">Nurse validation queue</h2>
                    <p class="text-xs mt-1" style="color: var(--ink-muted);">{{ $pendingValidationCount }} intake{{ $pendingValidationCount !== 1 ? 's' : '' }} awaiting acknowledgment before doctor review.</p>
                </div>
                <a href="{{ route('consultations.index', ['queue' => 1, 'status' => 'pending_validation']) }}" class="text-xs font-semibold hover:underline" style="color: var(--primary);">View all</a>
            </div>
            <ul class="space-y-2">
                @foreach ($validationQueue as $item)
                    <li class="rounded-xl border px-4 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                        style="border-color: var(--border); background: var(--bg-surface-elevated);">
                        <div>
                            <p class="text-sm font-semibold" style="color: var(--ink);">{{ $item->last_name }}, {{ $item->first_name }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--ink-muted);">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}@if ($item->complaint_text) · {{ Str::limit($item->complaint_text, 60) }}@endif</p>
                        </div>
                        <form action="{{ route('consultations.acknowledge-intake', $item->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-semibold text-white transition hover:opacity-90" style="background: var(--accent);">
                                Acknowledge intake
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <div class="lg:col-span-8 animate-in opacity-0 delay-5 rounded-xl border p-4 lg:p-5"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-display font-semibold text-lg lg:text-xl" style="color: var(--ink);">Recent pending consultations</h2>
                    <p class="text-xs mt-1" style="color: var(--ink-muted);">Quick access to patients currently in queue.</p>
                </div>
                <a href="{{ route('consultations.index', ['queue' => 1]) }}" class="text-xs font-semibold hover:underline" style="color: var(--primary);">
                    View all
                </a>
            </div>

            <div class="mt-4 divide-y divide-[var(--border)]">
                @forelse ($recentQueue as $item)
                    <a href="{{ route('consultations.show', $item['id']) }}"
                       class="block py-3 transition-colors hover:bg-black/[0.02] rounded-lg px-2 -mx-2">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--ink);">{{ucwords ($item['patient_name']) }}</p>
                                <p class="text-xs mt-1 capitalize" style="color: var(--ink-muted);">{{ $item['status'] }}</p>
                            </div>
                            <span class="text-xs" style="color: var(--ink-subtle);">{{ $item['time'] }}</span>
                        </div>
                    </a>
                @empty
                    <div class="py-8 text-center">
                        <p class="text-sm font-semibold" style="color: var(--ink);">No pending consultations</p>
                        <p class="text-xs mt-1" style="color: var(--ink-muted);">The clinical queue is currently clear.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-4 space-y-4">
            <div class="animate-in opacity-0 delay-6 rounded-xl border p-4 lg:p-5"
                 style="background: var(--primary); border-color: rgba(255,255,255,0.14); box-shadow: var(--shadow-md);">
                <h3 class="font-display font-semibold text-lg" style="color: #fff;">Clinical Reminder</h3>
                <p class="text-sm mt-2" style="color: rgba(255,255,255,0.88);">
                    Prioritize high-risk symptoms and ensure complete diagnosis notes before closing each consultation.
                </p>
            </div>

            <div class="animate-in opacity-0 delay-6 rounded-xl border p-4 lg:p-5"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
                <h3 class="text-xs font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">Quick actions</h3>
                <div class="mt-3 space-y-2">
                    <a href="{{ route('consultations.index', ['queue' => 1]) }}"
                       class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold text-white transition-[transform,box-shadow] duration-200 hover:shadow-sm hover:scale-[1.01]"
                       style="background-color: #1B4332; box-shadow: var(--shadow-sm);">
                        Manage queue
                    </a>
                    <a href="{{ route('patients.index') }}"
                       class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-semibold border transition-colors hover:bg-black/[0.02]"
                       style="border-color: var(--border); color: var(--primary);">
                        Open patient records
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($showResultsReady ?? false)
        @include('dashboard.partials.results-ready', [
            'panelTitle' => 'Recent completed — print handouts',
            'panelSubtitle' => 'Today’s finalized consultations. Print Rx and diagnosis summaries for patient pickup.',
            'showFilters' => true,
            'filterAction' => route('dashboard'),
        ])
    @endif
</div>
@endsection
