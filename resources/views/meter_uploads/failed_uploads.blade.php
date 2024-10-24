@extends('layouts.admin')

@section('title', 'Failed Uploads')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Failed Uploads</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200" id="failed_uploads_table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consumer No</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year - Month</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Reading</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Reading</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meter No</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Longitude</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latitude</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#failed_uploads_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('self_reading.failedlogs') }}',
            columns: [
                { data: null, name: 'id', orderable: false, searchable: false, render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1; // Calculate serial number
                }},
                { data: 'consumer_no', name: 'consumer_no' },
                { data: 'phone_number', name: 'phone_number' },
                { data: 'yearMonth', name: 'yearMonth' },
                { data: 'previousReading', name: 'previousReading' },
                { data: 'reading', name: 'reading' },
                { data: 'meter_no', name: 'meter_no' },
                { data: 'longitude', name: 'longitude' },
                { data: 'latitude', name: 'latitude' },
                { data: 'created_at', name: 'created_at', render: function(data) {
                    return new Date(data).toLocaleString('en-US', { 
                        year: 'numeric', month: 'short', day: 'numeric', 
                        hour: '2-digit', minute: '2-digit', hour12: false 
                    });
                }},
            ],
            dom: 'lBfrtip', // Add buttons to the DataTable
            buttons: [
                {
                    extend: 'copy',
                    text: 'Copy',
                    className: 'btn btn-primary'
                },
                {
                    extend: 'csv',
                    text: 'Export as CSV',
                    className: 'btn btn-primary'
                },
                {
                    extend: 'excel',
                    text: 'Export as Excel',
                    className: 'btn btn-primary'
                },
                {
                    extend: 'pdf',
                    text: 'Export as PDF',
                    className: 'btn btn-primary'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'btn btn-primary'
                }
            ]
        });
    });
</script>
@endsection
