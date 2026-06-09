@extends('layouts.app')

@section('content')
@php
    $todayLabel = now()->format('F d, Y');
    $weekdayLabel = now()->format('l');
    $schedule = [
        ['day' => 'Monday', 'icon' => 'medical', 'label' => 'General Consultation'],
        ['day' => 'Tuesday', 'icon' => 'medical', 'label' => 'General Consultation'],
        ['day' => 'Wednesday', 'icon' => 'medical', 'label' => 'Immunization'],
        ['day' => 'Thursday', 'icon' => 'medical', 'label' => 'Prenatal Care'],
        ['day' => 'Friday', 'icon' => 'medical', 'label' => 'Postpartum & FP'],
    ];
@endphp

<div class="space-y-4 lg:space-y-6">
    <div class="animate-in opacity-0 delay-1 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Dashboard</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">Overview of health center activity</p>
        </div>

        <div class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs sm:text-sm"
             style="background: var(--bg-surface); border-color: var(--border); color: var(--ink-muted); box-shadow: var(--shadow-sm);">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg"
                  style="background: var(--teal-soft); color: var(--primary);">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                    <path d="M16 2v4"></path>
                    <path d="M8 2v4"></path>
                    <path d="M3 10h18"></path>
                </svg>
            </span>
            <div class="leading-tight">
                <div class="font-semibold" style="color: var(--ink);">{{ $todayLabel }}</div>
                <div class="text-xs" style="color: var(--ink-muted);">{{ $weekdayLabel }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4">
        <div class="animate-in opacity-0 delay-2 p-4 lg:p-5 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Individual Records</p>
            @if ($totalPatients === 0)
                <div class="space-y-3">
                    <div class="py-6 text-center">
                        <div style="color: var(--ink-muted); margin-bottom: 0.75rem;">
                            <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20a9 9 0 0118 0v2h2v-2a11 11 0 00-22 0v2h2v-2z"></path></svg>
                        </div>
                        <p class="font-semibold text-sm" style="color: var(--ink);">No patients recorded</p>
                        <p class="text-xs mt-1" style="color: var(--ink-muted);">Add your first patient to get started</p>
                    </div>
                    <a href="{{ route('patients.create') }}" class="w-full inline-flex items-center justify-center text-xs font-bold px-3 py-2 rounded-lg transition-[transform,box-shadow] duration-200 hover:shadow-sm hover:scale-[1.01]" style="background: var(--primary); color: #fff; box-shadow: var(--shadow-sm);">Register first patient</a>
                </div>
            @else
                <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $totalPatients }}</p>
                <div class="mt-3 flex gap-2 flex-wrap">
                    
                    <a href="{{ route('patients.create') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: #0d4a3c;">Register patient</a>
                </div>
            @endif
        </div>
        <div class="animate-in opacity-0 delay-3 p-3 lg:p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--accent);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Pending check-ups</p>
            @if ($pendingAppointments === 0)
                <div class="space-y-3">
                    <div class="py-4 text-center">
                        <div style="color: #0d4a3c; margin-bottom: 0.5rem;">
                            <svg class="w-7 h-7 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="font-semibold text-sm" style="color: var(--ink);">All caught up</p>
                        <p class="text-xs mt-1" style="color: var(--ink-muted);">No pending appointments</p>
                    </div>
                    <a href="{{ route('consultations.index') }}" class="w-full inline-flex items-center justify-center text-xs font-bold px-3 py-2 rounded-lg transition-[transform,box-shadow] duration-200 hover:shadow-sm hover:scale-[1.01]" style="background: var(--accent); color: #fff; box-shadow: var(--shadow-sm);">View appointments</a>
                </div>
            @else
                <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $pendingAppointments }}</p>
                <p class="text-xs mt-2" style="color: var(--ink-muted);">Open queue awaiting review</p>
                <div class="mt-3 flex gap-2 flex-wrap">
                    <a href="{{ route('consultations.index') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: var(--primary);">Manage appointments</a>
                </div>
            @endif
        </div>
        <div class="animate-in opacity-0 delay-4 p-3 lg:p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: {{ $overdueImmunizations > 0 ? '#fef2f2' : 'var(--bg-surface)' }}; border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid {{ $overdueImmunizations > 0 ? '#ef4444' : 'var(--primary)' }};">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: {{ $overdueImmunizations > 0 ? '#991b1b' : 'var(--ink-muted)' }};">Overdue immunizations</p>
            @if ($overdueImmunizations === 0)
                <div class="space-y-3">
                    <div class="py-4 text-center">
                        <div style="color: var(--primary); margin-bottom: 0.5rem;">
                            <svg class="w-7 h-7 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="font-semibold text-sm" style="color: var(--ink);">Schedule on track</p>
                        <p class="text-xs mt-1" style="color: var(--ink-muted);">All immunizations current</p>
                    </div>
                </div>
            @else
                <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: #b91c1c;">{{ $overdueImmunizations }}</p>
                <p class="text-xs mt-2" style="color: #7f1d1d;">Patients needing follow-up</p>
                <div class="mt-3 flex gap-2 flex-wrap">
                    <a href="{{ route('immunizations.index') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: #b91c1c;">View chart</a>
                </div>
            @endif
        </div>
        <div class="animate-in opacity-0 delay-5 p-3 lg:p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid #f59e0b;">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Follow-up today</p>
            @if ($followUpConsultationsToday === 0)
                <div class="space-y-3">
                    <div class="py-4 text-center">
                        <div style="color: #f59e0b; margin-bottom: 0.5rem;">
                            <svg class="w-7 h-7 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="font-semibold text-sm" style="color: var(--ink);">No follow-ups scheduled</p>
                        <p class="text-xs mt-1" style="color: var(--ink-muted);">Nothing due today</p>
                    </div>
                </div>
            @else
                <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $followUpConsultationsToday }}</p>
                <p class="text-xs mt-2" style="color: var(--ink-muted);">Follow-up visits scheduled</p>
                <div class="mt-3 flex gap-2 flex-wrap">
                    <a href="{{ route('consultations.index') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: #f59e0b;">Review follow-up</a>
                </div>
            @endif
        </div>
        <div class="animate-in opacity-0 delay-4 p-4 lg:p-5 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Health workers</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $doctorsOnDuty }}</p>
            <p class="text-xs mt-2" style="color: var(--ink-muted);">Total staff on record</p>
        </div>
        @if($pendingPasswordResets > 0)
        <div class="animate-in opacity-0 delay-5 p-4 lg:p-5 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: #fef3c7; border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid #f59e0b;">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: #92400e;">Pending password resets</p>
            <p class="font-display font-semibold text-xl lg:text-2xl" style="color: #92400e;">{{ $pendingPasswordResets }}</p>
            <p class="text-xs mt-2" style="color: #78350f;">Users awaiting password reset</p>
            <div class="mt-3 flex gap-2 flex-wrap">
                <a href="{{ route('users.password-reset-requests') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: #f59e0b;">Review requests</a>
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:gap-5">
        <div class="lg:col-span-8 space-y-3 lg:space-y-5">
            <div class="animate-in opacity-0 delay-5 rounded-xl border p-4 lg:p-5"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-display font-semibold text-lg lg:text-xl" style="color: var(--ink);">Weekly Service Schedule</h2>
                        <p class="text-xs mt-1" style="color: var(--ink-muted);">Quick reference for clinic days</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                          style="background: var(--teal-soft); color: var(--primary);">
                        Updated weekly
                    </span>
                </div>

                <div class="mt-3 grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @foreach ($schedule as $slot)
                        @php
                            $isCurrentDay = strtolower($slot['day']) === strtolower($weekdayLabel);
                        @endphp
                        <div class="rounded-xl border p-3 text-center transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
                             style="background: @if($isCurrentDay) var(--teal-soft) @else var(--bg-surface-elevated) @endif; border-color: @if($isCurrentDay) var(--primary) @else var(--border) @endif; box-shadow: var(--shadow-sm); @if($isCurrentDay) border-width: 2px; @endif">
                            <div class="text-[10px] font-semibold uppercase tracking-wider"
                                 style="color: @if($isCurrentDay) var(--primary) @else var(--ink-muted) @endif;">
                                {{ $slot['day'] }}
                            </div>
                            <div class="mt-2 flex items-center justify-center" aria-hidden="true" style="color: @if($isCurrentDay) var(--primary) @else var(--primary) @endif;">
                                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M10 3h4v4h4v4h-4v4h-4v-4H6V7h4z"></path>
                                </svg>
                            </div>
                            <div class="mt-2 text-xs font-semibold leading-snug" style="color: @if($isCurrentDay) var(--primary) @else var(--ink) @endif;">
                                {{ $slot['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3 rounded-xl border px-4 py-3 text-xs"
                     style="background: rgba(0,0,0,0.02); border-color: var(--border); color: var(--ink-muted);">
                    Emergency cases are handled daily. Please coordinate with the RHU on-duty for urgent concerns.
                </div>
            </div>

            @if ($showResultsReady ?? false)
                @include('dashboard.partials.results-ready', [
                    'panelTitle' => 'Completed consultations — handouts',
                    'panelSubtitle' => 'Barangay-wide completed visits. Print patient handouts for pickup or records.',
                    'showFilters' => true,
                    'filterAction' => route('dashboard'),
                ])
            @endif

            <div class="animate-in opacity-0 delay-6 rounded-xl border p-4 lg:p-5"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
                <h2 class="font-display font-semibold text-lg lg:text-xl mb-4" style="color: var(--ink);">Recent activity</h2>
                <ul class="divide-y divide-[var(--border)] space-y-0">
                    @forelse($recentActivity as $activity)
                        <li class="py-3 text-sm transition-colors hover:bg-black/[0.02]" style="color: var(--ink-muted);">
                            {{ $activity }}
                        </li>
                    @empty
                        <li class="py-8 px-4 text-center space-y-4">
                            <div class="space-y-2">
                                <div class="text-sm font-semibold" style="color: var(--ink);">No recent activity</div>
                                <div class="text-xs" style="color: var(--ink-subtle);">Activity from patient registrations, consultations, and updates will appear here</div>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('patients.create') }}"
                                   class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-bold transition-[transform,box-shadow,background] duration-200 hover:shadow-sm hover:scale-[1.01] active:scale-[0.98]"
                                   style="background: var(--primary); color: #fff; box-shadow: var(--shadow-sm);">
                                    Get started
                                </a>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-3 lg:space-y-5">
            <div class="animate-in opacity-0 delay-5 rounded-xl border p-4 lg:p-5"
                 style="background: var(--primary); border-color: rgba(255,255,255,0.14); box-shadow: var(--shadow-md);">
                <h3 class="font-display font-semibold text-lg" style="color: #fff;">Health Advisory</h3>
                <p class="text-sm mt-2" style="color: rgba(255,255,255,0.88);">
                    Dengue awareness month is approaching. Please ensure all residents are practicing the 4S strategy in their households.
                </p>
                <a href="{{ route('reports.index') }}"
                   class="inline-flex items-center mt-3 px-3.5 py-2 rounded-xl text-xs font-semibold transition-[transform,box-shadow] duration-200 hover:shadow-md hover:scale-[1.01]"
                   style="background: rgba(255,255,255,0.14); color: #fff;">
                    Read guidelines
                </a>
            </div>

            <div class="animate-in opacity-0 delay-6 rounded-xl border p-4 lg:p-5"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">On-duty staff</h3>
                    <span class="text-xs" style="color: var(--ink-subtle);">Today</span>
                </div>

                <div class="mt-3 max-h-60 overflow-y-auto space-y-3">
                    @forelse ($onDutyStaff as $staff)
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full flex items-center justify-center text-xs font-semibold flex-shrink-0"
                                 style="background: var(--teal-soft); color: var(--primary);">
                                {{ $staff['initials'] }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold truncate" style="color: var(--ink);">{{ $staff['name'] }}</div>
                                <div class="text-xs truncate" style="color: var(--ink-muted);">{{ $staff['role'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center space-y-3">
                            <div style="color: var(--ink-muted);">
                                <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292m0-5.292H8.646A4 4 0 0012 4.354zm0 0h3.354A4 4 0 0012 4.354m0 5.292L15.354 7.354M12 9.646l-3.354 2.292m0 0A4 4 0 004.354 12m0 0h5.292m-5.292 0a4 4 0 100 5.292m5.292 0H8.646m3.354-2.292l3.354 2.292m0 0A4 4 0 0019.646 12m0 0v5.292m0-5.292h-5.292"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold" style="color: var(--ink);">No staff data</p>
                                <p class="text-xs mt-1" style="color: var(--ink-muted);">Add health workers to see them here</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
