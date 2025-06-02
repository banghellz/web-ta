<!-- resources/views/admin/users/detail-partial.blade.php -->
<div class="user-details">
    <div class="row g-3">
        <div class="col-12">
            <h4 class="mb-3">User Information</h4>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 30%">Name</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>
                <tr>
                    <th>Registration Date</th>
                    <td>{{ $user->created_at->format('d M Y, H:i') }}</td>
                </tr>
                <tr>
                    <th>Last Login</th>
                    <td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Never logged in' }}</td>
                </tr>
            </table>
        </div>

        @if ($user->detail)
            <div class="col-12">
                <h4 class="mb-3">Student Details</h4>
                <table class="table table-bordered">
                    <!-- Assuming UserDetail model has these fields -->
                    <tr>
                        <th style="width: 30%">NIM</th>
                        <td>{{ $user->detail->nim ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Coin Number</th>
                        <td>{{ $user->detail->no_koin ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Program Studi</th>
                        <td>{{ $user->detail->prodi ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    No student details available for this user.
                </div>
            </div>
        @endif
    </div>
</div>
