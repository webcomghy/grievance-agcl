@extends('layouts.admin')
@section('title', 'Dates')
@section('content')
    <h2 class="text-3xl font-bold text-gray-800">Dates</h2>
    <div class="flex space-x-4">
        <div class="flex-1 p-4 bg-white shadow rounded-lg"> <!-- Changed width to 1/4 for the form -->
            @include('meter_uploads.partials.set_month_and_date_form')
        </div>
        <div class="flex-1 p-4 bg-white shadow rounded-lg"> <!-- Changed width to 3/4 for the table -->
            @include('meter_uploads.partials.dates_table')
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
                pageLength: 5,
                columns: [
                    {data: 'month', name: 'month', render: function(data) { // 
                        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                        return months[data - 1]; // Convert month number to month name
                    }},
                    {data: 'year', name: 'year'},
                    {data: 'from_date', name: 'from_date'},
                    {data: 'to_date', name: 'to_date'},
                ]
            });
        });
    </script>
@endsection