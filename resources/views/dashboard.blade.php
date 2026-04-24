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

<div class="space-y-6 lg:space-y-8">
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
        <div class="animate-in opacity-0 delay-2 p-5 lg:p-6 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Total patients</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $totalPatients }}</p>
            <div class="mt-4 flex gap-2 flex-wrap">
                <a href="{{ route('patients.index') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: var(--primary);">View all</a>
                <a href="{{ route('patients.create') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: var(--accent);">Register patient</a>
            </div>
        </div>
        <div class="animate-in opacity-0 delay-3 p-4 lg:p-5 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--accent);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Pending appointments</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $pendingAppointments }}</p>
            <p class="text-xs mt-2" style="color: var(--ink-muted);">Open queue awaiting review</p>
            <div class="mt-4 flex gap-2 flex-wrap">
                <a href="{{ route('consultations.index') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: var(--primary);">Manage appointments</a>
            </div>
        </div>
        <div class="animate-in opacity-0 delay-4 p-4 lg:p-5 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: #fef2f2; border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid #ef4444;">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: #991b1b;">Overdue immunizations</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: #b91c1c;">{{ $overdueImmunizations }}</p>
            <p class="text-xs mt-2" style="color: #7f1d1d;">Patients needing follow-up</p>
            <div class="mt-4 flex gap-2 flex-wrap">
                <a href="{{ route('immunizations.index') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: #b91c1c;">View chart</a>
            </div>
        </div>
        <div class="animate-in opacity-0 delay-5 p-4 lg:p-5 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
             style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid #f59e0b;">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Follow-up today</p>
            <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $followUpConsultationsToday }}</p>
            <p class="text-xs mt-2" style="color: var(--ink-muted);">Follow-up visits scheduled</p>
            <div class="mt-4 flex gap-2 flex-wrap">
                <a href="{{ route('consultations.index') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: #f59e0b;">Review follow-up</a>
            </div>
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
            <div class="mt-4 flex gap-2 flex-wrap">
                <a href="{{ route('users.password-reset-requests') }}" class="text-xs font-bold px-2 py-1 rounded-lg border" style="border-color: var(--border); color: #f59e0b;">Review requests</a>
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-6">
        <div class="lg:col-span-8 space-y-4 lg:space-y-6">
            <div class="animate-in opacity-0 delay-5 rounded-xl border p-5 lg:p-6"
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

                <div class="mt-4 grid grid-cols-2 sm:grid-cols-5 gap-3">
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

                <div class="mt-4 rounded-xl border px-4 py-3 text-xs"
                     style="background: rgba(0,0,0,0.02); border-color: var(--border); color: var(--ink-muted);">
                    Emergency cases are handled daily. Please coordinate with the RHU on-duty for urgent concerns.
                </div>
            </div>

            <div class="animate-in opacity-0 delay-6 rounded-xl border p-5 lg:p-6"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
                <h2 class="font-display font-semibold text-lg lg:text-xl mb-4" style="color: var(--ink);">Recent activity</h2>
                <ul class="divide-y divide-[var(--border)] space-y-0">
                    @forelse($recentActivity as $activity)
                        <li class="py-3 text-sm transition-colors hover:bg-black/[0.02]" style="color: var(--ink-muted);">
                            {{ $activity }}
                        </li>
                    @empty
                        <li class="py-8 px-4 text-center">
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <div class="text-sm font-semibold" style="color: var(--ink);">Quick Actions</div>
                                    <div class="text-xs" style="color: var(--ink-subtle);">Get started with common tasks</div>
                                </div>
                                <div class="grid grid-cols-1 gap-2">
                                    <a href="{{ route('patients.create') }}"
                                       class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-bold transition-[transform,box-shadow,background] duration-200 hover:shadow-sm hover:scale-[1.01] active:scale-[0.98]"
                                       style="background: var(--primary); color: #fff; box-shadow: var(--shadow-sm);">
                                        Register Patient
                                    </a>
                                    <a href="{{ route('consultations.index') }}"
                                       class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-bold transition-[transform,box-shadow,background] duration-200 hover:shadow-sm hover:scale-[1.01] active:scale-[0.98]"
                                       style="background: var(--accent); color: #fff; box-shadow: var(--shadow-sm);">
                                        Log Consultation
                                    </a>
                                    <!-- TODO: Connect to vitals logging when available -->
                                    <button
                                       class="inline-flex items-center justify-center px-3 py-2 rounded-lg text-xs font-bold transition-[transform,box-shadow,background,opacity] duration-200 hover:shadow-sm hover:scale-[1.01] active:scale-[0.98] disabled"
                                       style="background: var(--border); color: var(--ink-muted); box-shadow: var(--shadow-sm); opacity: 0.5; cursor: not-allowed;"
                                       title="Feature coming soon">
                                        Update Vitals
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-4 lg:space-y-6">
            <div class="animate-in opacity-0 delay-5 rounded-xl border p-5 lg:p-6"
                 style="background: var(--primary); border-color: rgba(255,255,255,0.14); box-shadow: var(--shadow-md);">
                <h3 class="font-display font-semibold text-lg" style="color: #fff;">Health Advisory</h3>
                <p class="text-sm mt-2" style="color: rgba(255,255,255,0.88);">
                    Dengue awareness month is approaching. Please ensure all residents are practicing the 4S strategy in their households.
                </p>
                <a href="{{ route('reports.index') }}"
                   class="inline-flex items-center mt-4 px-3.5 py-2 rounded-xl text-xs font-semibold transition-[transform,box-shadow] duration-200 hover:shadow-md hover:scale-[1.01]"
                   style="background: rgba(255,255,255,0.14); color: #fff;">
                    Read guidelines
                </a>
            </div>

            <div class="animate-in opacity-0 delay-6 rounded-xl border p-5 lg:p-6"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-semibold uppercase tracking-wider" style="color: var(--ink-muted);">On-duty staff</h3>
                    <span class="text-xs" style="color: var(--ink-subtle);">Today</span>
                </div>

                <div class="mt-4 max-h-60 overflow-y-auto space-y-3">
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
                        <div class="text-sm" style="color: var(--ink-muted);">No staff listed.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
