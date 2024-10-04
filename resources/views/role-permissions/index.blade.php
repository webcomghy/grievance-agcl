@extends('layouts.admin')

@section('styles')
<style>
    .loader {
        border: 8px solid #f3f3f3;
        border-top: 8px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    #fullScreenLoader {
        backdrop-filter: blur(5px);
    }
</style>
@endsection
@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-4">Roles and Permissions Management</h2>

    <div class="mb-4">
        <ul class="flex border-b">
            <li class="-mb-px mr-1">
                <a class="bg-white inline-block border-l border-t border-r rounded-t py-2 px-4 text-blue-700 font-semibold" href="#" onclick="showTab('manage')">Manage</a>
            </li>
            <li class="mr-1">
                <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold" href="#" onclick="showTab('view')">View Assignments</a>
            </li>
        </ul>
    </div>

    <div id="manageTab" class="tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Roles Section -->
            <div>
                <h3 class="text-xl font-semibold mb-2">Roles</h3>
                <ul class="list-disc pl-5 mb-4">
                    @foreach($roles as $role)
                        <li>{{ $role->name }}</li>
                    @endforeach
                </ul>
                <form action="{{ route('roles.create') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="text" name="name" placeholder="New Role Name" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <button type="submit" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Create Role</button>
                </form>
            </div>

            <!-- Permissions Section -->
            <div>
                <h3 class="text-xl font-semibold mb-2">Permissions</h3>
                <ul class="list-disc pl-5 mb-4">
                    @foreach($permissions as $permission)
                        <li>{{ $permission->name }}</li>
                    @endforeach
                </ul>
                <form action="{{ route('permissions.create') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="text" name="name" placeholder="New Permission Name" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <button type="submit" class="mt-2 bg-green-500 text-white px-4 py-2 rounded">Create Permission</button>
                </form>
            </div>

            <!-- Assign Role to User -->
            <div>
                <h3 class="text-xl font-semibold mb-2">Assign Role to User(s)</h3>
                <form action="{{ route('roles.assign') }}" method="POST">
                    @csrf
                    <select name="user_ids[]" id="userSelect" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                        @endforeach
                    </select>
                    <select name="roles[]" id="roleAssignSelect" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" multiple>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="mt-2 bg-purple-500 text-white px-4 py-2 rounded w-full">Assign Role(s) to User(s)</button>
                </form>
            </div>

            <!-- Assign Permission to Role -->
            <div>
                <h3 class="text-xl font-semibold mb-2">Assign Permission to Role</h3>
                <form action="{{ route('permissions.assign') }}" method="POST">
                    @csrf
                    <select name="role" id="roleSelect" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <select name="permissions[]" id="permissionSelect" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" multiple>
                        @foreach($permissions as $permission)
                            <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="mt-2 bg-yellow-500 text-white px-4 py-2 rounded w-full">Assign Permission(s)</button>
                </form>
                <div id="permissionLoader" class="hidden">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="viewTab" class="tab-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Users and Their Roles -->
            <div>
                <h3 class="text-xl font-semibold mb-2">Users and Their Roles</h3>
                <table id="usersTable" class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Username</th>
                            <th class="py-2 px-4 border-b">Roles</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <!-- Roles and Their Permissions -->
            <div>
                <h3 class="text-xl font-semibold mb-2">Roles and Their Permissions</h3>
                <ul class="list-disc pl-5 mb-4">
                    @foreach($roles as $role)
                        <li>
                            {{ $role->name }}
                            <ul class="list-circle pl-5">
                                @foreach($role->permissions as $permission)
                                    <li>{{ $permission->name }}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div id="fullScreenLoader" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="loader"></div>
</div>

@endsection

@section('scripts')
<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    document.getElementById(tabName + 'Tab').classList.remove('hidden');

    document.querySelectorAll('ul.flex li a').forEach(link => {
        link.classList.remove('text-blue-700', 'border-l', 'border-t', 'border-r', 'rounded-t');
        link.classList.add('text-blue-500', 'hover:text-blue-800');
    });
    event.target.classList.remove('text-blue-500', 'hover:text-blue-800');
    event.target.classList.add('text-blue-700', 'border-l', 'border-t', 'border-r', 'rounded-t');
}

$(document).ready(function() {

    Swal.fire({
        title: 'Instructions',
        html: 'To assign role to user, hold the shift key and click on user(s) and then click on role(s).<br><br><strong>Please reselect previous user roles otherwise roles will be removed.</strong><br><br>To assign permission to role, select a role from the drop down and hold shift to select multiple roles.',
        icon: 'info',
        confirmButtonText: 'Got it!'
    });
    $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("users.with.roles") }}',
        columns: [
            { data: 'username', name: 'username' },
            { data: 'roles', name: 'roles' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });
});

function removeRole(userId, roleName) {
    if (confirm('Are you sure you want to remove this role?')) {
        $.ajax({
            url: '{{ route("roles.remove") }}',
            type: 'POST',
            data: {
                user_id: userId,
                role: roleName,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                alert(response.message);
                $('#usersTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseText);
            }
        });
    }
}

// Function to update permissions based on selected role
function updatePermissions() {
    var roleId = $('#roleSelect').val();
    $('#fullScreenLoader').removeClass('hidden');

    $.ajax({
        url: '{{ route("roles.permissions") }}',
        type: 'GET',
        data: { role: roleId },
        success: function(permissions) {
            $('#permissionSelect option').prop('selected', false);
            permissions.forEach(function(permission) {
                $('#permissionSelect option[value="' + permission + '"]').prop('selected', true);
            });
        },
        error: function() {
            alert('An error occurred while fetching permissions.');
        },
        complete: function() {
            $('#fullScreenLoader').addClass('hidden');
        }
    });
}

$(document).ready(function() {
    updatePermissions();
    $('#roleSelect').change(updatePermissions);
});
</script>
@endsection
