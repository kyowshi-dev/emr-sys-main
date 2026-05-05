@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Edit Consultation</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">PT{{ str_pad($consultation->patient_id, 3, '0', STR_PAD_LEFT) }} - {{ $patient->last_name }}, {{ $patient->first_name }}</p>
        </div>
        <a href="{{ route('consultations.show', $consultation->id) }}" class="px-4 py-2 rounded-xl text-white text-sm font-semibold transition" style="background: var(--primary);">← Back to View</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-5 lg:space-y-6">
            <!-- Consultation Info -->
            <div class="rounded-xl border p-5 lg:p-6 space-y-4" style="background: var(--bg-surface); border-color: var(--border);">
                <h2 class="font-semibold text-lg" style="color: var(--ink);">Consultation Details</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium" style="color: var(--ink-muted);">Date</p>
                        <p class="text-sm mt-1" style="color: var(--ink);">{{ \Carbon\Carbon::parse($consultation->created_at)->format('Y-m-d H:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--ink-muted);">Status</p>
                        <p class="text-sm mt-1" style="color: var(--ink);">{{ ucfirst(str_replace('_', ' ', $consultation->status)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--ink-muted);">Health Worker</p>
                        <p class="text-sm mt-1" style="color: var(--ink);">{{ $consultation->worker_first_name }} {{ $consultation->worker_last_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium" style="color: var(--ink-muted);">Nature of Visit</p>
                        <p class="text-sm mt-1" style="color: var(--ink);">{{ $consultation->nature_of_visit }}</p>
                    </div>
                </div>
            </div>

            <!-- Diagnoses Section -->
            <div class="rounded-xl border p-5 lg:p-6 space-y-4" style="background: var(--bg-surface); border-color: var(--border);">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-lg" style="color: var(--ink);">Diagnoses</h2>
                </div>
                
                @if ($diagnoses->count() > 0)
                    <div class="space-y-3">
                        @foreach ($diagnoses as $diagnosis)
                            <div class="p-3 rounded-lg border flex items-start justify-between" style="background: rgba(0,0,0,0.02); border-color: var(--border);">
                                <div class="flex-1">
                                    <p class="text-sm font-medium" style="color: var(--ink);">{{ $diagnosis->diagnosis_name }}</p>
                                    @if ($diagnosis->remarks)
                                        <p class="text-xs mt-1" style="color: var(--ink-muted);">{{ $diagnosis->remarks }}</p>
                                    @endif
                                </div>
                                <span class="text-xs" style="color: var(--ink-muted);">#{{ $diagnosis->id }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm italic" style="color: var(--ink-muted);">No diagnoses recorded for this consultation.</p>
                @endif
                
                <p class="text-xs" style="color: var(--ink-subtle);">To modify diagnoses, please view the full consultation record.</p>
            </div>

            <!-- Prescriptions Section -->
            <div class="rounded-xl border p-5 lg:p-6 space-y-4" style="background: var(--bg-surface); border-color: var(--border);">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-lg" style="color: var(--ink);">Prescriptions</h2>
                </div>
                
                @if ($prescriptions->count() > 0)
                    <div class="space-y-3">
                        @foreach ($prescriptions as $prescription)
                            <div class="p-3 rounded-lg border" style="background: rgba(0,0,0,0.02); border-color: var(--border);">
                                <p class="text-sm font-medium" style="color: var(--ink);">{{ $prescription->medicine_name }}</p>
                                <div class="grid grid-cols-2 gap-2 mt-2 text-xs" style="color: var(--ink-muted);">
                                    @if ($prescription->dosage)
                                        <p><span class="font-medium">Dosage:</span> {{ $prescription->dosage }}</p>
                                    @endif
                                    @if ($prescription->frequency)
                                        <p><span class="font-medium">Frequency:</span> {{ $prescription->frequency }}</p>
                                    @endif
                                    @if ($prescription->duration)
                                        <p><span class="font-medium">Duration:</span> {{ $prescription->duration }}</p>
                                    @endif
                                    @if ($prescription->quantity)
                                        <p><span class="font-medium">Quantity:</span> {{ $prescription->quantity }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm italic" style="color: var(--ink-muted);">No prescriptions recorded for this consultation.</p>
                @endif
                
                <p class="text-xs" style="color: var(--ink-subtle);">To add or modify prescriptions, please view the full consultation record.</p>
            </div>

            <!-- Notes -->
            <div class="rounded-xl border p-5 lg:p-6 space-y-4" style="background: var(--bg-surface); border-color: var(--border);">
                <h2 class="font-semibold text-lg" style="color: var(--ink);">Quick Notes</h2>
                <textarea placeholder="Add quick notes about this consultation..." rows="4" class="w-full px-3 py-2 rounded-lg border text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary); resize: vertical;"></textarea>
                <button type="button" class="px-4 py-2 rounded-xl text-white text-sm font-semibold transition" style="background: var(--primary);">Save Notes</button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-5 lg:space-y-6">
            <!-- Patient Info Card -->
            <div class="rounded-xl border p-5 lg:p-6 space-y-4" style="background: var(--bg-surface); border-color: var(--border);">
                <h3 class="font-semibold text-base" style="color: var(--ink);">Patient Information</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <p style="color: var(--ink-muted);">Name</p>
                        <p class="font-medium" style="color: var(--ink);">{{ $patient->last_name }}, {{ $patient->first_name }}</p>
                    </div>
                    <div>
                        <p style="color: var(--ink-muted);">Patient ID</p>
                        <p class="font-medium" style="color: var(--ink);">PT{{ str_pad($patient->id, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <p style="color: var(--ink-muted);">Date of Birth</p>
                        <p class="font-medium" style="color: var(--ink);">{{ \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <p style="color: var(--ink-muted);">Sex</p>
                        <p class="font-medium" style="color: var(--ink);">{{ $patient->sex }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="rounded-xl border p-5 lg:p-6 space-y-3" style="background: var(--bg-surface); border-color: var(--border);">
                <h3 class="font-semibold text-base" style="color: var(--ink);">Quick Actions</h3>
                
                <div class="space-y-2">
                    <a href="{{ route('consultations.show', $consultation->id) }}" class="block w-full px-4 py-2 rounded-lg text-center text-sm font-semibold transition" style="background: var(--primary-soft); color: var(--primary);">👁 View Full Record</a>
                    <form action="{{ route('consultations.export', $consultation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-lg text-center text-sm font-semibold transition" style="background: var(--teal-soft); color: var(--teal); border: none; cursor: pointer;">📄 Export as PDF</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
