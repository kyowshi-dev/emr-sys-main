@extends('layouts.app')

@section('content')
<div class="space-y-4 lg:space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-sky-700">User Management</h1>
            <p class="text-xs lg:text-sm text-gray-600 mt-1">
                View and manage all registered users in the system.
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="inline-flex items-center justify-center px-4 lg:px-5 py-2 lg:py-2.5 rounded-xl lg:rounded-2xl bg-[var(--primary)] text-xs lg:text-sm font-semibold text-white shadow-md hover:bg-[var(--primary-light)] transition">
            + Add User
</a>
    </div>

    <div class="overflow-hidden rounded-xl lg:rounded-2xl border border-gray-200 bg-white/80 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th class="px-3 lg:px-6 py-2 lg:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Username</th>
                        <th class="px-3 lg:px-6 py-2 lg:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap hidden md:table-cell">Email</th>
                        <th class="px-3 lg:px-6 py-2 lg:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap hidden lg:table-cell">Registered At</th>
                        <th class="px-3 lg:px-6 py-2 lg:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-3 lg:px-6 py-2 lg:py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-sky-50/60 transition-colors">
                            <td class="px-3 lg:px-6 py-2 lg:py-3 text-sm text-gray-700">
                                <div class="font-medium">{{ $user->username }}</div>
                                <div class="text-xs text-gray-500 md:hidden">{{ $user->email }}</div>
                            </td>
                            <td class="px-3 lg:px-6 py-2 lg:py-3 text-sm text-gray-500 hidden md:table-cell">
                                {{ $user->email }}
                            </td>
                            <td class="px-3 lg:px-6 py-2 lg:py-3 text-sm text-gray-500 hidden lg:table-cell">
                                {{ $user->created_at?->format('M d, Y') }}
                            </td>
                            <td class="px-3 lg:px-6 py-2 lg:py-3 text-sm">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center px-2 lg:px-3 py-0.5 lg:py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 lg:px-3 py-0.5 lg:py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                                        Disabled
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 lg:px-6 py-2 lg:py-3 text-sm text-right">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="inline-flex items-center px-2 lg:px-3 py-1 lg:py-1.5 rounded-full border border-sky-300 text-xs font-semibold text-sky-600 hover:bg-sky-50 transition">
                                        Edit
                                    </a>
                                    <a href="{{ route('users.permissions.edit', $user) }}"
                                       class="inline-flex items-center px-2 lg:px-3 py-1 lg:py-1.5 rounded-full border border-purple-300 text-xs font-semibold text-purple-600 hover:bg-purple-50 transition">
                                        Permissions
                                    </a>
                                    @if ($user->is_active && ! $user->isAdmin())
                                        <form action="{{ route('users.disable', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex items-center px-2 lg:px-3 py-1 lg:py-1.5 rounded-full border border-red-300 text-xs font-semibold text-red-600 hover:bg-red-50 transition"
                                                onclick="return confirm('Are you sure you want to disable this user?');"
                                            >
                                                Disable
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
