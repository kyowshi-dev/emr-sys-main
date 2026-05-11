@extends('layouts.app')

@section('title', 'Immunization — ' . $patient->last_name . ', ' . $patient->first_name)

@section('content')
<div class="space-y-5 lg:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('patients.show', $patient->id) }}" class="text-sm font-medium hover:underline mb-1 inline-block" style="color: var(--primary);">← Back to patient</a>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Immunization — {{ $patient->last_name }}, {{ $patient->first_name }}</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">{{ $patient->age }} y/o · {{ $patient->sex }} · DOB {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('M j, Y') }}</p>
        </div>

        <div class="flex items-center justify-end">
            <button type="button" onclick="openPageModal()" class="px-4 py-2 rounded-xl text-sm font-semibold text-white transition duration-200 hover:shadow-md" style="background-color: #0e4a3c;">
                Add immunization record
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-xl border px-4 py-3" style="background: var(--teal-soft); border-color: var(--primary); color: var(--primary);">
            {{ session('success') }}
        </div>
    @endif

    <div>
        <h2 class="font-display font-semibold text-lg mb-3" style="color: var(--ink);">History</h2>
        <div class="rounded-xl border overflow-hidden" style="background: var(--bg-surface-elevated); border-color: var(--border);">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead style="background: var(--teal-soft);">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap" style="color: var(--ink-muted);">Date</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap" style="color: var(--ink-muted);">Vaccine</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap" style="color: var(--ink-muted);">Dose</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap hidden sm:table-cell" style="color: var(--ink-muted);">Given by</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap hidden md:table-cell" style="color: var(--ink-muted);">Next due</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse ($records as $r)
                            <tr class="transition-colors hover:bg-black/[0.02]">
                                <td class="px-3 lg:px-4 py-2 lg:py-3 whitespace-nowrap" style="color: var(--ink);">{{ \Carbon\Carbon::parse($r->date_given)->format('M d, Y') }}</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3" style="color: var(--ink);">{{ $r->vaccine_name }}@if($r->vaccine_code) <span class="text-xs" style="color: var(--ink-muted);">({{ $r->vaccine_code }})</span>@endif</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3" style="color: var(--ink);">{{ $r->dose_number }}</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 hidden sm:table-cell" style="color: var(--ink-muted);">{{ $r->administered_by_name ?? '—' }}</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 hidden md:table-cell" style="color: var(--ink-muted);">
                                    @php
                                        $isOverdue = $r->next_due_date && \Carbon\Carbon::parse($r->next_due_date)->isBefore(\Carbon\Carbon::today());
                                    @endphp
                                    <div class="flex flex-col gap-1">
                                        <div>
                                            {{ $r->next_due_date ? \Carbon\Carbon::parse($r->next_due_date)->format('M d, Y') : '—' }}
                                        </div>
                                        @if ($isOverdue)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800 w-fit">
                                                Overdue
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 lg:px-4 py-6 text-center text-sm" style="color: var(--ink-muted);">No immunization records yet. Add one below.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </div>

    @push('modal-content')
        <div class="flex items-center justify-between border-b border-[var(--border)] p-5">
            <div>
                <h2 class="font-display font-semibold text-lg" style="color: var(--ink);">Add immunization record</h2>
                <p class="text-sm mt-1" style="color: var(--ink-muted);"></p>
            </div>
            <button type="button" onclick="closePageModal()" class="text-sm font-medium text-gray-600 hover:text-gray-900">Close</button>
        </div>

        <div class="p-5">
            <form action="{{ route('immunizations.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label for="vaccine_id" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Vaccine</label>
                        <select id="vaccine_id" name="vaccine_id" required class="w-full rounded-lg border py-2 px-3 text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                            <option value="">Select vaccine</option>
                            @foreach ($vaccines as $v)
                                <option value="{{ $v->id }}" @selected(old('vaccine_id') == $v->id)>{{ $v->vaccine_name }}@if($v->vaccine_code) ({{ $v->vaccine_code }})@endif</option>
                            @endforeach
                        </select>
                        @error('vaccine_id')<p class="mt-1 text-xs" style="color: var(--accent);">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="dose_number" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Dose number</label>
                        <input type="number" id="dose_number" name="dose_number" value="{{ old('dose_number', 1) }}" min="1" max="99" required class="w-full rounded-lg border py-2 px-3 text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                        @error('dose_number')<p class="mt-1 text-xs" style="color: var(--accent);">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="date_given" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Date given</label>
                        <input type="date" id="date_given" name="date_given" value="{{ old('date_given', date('Y-m-d')) }}" required class="w-full rounded-lg border py-2 px-3 text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                        @error('date_given')<p class="mt-1 text-xs" style="color: var(--accent);">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="administered_by" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Administered by</label>
                        <select id="administered_by" name="administered_by" class="w-full rounded-lg border py-2 px-3 text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                            <option value="">— Optional —</option>
                            @foreach ($healthWorkers as $hw)
                                <option value="{{ $hw->id }}" @selected(old('administered_by') == $hw->id)>{{ $hw->last_name }}, {{ $hw->first_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="next_due_date" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Next due date</label>
                        <input type="date" id="next_due_date" name="next_due_date" value="{{ old('next_due_date') }}" class="w-full rounded-lg border py-2 px-3 text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-xs font-medium mb-1" style="color: var(--ink-muted);">Notes</label>
                        <textarea id="notes" name="notes" rows="2" maxlength="500" class="w-full rounded-lg border py-2 px-3 text-sm focus:outline-none focus:ring-2 transition" style="border-color: var(--border); color: var(--ink); --tw-ring-color: var(--primary);">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closePageModal()" class="px-4 py-2 rounded-xl text-sm font-medium border border-gray-200 text-gray-700 hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background: #0e4a3c;">Save immunization record</button>
                </div>
            </form>
        </div>
    @endpush

    @push('scripts')
        <script>
            @if ($errors->any())
                document.addEventListener('DOMContentLoaded', function () {
                    openPageModal();
                });
            @endif
        </script>
    @endpush
    @endsection
</div>