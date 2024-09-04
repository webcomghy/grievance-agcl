@extends('layouts.admin')

@section('title', 'Grievance Details')

@section('action_buttons')
    <span class="text-gray-600 mr-4">
        <i class="far fa-clock"></i>
        {{ $grievance->created_at->diffForHumans() }} has passed since the submission
    </span>
    
    <button onclick="openForwardModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
        <i class="fas fa-forward"></i>
    </button>
    <button onclick="printGrievanceCard()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
        <i class="fas fa-print"></i>
    </button>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Grievance Details</h1>

    <div class="flex flex-col lg:flex-row gap-6">
        <div id="grievanceCard" class="bg-white shadow-md rounded-lg overflow-hidden lg:w-2/3">
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

        <!-- Notesheet Timeline (Improved padding) -->
        <div class="w-full lg:w-1/3 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-8">
                <h3 class="text-2xl font-semibold text-gray-800 mb-8">Forwarding Timeline</h3>
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        <li>
                            <div class="relative pb-10">
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex items-start space-x-4">
                                    <div>
                                        <span class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-forward text-white text-lg"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 py-1.5">
                                        <div class="text-sm font-medium text-gray-900 mb-1">Forwarded to Department 2</div>
                                        <div class="text-sm text-gray-700 mb-2">
                                            <p>Additional details about the forwarding can go here.</p>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <time datetime="2023-03-15">Mar 15, 2023</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="relative pb-10">
                                <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <div class="relative flex items-start space-x-4">
                                    <div>
                                        <span class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-forward text-white text-lg"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 py-1.5">
                                        <div class="text-sm font-medium text-gray-900 mb-1">Forwarded to Department 1</div>
                                        <div class="text-sm text-gray-700 mb-2">
                                            <p>Additional details about the forwarding can go here.</p>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <time datetime="2023-01-13">Jan 13, 2023</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="relative">
                                <div class="relative flex items-start space-x-4">
                                    <div>
                                        <span class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-check text-white text-lg"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 py-1.5">
                                        <div class="text-sm font-medium text-gray-900 mb-1">Grievance Received</div>
                                        <div class="text-sm text-gray-700 mb-2">
                                            <p>Initial grievance details can be added here.</p>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <time datetime="2023-01-10">Jan 10, 2023</time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-between">
        <a href="{{ route('grievances.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Back to List
        </a>
        
    </div>
</div>

<!-- Forward Modal -->
<div id="forwardModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('grievances.update', $grievance) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Forward Grievance
                            </h3>
                            <div class="mt-2">
                                <div class="mb-4">
                                    <label for="forward_to" class="block text-sm font-medium text-gray-700">Forward To</label>
                                    <select id="forward_to" name="forward_to" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">Select Department</option>
                                        <option value="dept1">Department 1</option>
                                        <option value="dept2">Department 2</option>
                                        <option value="dept3">Department 3</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Submit
                    </button>
                    <button type="button" onclick="closeForwardModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #grievanceCard, #grievanceCard * {
            visibility: visible;
        }
        #grievanceCard {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>

<script>
    function openForwardModal() {
        document.getElementById('forwardModal').classList.remove('hidden');
    }

    function closeForwardModal() {
        document.getElementById('forwardModal').classList.add('hidden');
    }

    function printGrievanceCard() {
        window.print();
    }
</script>

@endsection
