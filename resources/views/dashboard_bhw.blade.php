@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Dashboard</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">Search a patient to start a new consultation or register a new one.</p>
        </div>
    </div>

    <div class="rounded-xl" x-data="patientSearch()">
        <div class="relative">
            <span class="absolute inset-y-0 flex items-center pointer-events-none" style="color: var(--ink-subtle); left: calc(0.75rem);">
                <i class="fa fa-search" aria-hidden="true"></i>
            </span>
            <input type="text" x-model="query" @input.debounce.300ms="search()"
                   placeholder="Search patients by name or ID"
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 transition"
                   style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);"
                   autocomplete="off">
        </div>
        <div x-show="results.length > 0" class="mt-3 rounded-lg border overflow-hidden" style="display: none; border-color: var(--border); background: var(--bg-surface-elevated); box-shadow: var(--shadow-md);">
            <ul>
                <template x-for="patient in results" :key="patient.id">
                    <li class="border-b last:border-0 transition-colors hover:bg-black/[0.03]">
                        <a :href="'/patients/' + patient.id + '/consultations/create'" class="block px-4 py-2.5">
                            <div class="font-medium text-sm" style="color: var(--ink);" x-text="patient.text"></div>
                            <div class="text-xs mt-0.5" style="color: var(--ink-muted);">
                                <span x-text="patient.subtext"></span>
                                <span class="font-semibold" style="color: var(--primary);"> - Create consultation</span>
                            </div>
                        </a>
                    </li>
                </template>
            </ul>
        </div>
        <div x-show="query.length > 1 && results.length === 0 && !loading" class="mt-2 text-xs" style="display: none; color: var(--ink-muted);">
            No patient found. <a href="{{ url('/patients/create') }}" class="font-semibold" style="color: var(--primary);">Register a new patient</a>.
        </div>
    </div>

    <div>
        <h2 class="font-display font-semibold text-lg lg:text-xl mb-3" style="color: var(--ink);">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="{{ route('reports.index') }}" class="block p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
           style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg" style="background: var(--teal-soft);">
                    <i class="fa-solid fa-file-lines text-lg" style="color: var(--primary);" aria-hidden="true"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-sm" style="color: var(--ink);">Reports</h3>
                    <p class="text-xs mt-1" style="color: var(--ink-muted);">View and generate reports</p>
                </div>
            </div>
        </a>

        <a href="{{ route('patients.index') }}" class="block p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
           style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg" style="background: var(--teal-soft);">
                    <i class="fa-solid fa-users text-lg" style="color: var(--primary);" aria-hidden="true"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-sm" style="color: var(--ink);">Patient Records</h3>
                    <p class="text-xs mt-1" style="color: var(--ink-muted);">Manage patient information</p>
                </div>
            </div>
        </a>

        <a href="{{ route('consultations.index') }}" class="block p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
           style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm);">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg" style="background: var(--teal-soft);">
                    <i class="fa-solid fa-stethoscope text-lg" style="color: var(--primary);" aria-hidden="true"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-sm" style="color: var(--ink);">Consultations</h3>
                    <p class="text-xs mt-1" style="color: var(--ink-muted);">View check-ups and visits</p>
                </div>
            </div>
        </a>
        </div>
    </div>

    <div>
        <h2 class="font-display font-semibold text-lg lg:text-xl mb-3" style="color: var(--ink);">BHW Overview</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);">
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Total patients</p>
                <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $totalPatients ?? 0 }}</p>
                <p class="text-xs mt-2" style="color: var(--ink-muted);">Registered records in the barangay</p>
            </div>

            <div class="p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--accent);">
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Consultations today</p>
                <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $consultationsToday ?? 0 }}</p>
                <p class="text-xs mt-2" style="color: var(--ink-muted);">Newly logged patient visits</p>
            </div>

            <div class="p-4 rounded-xl border transition-[transform,box-shadow] duration-200 hover:scale-[1.01] hover:shadow-md"
                 style="background: var(--bg-surface); border-color: var(--border); box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);">
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--ink-muted);">Pending queue</p>
                <p class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">{{ $pendingConsultations ?? 0 }}</p>
                <p class="text-xs mt-2" style="color: var(--ink-muted);">Cases waiting for doctor review</p>
            </div>
        </div>
    </div>
</div>

<script>
    function patientSearch() {
        return {
            query: '',
            results: [],
            loading: false,
            async search() {
                if (this.query.length < 2) { this.results = []; return; }
                this.loading = true;
                try {
                    const response = await fetch(`/search/patients?query=${this.query}`);
                    this.results = await response.json();
                } catch (e) { console.error('Search failed:', e); }
                this.loading = false;
            },
        };
    }
</script>
@endsection