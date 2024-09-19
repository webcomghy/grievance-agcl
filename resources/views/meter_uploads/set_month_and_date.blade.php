@extends('layouts.admin')

@section('title', 'Set Month and Date')

@section('content')
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Set Month and Date</h2>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('meter_uploads.partials.set_month_and_date_form')
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')

    <script>

    </script>
@endsection