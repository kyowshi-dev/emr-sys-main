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
            <h1 class="text-xl lg:text-2xl font-extrabold text-sky-700">Edit Lab Request</h1>
            <p class="text-xs lg:text-sm text-gray-600 mt-1">LR{{ str_pad($labRequest->id, 3, '0', STR_PAD_LEFT) }} - {{ $labRequest->patient->last_name }}, {{ $labRequest->patient->first_name }}</p>
        </div>
        <a href="{{ route('lab_requests.show', $labRequest->id) }}" class="text-xs lg:text-sm font-medium text-gray-600 hover:text-sky-600">← Back</a>
    </div>

    <form action="{{ route('lab_requests.update', $labRequest->id) }}" method="POST" class="bg-white p-4 lg:p-6 xl:p-8 rounded-xl lg:rounded-lg shadow-sm border border-gray-200 space-y-6 lg:space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base">Test Details</h3>

                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Lab Test Name <span class="text-red-500">*</span></label>
                    <input type="text" name="lab_test_name" value="{{ old('lab_test_name', $labRequest->lab_test_name) }}" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm" required>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Test Description</label>
                    <textarea name="lab_test_description" rows="3" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm">{{ old('lab_test_description', $labRequest->lab_test_description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm" required>
                        <option value="pending" @selected(old('status', $labRequest->status) === 'pending')>Pending</option>
                        <option value="completed" @selected(old('status', $labRequest->status) === 'completed')>Completed</option>
                        <option value="cancelled" @selected(old('status', $labRequest->status) === 'cancelled')>Cancelled</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Completed Date</label>
                    <input type="date" name="completed_date" value="{{ old('completed_date', $labRequest->completed_date?->format('Y-m-d')) }}" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="font-bold text-gray-800 pb-2 border-b border-gray-100 text-sm lg:text-base">Results & Notes</h3>

                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Results</label>
                    <textarea name="results" rows="4" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm" placeholder="Enter test results here">{{ old('results', $labRequest->results) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs lg:text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 lg:px-4 py-2 lg:py-2.5 rounded-xl border border-gray-300 focus:ring-sky-500 focus:border-sky-500 text-sm" placeholder="Additional notes">{{ old('notes', $labRequest->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('lab_requests.show', $labRequest->id) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Cancel</a>
            <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-xl transition">Update Lab Request</button>
        </div>
    </form>
</div>
@endsection