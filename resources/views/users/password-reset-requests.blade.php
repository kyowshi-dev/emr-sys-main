@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-sky-700">Password Reset Requests</h1>
            <p class="text-sm text-gray-600">Review and complete requests submitted by users.</p>
        </div>
        <a href="{{ route('users.index') }}" class="text-sky-600 hover:underline text-sm">Back to User Management</a>
    </div>

    @if(session('success'))
        <div class="p-3 rounded bg-emerald-100 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="overflow-x-auto rounded-lg border bg-white p-2">
        <table class="min-w-full text-left text-sm text-gray-700">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2">Requested By</th>
                    <th class="px-3 py-2">Username</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Created</th>
                    <th class="px-3 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $req->user?->username ?? 'Unknown' }}</td>
                        <td class="px-3 py-2">{{ $req->username_requested }}</td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 text-xs rounded {{ $req->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-2">{{ $req->created_at->diffForHumans() }}</td>
                        <td class="px-3 py-2">
                            @if($req->status === 'pending')
                                <form action="{{ route('users.password-reset-requests.complete', ['passwordResetRequest' => $req->id]) }}" method="POST" class="flex gap-2 items-center">
                                    @csrf
                                    <input type="hidden" name="admin_note" value="Manual reset completed by admin." />
                                    <button type="submit" class="px-2 py-1 rounded text-xs bg-emerald-500 text-white hover:bg-emerald-600">Mark Completed</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-500">No action</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-gray-500">No password reset requests at this time.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $requests->links() }}
    </div>
</div>
@endsection