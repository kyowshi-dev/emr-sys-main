@extends('layouts.app')

@section('title', 'Zones')

@section('content')
<div class="space-y-5 lg:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="font-display font-semibold text-2xl lg:text-3xl" style="color: var(--ink);">Zones</h1>
            <p class="text-sm mt-1" style="color: var(--ink-muted);">Manage geographic zones and assign health workers.</p>
        </div>
        <a href="{{ route('zones.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition duration-200 hover:shadow-md" style="background: var(--accent);">
            Add zone
        </a>
    </div>

    @if (session('success'))
        <div class="rounded-xl border px-4 py-3" style="background: var(--teal-soft); border-color: var(--primary); color: var(--primary);">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border px-4 py-3" style="background: var(--red-soft); border-color: var(--red); color: var(--red);">
            {{ session('error') }}
        </div>
    @endif

    <div class="lg:col-span-2">
        <div class="rounded-xl border overflow-hidden" style="background: var(--bg-surface-elevated); border-color: var(--border);">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead style="background: var(--teal-soft);">
                        <tr>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap" style="color: var(--ink-muted);">Zone Number</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap hidden sm:table-cell" style="color: var(--ink-muted);">Assigned Worker</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-left text-xs font-medium whitespace-nowrap hidden md:table-cell" style="color: var(--ink-muted);">Households</th>
                            <th class="px-3 lg:px-4 py-2 lg:py-3 text-right text-xs font-medium whitespace-nowrap" style="color: var(--ink-muted);"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse ($zones as $zone)
                            <tr class="transition-colors hover:bg-black/[0.02]">
                                <td class="px-3 lg:px-4 py-2 lg:py-3" style="color: var(--ink);">{{ $zone->zone_number }}</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 hidden sm:table-cell" style="color: var(--ink-muted);">{{ $zone->worker_name ?? '—' }}</td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 hidden md:table-cell" style="color: var(--ink-muted);">
                                    @php
                                        $householdCount = DB::table('households')->where('zone_id', $zone->id)->count();
                                    @endphp
                                    {{ $householdCount }}
                                </td>
                                <td class="px-3 lg:px-4 py-2 lg:py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('zones.show', $zone->id) }}" class="text-sm font-medium hover:underline" style="color: var(--primary);">View</a>
                                    <span class="mx-2" style="color: var(--ink-muted);">·</span>
                                    <a href="{{ route('zones.edit', $zone->id) }}" class="text-sm font-medium hover:underline" style="color: var(--primary);">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 lg:px-4 py-6 text-center text-sm" style="color: var(--ink-muted);">No zones yet. Add one above.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($zones->hasPages())
            <div class="mt-4">
                {{ $zones->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
