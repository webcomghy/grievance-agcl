@extends('layouts.admin')
@section('title', 'Edit Availability Date')

@section('content')
    <h2 class="text-3xl font-bold text-gray-800">Edit Availability Date</h2>
    <form action="{{ route('meter_uploads.update', $availabilityDate->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="month" name="month" required class="mt-1 block w-full">
                    @foreach ($months as $key => $month)
                        <option value="{{ $key }}" {{ $availabilityDate->month == $key ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                <select class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="year" name="year" required class="mt-1 block w-full">
                    @foreach ($years as $year)
                        <option value="{{ $year }}" {{ $availabilityDate->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                <input class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" type="date" id="from_date" name="from_date" value="{{ $availabilityDate->from_date }}" required class="mt-1 block w-full">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                <input class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" type="date" id="to_date" name="to_date" value="{{ $availabilityDate->to_date }}" required class="mt-1 block w-full">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Update</button>
        </div>
    </form>
@endsection
