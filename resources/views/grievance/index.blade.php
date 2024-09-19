@extends('layouts.admin')
@section('title', 'Grievances')
@section('action_buttons')
    <a href="{{ route('grievances.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300 ease-in-out">
        <i class="fas fa-plus"></i>
    </a>
@endsection
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Grievances</h2>
        
    </div>
    <div id="priority_filter_container" class="mb-6">
        <label for="priority_filter" class="block text-sm font-medium text-gray-700 mb-2">Filter by Priority:</label>
        <select id="priority_filter" class="block w-full max-w-xs bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">All</option>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
        </select>
    </div>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full" id="grievances_table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumer No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CA No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#grievances_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('grievances.index') }}',
                data: function (d) {
                    d.priority = $('#priority_filter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'consumer_no', name: 'consumer_no' },
                { data: 'ca_no', name: 'ca_no' },
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'category', name: 'category' },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function(data, type, row) {
                        var statusClasses = {
                            'Pending': 'bg-yellow-100 text-yellow-800',
                            'Forwarded': 'bg-blue-100 text-blue-800',
                            'Resolved': 'bg-green-100 text-green-800',
                            'Assigned': 'bg-orange-500 text-white-800',
                            'Closed': 'bg-red-100 text-red-800'
                        };
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' + 
                               (statusClasses[data] || 'bg-gray-100 text-gray-800') + '">' + data + '</span>';
                    }
                },
                { 
                    data: 'priority', 
                    name: 'priority',
                    render: function(data, type, row) {
                        var priorityClasses = {
                            'High': 'bg-red-100 text-red-800',
                            'Medium': 'bg-yellow-100 text-yellow-800',
                            'Low': 'bg-green-100 text-green-800'
                        };
                        return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' + 
                               (priorityClasses[data] || 'bg-gray-100 text-gray-800') + '">' + data + '</span>';
                    }
                },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        $('#priority_filter').on('change', function() {
            $('#grievances_table').DataTable().ajax.reload();
        });
    });
</script>
@endsection
