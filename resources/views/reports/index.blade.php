@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold mb-6">{{$status}} Grievances</h2>

    <div class="mb-4 flex items-center">
        <div class="mr-4 flex-grow">
            <label class="block text-sm font-medium text-gray-700">Filter by Category:</label>
            <div class="flex flex-wrap">
                @foreach (App\Models\Grievance::$categories as $category => $subcategories)
                    <button class="category-filter bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-1 px-2 rounded mr-2" data-category="{{ $category }}">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Filter by Time:</label>
            <div class="flex flex-wrap">
                <button class="time-filter bg-blue-100 hover:bg-blue-300 text-blue-800 font-semibold py-1 px-2 rounded mr-2" data-time="24_hours">Within 24 hours</button>
                <button class="time-filter bg-yellow-100 hover:bg-yellow-300 text-yellow-800 font-semibold py-1 px-2 rounded mr-2" data-time="48_hours">24 to 48 hours</button>
                <button class="time-filter bg-red-100 hover:bg-red-300 text-red-800 font-semibold py-1 px-2 rounded" data-time="above_48">Above 48 hours</button>
            </div>
        </div>

      <!-- Added Reset Filter Button -->
      <div>
        <label class="block text-sm font-medium text-gray-700">Reset:</label>
        <button id="reset-filters" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-1 px-2 rounded ml-4">Reset Filters</button>
        <!-- End of added code -->
      </div>
    </div>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full" id="grievances_table">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumer No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CA No</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sub-Category</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grid Code</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted At</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#grievances_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('reports.index', ['status' => $status]) }}',
                data: function (d) {
                    d.category = $('.category-filter.active').data('category'); // Get selected category
                    d.time_filter = $('.time-filter.active').data('time'); // Get selected time filter
                }
            },
            columns: [
                { data: null, name: 'id', orderable: false, searchable: false, render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; // Calculate serial number
                }},
                { data: 'ticket_number', name: 'ticket_number' },
                { data: 'consumer_no', name: 'consumer_no' },
                { data: 'ca_no', name: 'ca_no' },
                { data: 'category', name: 'category' },
                { data: 'subcategory', name: 'sub_category' },
                { data: 'name', name: 'name' },
                { data: 'phone', name: 'phone' },
                { data: 'priority', name: 'priority', render: function(data) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' + (data === 'High' ? 'bg-red-100 text-red-800' : (data === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800')) + '">' + data + '</span>';
                }},
                { data: 'grid_code', name: 'grid_code' },
                { data: 'status', name: 'status', render: function(data) {
                    return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' + (data === 'Pending' ? 'bg-yellow-100 text-yellow-800' : (data === 'Resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) + '">' + data + '</span>';
                }},
                { data: 'created_at', name: 'created_at', render: function(data) {
                    return new Date(data).toLocaleString('en-US', { 
                        year: 'numeric', month: 'short', day: 'numeric', 
                        hour: '2-digit', minute: '2-digit', hour12: false 
                    }); // Format date to "Sep 21, 2024 06:44"
                }},
            ],
            dom: 'lBfrtip',// Add buttons to the DataTable
            buttons: [
                'copy', // Copy button
                'csv',  // CSV export button
                'excel', // Excel export button
                'pdf',  // PDF export button
                'print', // Print button
                'colvis' // Column visibility button
            ]
        });

        $('.category-filter').on('click', function() {
            $('.category-filter').removeClass('active');
            $(this).addClass('active');
            table.ajax.reload();
        });

        $('.time-filter').on('click', function() {
            $('.time-filter').removeClass('active');
            $(this).addClass('active');
            table.ajax.reload();
        });

        // Reset filters functionality
        $('#reset-filters').on('click', function() {
            $('.category-filter').removeClass('active'); // Remove active class from category filters
            $('.time-filter').removeClass('active'); // Remove active class from time filters
            table.ajax.reload(); // Reload the table data
        });
    });
</script>
@endsection
