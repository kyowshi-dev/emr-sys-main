@extends('layouts.app')

@section('content')
<div class="space-y-5 lg:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Lab requests</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">Manage laboratory test requests.</p>
        </div>
        <a href="{{ route('lab_requests.create') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all duration-200 hover:opacity-95 active:scale-[0.98] shrink-0"
           style="background: var(--accent); box-shadow: 0 2px 8px rgba(196, 92, 65, 0.25);">
            + New lab request
        </a>
    </div>

    <div class="rounded-xl border overflow-hidden" style="background: var(--bg-surface-elevated); border-color: var(--border); box-shadow: var(--shadow-sm);">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-xs lg:text-sm">
                <thead>
                    <tr style="background: var(--teal-soft);">
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--ink-muted);">ID</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--ink-muted);">Patient</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--ink-muted);">Test</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--ink-muted);">Status</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap hidden md:table-cell" style="color: var(--ink-muted);">Requested</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider whitespace-nowrap hidden lg:table-cell" style="color: var(--ink-muted);">Requester</th>
                        <th class="px-4 py-3 font-semibold uppercase tracking-wider text-right whitespace-nowrap" style="color: var(--ink-muted);">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @forelse ($labRequests as $labRequest)
                        <tr class="transition-colors hover:bg-black/[0.02]">
                            <td class="px-4 py-2.5 font-medium whitespace-nowrap" style="color: var(--ink);">LR{{ str_pad($labRequest->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-4 py-2.5" style="color: var(--ink);">
                                <div class="font-medium">{{ $labRequest->last_name }}, {{ $labRequest->first_name }}</div>
                                <div class="text-xs" style="color: var(--ink-muted);">PT{{ str_pad($labRequest->patient_id, 3, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-4 py-2.5" style="color: var(--ink);">
                                <div class="font-medium">{{ $labRequest->lab_test_name }}</div>
                                @if ($labRequest->lab_test_description)
                                    <div class="text-xs" style="color: var(--ink-muted);">{{ Str::limit($labRequest->lab_test_description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if ($labRequest->status === 'completed') bg-green-100 text-green-800
                                    @elseif ($labRequest->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($labRequest->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap hidden md:table-cell" style="color: var(--ink-muted);">
                                {{ \Carbon\Carbon::parse($labRequest->requested_date)->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap hidden lg:table-cell" style="color: var(--ink-muted);">
                                {{ $labRequest->requester_first_name }} {{ $labRequest->requester_last_name }}
                            </td>
                            <td class="px-4 py-2.5 text-right whitespace-nowrap">
                                <a href="{{ route('lab_requests.show', $labRequest->id) }}" class="font-semibold text-sm transition-colors hover:underline" style="color: var(--primary);">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex justify-center mb-3"><i class="fa-solid fa-flask text-3xl" style="color: var(--ink-subtle);"></i></div>
                                <p class="text-sm font-medium" style="color: var(--ink);">No lab requests yet</p>
                                <p class="text-xs mt-1 mb-3" style="color: var(--ink-muted);">Create a new lab request to send tests for patient analysis</p>
                                <a href="{{ route('lab_requests.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-white transition hover:opacity-90" style="background: var(--accent);"><i class="fa-solid fa-plus"></i> New lab request</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($labRequests->hasPages())
            <div class="border-t px-4 py-3" style="border-color: var(--border);">
                {{ $labRequests->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>
@endsection