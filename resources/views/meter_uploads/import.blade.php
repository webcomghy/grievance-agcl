@extends('layouts.admin')
@section('title', 'Import Previous Readings')
@section('content')
    <h2 class="text-3xl font-bold text-gray-800">Import Previous Readings</h2>
    <form action="{{ route('self_reading.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label for="file" class="block text-sm font-medium text-gray-700">Upload File</label>
            <input type="file" id="file" name="file" class="form-control mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">Import</button>
    </form>

    @if(session('success'))
        <div class="mt-4 text-green-500">{{ session('success') }}</div>
    @endif
@endsection
