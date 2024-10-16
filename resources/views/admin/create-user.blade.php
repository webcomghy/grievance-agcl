@extends('layouts.admin')

@section('title', 'Users')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">
                Users 
            </h2>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Creation Form -->
                    <div>
                        <h2 class="text-xl font-semibold mb-2">Create User</h2>
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" id="username" name="username" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" id="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="mb-4">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div class="mb-4">
                                <label for="grid_id" class="block text-sm font-medium text-gray-700">Select Grid</label>
                                <select id="grid_id" name="grid_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Grid</option>
                                    @foreach($gridMaster as $grid)
                                        <option value="{{ $grid->GRID_ID }}">{{ $grid->GRID_ID }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-sm uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Create User
                                </button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold mb-2">Existing Users</h2>
                        <table id="usersTable" class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Username</th>
                                    <th class="py-2 px-4 border-b">Created At</th>
                                    <th class="py-2 px-4 border-b">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('users.index') }}',
                columns: [
                    { data: 'username', name: 'username' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                pageLength: 5
            });
        });

        function deleteUser(username) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route("users.destroy", ":username") }}'.replace(':username', username),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}' 
                    },
                    success: function(response) {
                        alert('User deleted successfully.');
                        $('#usersTable').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert('Error deleting user: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>
    @endsection
@endsection
