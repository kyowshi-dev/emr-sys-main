@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-4 lg:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl lg:text-2xl font-extrabold text-sky-700">Lab Request #{{ str_pad($labRequest->id, 3, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-xs lg:text-sm text-gray-600 mt-1">{{ $labRequest->patient_last_name }}, {{ $labRequest->patient_first_name }} (PT{{ str_pad($labRequest->patient_id, 3, '0', STR_PAD_LEFT) }})</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('lab_requests.pdf', $labRequest->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs lg:text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                Export PDF
            </a>
            <a href="{{ route('lab_requests.edit', $labRequest->id) }}" class="px-3 py-1.5 text-xs lg:text-sm font-medium text-gray-600 hover:text-sky-600 border border-gray-300 rounded-lg">Edit</a>
            <a href="{{ route('lab_requests.index') }}" class="text-xs lg:text-sm font-medium text-gray-600 hover:text-sky-600">← Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
        <!-- Left Column: Request Details -->
        <div class="space-y-4">
            <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 space-y-4">
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base">Request Information</h3>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Request ID</label>
                        <p class="text-sm font-semibold text-gray-800">LR{{ str_pad($labRequest->id, 3, '0', STR_PAD_LEFT) }}</p>
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

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Lab Test Name</label>
                    <p class="text-sm font-medium text-gray-800">{{ $labRequest->lab_test_name }}</p>
                </div>

                @if ($labRequest->lab_test_description)
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Test Description</label>
                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $labRequest->lab_test_description }}</p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-3 pt-2 border-t">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Requested Date</label>
                        <p class="text-sm text-gray-800">{{ $labRequest->requested_date->format('M d, Y') }}</p>
                    </div>
                    @if ($labRequest->completed_date)
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Completed Date</label>
                        <p class="text-sm text-gray-800">{{ $labRequest->completed_date->format('M d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Requested By</label>
                    <p class="text-sm text-gray-800">{{ $labRequest->requester_first_name }} {{ $labRequest->requester_last_name }}</p>
                </div>

                @if ($labRequest->consultation_id)
                <div class="pt-2 border-t">
                    <label class="block text-xs font-medium text-gray-500 uppercase">Related Consultation</label>
                    <a href="{{ route('consultations.show', $labRequest->consultation_id) }}" class="text-sm text-sky-600 hover:text-sky-800 font-medium">
                        Consultation #{{ str_pad($labRequest->consultation_id, 3, '0', STR_PAD_LEFT) }}
                    </a>
                </div>
                @endif
            </div>

            <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base mb-4">Test Results</h3>

                @if ($labRequest->results)
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Results</label>
                    <div class="text-sm text-gray-800 p-4 bg-gray-50 rounded-lg whitespace-pre-wrap font-mono border border-gray-200">{{ $labRequest->results }}</div>
                </div>
                @else
                <div>
                    <p class="text-sm text-gray-500 italic text-center py-4">No results available yet</p>
                </div>
                @endif

                @if ($labRequest->notes)
                <div class="pt-4 border-t">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-2">Notes</label>
                    <div class="text-sm text-gray-800 p-4 bg-gray-50 rounded-lg whitespace-pre-wrap">{{ $labRequest->notes }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Patient Information -->
        <div class="bg-white p-4 lg:p-6 rounded-xl lg:rounded-2xl shadow-sm border border-gray-200 space-y-6">
            <div>
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base">Patient Information</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase">Full Name</label>
                    <p class="text-lg font-bold text-gray-800">{{ $labRequest->patient_last_name }}, {{ $labRequest->patient_first_name }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Patient ID</label>
                        <p class="text-sm font-semibold text-gray-800">PT{{ str_pad($labRequest->patient_id, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Request Status</label>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            @if ($labRequest->status === 'completed') bg-green-100 text-green-800
                            @elseif ($labRequest->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($labRequest->status) }}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-xs text-gray-600 uppercase mb-3">Quick Reference</h4>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Request Date:</dt>
                            <dd class="font-medium text-gray-800">{{ $labRequest->requested_date->format('M d, Y') }}</dd>
                        </div>
                        @if ($labRequest->completed_date)
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Completed:</dt>
                            <dd class="font-medium text-gray-800">{{ $labRequest->completed_date->format('M d, Y') }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-300">
                            <dt class="text-gray-600">Requested By:</dt>
                            <dd class="font-medium text-gray-800">{{ $labRequest->requester_first_name }} {{ $labRequest->requester_last_name }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h4 class="font-semibold text-xs text-blue-600 uppercase mb-2">Lab Test Details</h4>
                    <p class="text-sm font-semibold text-blue-900">{{ $labRequest->lab_test_name }}</p>
                    @if ($labRequest->lab_test_description)
                    <p class="text-xs text-blue-800 mt-2">{{ $labRequest->lab_test_description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection