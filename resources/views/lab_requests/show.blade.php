@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 lg:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl lg:text-2xl font-extrabold text-sky-700">Lab Request #{{ str_pad($labRequest->id, 3, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-xs lg:text-sm text-gray-600 mt-1">{{ $labRequest->patient_last_name }}, {{ $labRequest->patient_first_name }} (PT{{ str_pad($labRequest->patient_id, 3, '0', STR_PAD_LEFT) }})</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('lab_requests.edit', $labRequest) }}" class="px-3 py-1.5 text-xs lg:text-sm font-medium text-gray-600 hover:text-sky-600 border border-gray-300 rounded-lg">Edit</a>
            <a href="{{ route('lab_requests.index') }}" class="text-xs lg:text-sm font-medium text-gray-600 hover:text-sky-600">← Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
        <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 space-y-4">
            <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base">Request Details</h3>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Status</label>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                        @if ($labRequest->status === 'completed') bg-green-100 text-green-800
                        @elseif ($labRequest->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($labRequest->status) }}
                    </span>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Lab Test</label>
                    <p class="text-sm font-medium text-gray-800">{{ $labRequest->lab_test_name }}</p>
                    @if ($labRequest->lab_test_description)
                        <p class="text-xs text-gray-600 mt-1">{{ $labRequest->lab_test_description }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Requested Date</label>
                    <p class="text-sm text-gray-800">{{ $labRequest->requested_date->format('F j, Y') }}</p>
                </div>

                @if ($labRequest->completed_date)
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Completed Date</label>
                    <p class="text-sm text-gray-800">{{ $labRequest->completed_date->format('F j, Y') }}</p>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Requested By</label>
                    <p class="text-sm text-gray-800">{{ $labRequest->requester_first_name }} {{ $labRequest->requester_last_name }}</p>
                </div>

                @if ($labRequest->consultation_id)
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Related Consultation</label>
                    <a href="{{ route('consultations.show', $labRequest->consultation_id) }}" class="text-sm text-sky-600 hover:text-sky-800">Consultation #{{ $labRequest->consultation_id }}</a>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 space-y-4">
            <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base">Results & Notes</h3>

            @if ($labRequest->results)
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Results</label>
                <div class="text-sm text-gray-800 mt-1 p-3 bg-gray-50 rounded-lg whitespace-pre-wrap">{{ $labRequest->results }}</div>
            </div>
            @else
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Results</label>
                <p class="text-sm text-gray-500 italic">No results available yet</p>
            </div>
            @endif

            @if ($labRequest->notes)
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Notes</label>
                <div class="text-sm text-gray-800 mt-1 p-3 bg-gray-50 rounded-lg whitespace-pre-wrap">{{ $labRequest->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
        <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base mb-4">Patient Information</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Name</label>
                <p class="text-sm font-medium text-gray-800">{{ $labRequest->patient_last_name }}, {{ $labRequest->patient_first_name }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Patient ID</label>
                <p class="text-sm text-gray-800">PT{{ str_pad($labRequest->patient_id, 3, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase">Status</label>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                    @if ($labRequest->status === 'completed') bg-green-100 text-green-800
                    @elseif ($labRequest->status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($labRequest->status) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection