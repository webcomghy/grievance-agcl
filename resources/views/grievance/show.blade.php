@extends('layouts.admin')

@section('title', 'Grievance Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Grievance Details</h1>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ $grievance->category }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p><strong>Consumer No:</strong> {{ $grievance->consumer_no }}</p>
                    <p><strong>CA No:</strong> {{ $grievance->ca_no }}</p>
                    <p><strong>Name:</strong> {{ $grievance->name }}</p>
                    <p><strong>Phone:</strong> {{ $grievance->phone }}</p>
                    <p><strong>Email:</strong> {{ $grievance->email }}</p>
                </div>
                <div>
                    <p><strong>Status:</strong> 
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $grievance->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($grievance->status === 'Forwarded' ? 'bg-blue-100 text-blue-800' : 
                               ($grievance->status === 'Resolved' ? 'bg-green-100 text-green-800' : 
                               'bg-red-100 text-red-800')) }}">
                            {{ $grievance->status }}
                        </span>
                    </p>
                    <p><strong>Priority:</strong> 
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $grievance->priority === 'High' ? 'bg-red-100 text-red-800' : 
                               ($grievance->priority === 'Medium' ? 'bg-yellow-100 text-yellow-800' : 
                               'bg-green-100 text-green-800') }}">
                            {{ $grievance->priority }}
                        </span>
                    </p>
                    <p><strong>Submitted on:</strong> {{ $grievance->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Description:</h3>
                <p class="text-gray-600">{{ $grievance->description }}</p>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-between">
        <a href="{{ route('grievances.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Back to List
        </a>
        
    </div>
</div>
@endsection
