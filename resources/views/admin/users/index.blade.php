@extends('admin.layouts.app')

@section('title', 'Users - Admin')
@section('page-title', 'Users')

@section('content')

<div class="admin-card">
    <div class="admin-card-title">👥 Registered Users ({{ $users->count() }})</div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Matric ID</th>
                <th>Email</th>
                <th>Registered</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td style="font-weight:700;">{{ $user->name }}</td>
                <td><span class="badge badge-blue">{{ $user->matric_id }}</span></td>
                <td style="color:#888;">{{ $user->email }}</td>
                <td style="color:#888; font-size:12px;">{{ $user->created_at->format('d M Y') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                        onsubmit="return confirm('Delete {{ $user->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; color:#888;">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection