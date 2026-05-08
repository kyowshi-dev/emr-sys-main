@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6" x-data="{ blurSensitive: false }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Patient records</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">Search and manage patient information.</p>
        </div>
        <a href="{{ url('/patients/create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all duration-200 hover:opacity-95 active:scale-[0.98] shrink-0"
           style="background: #0d4a3c; box-shadow: 0 2px 8px rgba(196, 92, 65, 0.25);">
            Enrol New Patient 
        </a>
    </div>

    <div class="rounded-xl" x-data="patientSearch()">
        <div class="relative">
            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none" style="color: var(--ink-subtle);">
                <i class="fa fa-search" aria-hidden="true"></i>
            </span>
            <input type="text" x-model="query" @input.debounce.300ms="search()"
                   placeholder="Search"
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border text-sm focus:outline-none focus:ring-2 transition"
                   style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);"
                   autocomplete="off">
        </div>
        <div x-show="results.length > 0" class="mt-3 rounded-lg border overflow-hidden" style="display: none; border-color: var(--border); background: var(--bg-surface-elevated); box-shadow: var(--shadow-md);">
            <ul>
                <template x-for="patient in results" :key="patient.id">
                    <li class="border-b last:border-0 transition-colors hover:bg-black/[0.03]">
                        <a :href="'/patients/' + patient.id" class="block px-4 py-2.5">
                            <div class="font-medium text-sm" style="color: var(--ink);" x-text="patient.text"></div>
                            <div class="text-xs mt-0.5" style="color: var(--ink-muted);" x-text="patient.subtext"></div>
                        </a>
                    </li>
                </template>
            </ul>
        </div>
        <div x-show="query.length > 1 && results.length === 0 && !loading" class="mt-2 text-xs" style="display: none; color: var(--ink-muted);">
            No patient found. You may register a new record.
        </div>
    </div>


    <div class="rounded-xl border overflow-hidden" style="background: var(--bg-surface-elevated); border-color: var(--border); box-shadow: var(--shadow-sm);">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs lg:text-sm">
                <thead>
                    <tr style="background: var(--teal-soft);">
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--ink-muted);">ID</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--ink-muted);">Name</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--ink-muted);">Age</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap hidden sm:table-cell" style="color: var(--ink-muted);">Gender</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap hidden md:table-cell" style="color: var(--ink-muted);">Phone</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap hidden lg:table-cell" style="color: var(--ink-muted);">Last visit</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right whitespace-nowrap" style="color: var(--ink-muted);">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse ($patients as $patient)
                        <tr class="transition-colors hover:bg-black/[0.02]">
                            <td class="px-4 py-2.5 font-medium whitespace-nowrap" style="color: var(--ink);">PT{{ str_pad($patient->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2.5" style="color: var(--ink);">
                                <div class="font-medium" :class="{ 'blur-sensitive': blurSensitive }">{{ $patient->last_name }}, {{ $patient->first_name }}</div>
                                <div class="text-xs sm:hidden" style="color: var(--ink-muted);">{{ $patient->sex }}</div>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap" style="color: var(--ink-muted);">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap hidden sm:table-cell" style="color: var(--ink-muted);">{{ $patient->sex }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap hidden md:table-cell" :class="{ 'blur-sensitive': blurSensitive }" style="color: var(--ink-muted);">{{ $patient->contact_number ?? '—' }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap hidden lg:table-cell" style="color: var(--ink-muted);">
                                @if ($patient->last_visit) {{ \Carbon\Carbon::parse($patient->last_visit)->format('Y-m-d') }} @else — @endif
                            </td>
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <a href="{{ route('patients.show', $patient->id) }}" class="font-semibold text-sm transition-colors hover:underline" style="color: var(--primary);">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-sm" style="color: var(--ink-muted);">No patients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($patients->hasPages())
            <div class="border-t px-4 py-3" style="border-color: var(--border);">
                {{ $patients->onEachSide(1)->links() }}
            </div>
        @endif
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

@if(session('success') && session('new_patient_id'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const patientId = {{ session('new_patient_id') }};
        Swal.fire({
            title: 'Patient Registered Successfully!',
            text: 'What would you like to do next?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: 'Proceed to Consultation',
            cancelButtonText: 'Back to Home',
            confirmButtonColor: '#0d4a3c',
            cancelButtonColor: '#6b7280',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/patients/${patientId}/consultations/create`;
            } else {
                // Stay on the current page (patients index)
            }
        });
    });
</script>
@endif>

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
