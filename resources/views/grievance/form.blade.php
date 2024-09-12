@extends('layouts.admin')
@section('title', 'Add Grievance')
@section('content')
    <section id="grievance-form" class="p-4 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Create Grievance</h2>
        <form method="post" action="{{ route('grievances.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="is_grid_admin" value=1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="consumer_no" class="block text-sm font-medium mb-1">Consumer No</label>
                    <input type="text" id="consumer_no" name="consumer_no" required class="border rounded-md p-2 w-full">
                </div>
                <div>
                    <label for="ca_no" class="block text-sm font-medium mb-1">CA Number</label>
                    <input type="text" id="ca_no" name="ca_no" required class="border rounded-md p-2 w-full">
                </div>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium mb-1">Category</label>
                <select id="category" name="category" required class="border rounded-md p-2 w-full">
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" id="name" name="name" required class="border rounded-md p-2 w-full">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium mb-1">Phone</label>
                    <input type="text" id="phone" name="phone" required class="border rounded-md p-2 w-full">
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input type="email" id="email" name="email" required class="border rounded-md p-2 w-full">
            </div>

            <div>
                <label for="address" class="block text-sm font-medium mb-1">Address</label>
                <textarea id="address" name="address" required class="border rounded-md p-2 w-full" rows="3"></textarea>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium mb-1">Description</label>
                <textarea id="description" name="description" required class="border rounded-md p-2 w-full" rows="4"></textarea>
            </div>
           
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-sm uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Submit Grievance</button>
        </form>
    </section>

    <!-- Add loader element -->
    <div id="loader" class="hidden fixed inset-0 bg-gray-800 bg-opacity-25 flex items-center justify-center">
        <div class="loader"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
       
    </script>

    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection