@extends('layouts.admin')
@section('content')
<div class="p-6">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 ease-in-out transform hover:scale-105">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700">Closed Grievances</h3>
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $closed }}</p>
            <p class="text-sm text-gray-500 mt-2">Total closed cases</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 ease-in-out transform hover:scale-105">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700">Resolved Grievances</h3>
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                    </path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $resolved }}</p>
            <p class="text-sm text-gray-500 mt-2">Successfully resolved</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 ease-in-out transform hover:scale-105">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700">Open Grievances</h3>
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $inprogress ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-2">In progress</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 ease-in-out transform hover:scale-105">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700">Total Grievances </h3>
                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $total }}</p>
            <p class="text-sm text-gray-500 mt-2">All time</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 ease-in-out transform hover:scale-105">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700">Unattended Grievances</h3>
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $unread }}</p>
            <p class="text-sm text-gray-500 mt-2">Awaiting attention</p>
        </div>


        <div class="bg-white rounded-lg shadow-md p-6 transition duration-300 ease-in-out transform hover:scale-105">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700">Avg. Resolution Time</h3>
                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $averageTimeFormatted ?? '3d 4h' }}</p>
            <p class="text-sm text-gray-500 mt-2">Time to resolve</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($recentFive as $recent)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $recent->ticket_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $recent->category }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ 
                                        $recent->status === 'Pending' 
                                        || $recent->status === 'Forwarded' 
                                        || $recent->status === 'Assigned' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'  
                                    }}">{{ $recent->status }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $recent->created_at }}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection