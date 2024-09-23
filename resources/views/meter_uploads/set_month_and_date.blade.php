@extends('layouts.admin')

@section('title', 'Set Month and Date')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Set Month and Date</h2>
    <div class="flex space-x-4">
        <div class="flex-1 p-4 bg-white shadow rounded-lg">
            @include('meter_uploads.partials.set_month_and_date_form')
        </div>
        <div class="flex-1 p-4 bg-white shadow rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Availability Dates</h3>
            <table class="min-w-full" id="availability_dates_table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Date</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#availability_dates_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('meter_uploads.set_dates') }}",
                columns: [
                    {data: 'month', name: 'month'},
                    {data: 'year', name: 'year'},
                    {data: 'from_date', name: 'from_date'},
                    {data: 'to_date', name: 'to_date'},
                ]
            });
        });
    </script>
@endsection