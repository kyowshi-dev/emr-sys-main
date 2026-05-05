@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 lg:space-y-6">
    @if ($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 px-3 lg:px-4 py-2 lg:py-3 text-xs lg:text-sm text-red-800">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl lg:text-2xl font-extrabold text-sky-700">New Consultation</h1>
            <p class="text-xs lg:text-sm text-gray-600 mt-1">Admitting <span class="font-semibold text-gray-800">{{ $patient->last_name }}, {{ $patient->first_name }}</span> (PT{{ str_pad($patient->id, 3, '0', STR_PAD_LEFT) }})</p>
        </div>
        <a href="{{ route('patients.show', $patient->id) }}" class="text-xs lg:text-sm font-medium text-gray-600 hover:text-sky-600">← Back</a>
    </div>

    <form action="{{ route('consultations.store', $patient->id) }}" method="POST" class="space-y-4 lg:space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
            <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-3 lg:mb-4 pb-2 border-b border-gray-100 text-sm lg:text-base">1. Visit details</h3>

                <div class="mb-3 lg:mb-4">
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Nature of visit <span class="text-red-500">*</span></label>
                    <select name="nature_of_visit" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm" required>
                        <option value="Checkup" @selected(old('nature_of_visit') === 'Checkup')>General checkup</option>
                        <//option value="Prenatal" @selected(old('nature_of_visit') === 'Prenatal')>Prenatal</option>
                        <option value="Immunization" @selected(old('nature_of_visit') === 'Immunization')>Immunization</option>
                        <option value="Emergency" @selected(old('nature_of_visit') === 'Emergency')>Emergency / injury</option>
                        <option value="Follow-up" @selected(old('nature_of_visit') === 'Follow-up')>Follow-up</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Chief complaint (optional)</label>
                    <textarea name="chief_complaint" rows="3" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm" placeholder="e.g. Fever 3 days, cough">{{ old('chief_complaint') }}</textarea>
                </div>
            </div>

            <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-3 lg:mb-4 pb-2 border-b border-gray-100 text-sm lg:text-base">2. Vital signs</h3>

                <div class="grid grid-cols-2 gap-3 lg:gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Blood pressure (mmHg)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="bp_systolic" value="{{ old('bp_systolic') }}" min="0" max="300" placeholder="120" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 text-center text-sm focus:ring-sky-500 focus:border-sky-500">
                            <span class="text-gray-400">/</span>
                            <input type="number" name="bp_diastolic" value="{{ old('bp_diastolic') }}" min="0" max="200" placeholder="80" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 text-center text-sm focus:ring-sky-500 focus:border-sky-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Temperature (°C) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.1" name="temperature" value="{{ old('temperature') }}" min="30" max="45" placeholder="36.5" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 text-center text-sm focus:ring-sky-500 focus:border-sky-500" required>
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight" value="{{ old('weight') }}" min="0" max="500" placeholder="—" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 text-center text-sm focus:ring-sky-500 focus:border-sky-500">
                    </div>

                    <div>
                        <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Height (cm)</label>
                        <input type="number" step="0.1" name="height" value="{{ old('height') }}" min="0" max="300" placeholder="—" class="w-full px-3 lg:px-4 py-2 rounded-xl border border-gray-300 text-center text-sm focus:ring-sky-500 focus:border-sky-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 lg:gap-3">
            <a href="{{ route('patients.show', $patient->id) }}" class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium text-xs lg:text-sm hover:bg-[var(--primary-light)]">Cancel</a>
            <button type="submit" class="px-5 lg:px-6 py-2 lg:py-2.5 rounded-xl bg-[var(--primary)] text-white font-semibold text-xs lg:text-sm shadow-md hover:bg-[var(--primary-light)] transition">
                Save & send to doctor
            </button>
        </div>
    </form>
</div>
@endsection
