@extends('layouts.admin')

@section('title', 'Pending Grievances')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6">Pending Grievances</h2>

    <div class="mb-4">
        <label for="time_filter" class="block text-sm font-medium text-gray-700">Filter by Time:</label>
        <select id="time_filter" class="block w-full max-w-xs bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="">All</option>
            <option value="24_hours">Within 24 hours</option>
            <option value="48_hours">24 to 48 hours</option>
            <option value="above_48">Above 48 hours</option>
        </select>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full" id="grievances_table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumer No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#grievances_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('grievances.pending') }}',
                data: function (d) {
                    d.time_filter = $('#time_filter').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'consumer_no', name: 'consumer_no' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });

        $('#time_filter').on('change', function() {
            table.ajax.reload();
        });
    });
</script>
@endsection
@endsection