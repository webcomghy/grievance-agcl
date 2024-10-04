<x-guest-layout>
    @section('title', 'Submit a Grievance')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="text-center mb-6">
                        <img src="{{ asset('logo.jpg') }}" alt="Logo" class="mx-auto h-16 w-auto">
                    </div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Add your Grievance</h2>
                    <form method="post" action="{{ route('grievances.store') }}">
                        @csrf
                        <input type="hidden" name="is_grid_admin" value=0>
                        <div class="mb-4 flex space-x-4">
                            <div class="w-1/2">
                                <label for="consumer_no" class="block text-sm font-medium text-gray-700">Consumer No</label>
                                <input type="text" id="consumer_no" name="consumer_no" class="border border-gray-300 p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="w-1/2">
                                <label for="ca_no" class="block text-sm font-medium text-gray-700">CA Number</label>
                                <input type="text" id="ca_no" name="ca_no" class="border border-gray-300 p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="mb-4 flex space-x-4">
                            <div class="w-1/2">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" id="name" name="name" required class="border border-gray-300 p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="w-1/2">
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" id="phone" name="phone" required class="border border-gray-300 p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" class="border border-gray-300 p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" required class="border border-gray-300 p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                             <textarea id="address" name="address" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" required class="border border-gray-300 p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Check if Geolocation is supported
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                // Set the latitude and longitude values
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
            }, function() {
                // Handle error if user denies location access
                console.error("Geolocation access denied.");
            });
        } else {
            console.error("Geolocation is not supported by this browser.");
        }
    </script>

<script>
    // Show SweetAlert to request location permission
    window.onload = function() {
        Swal.fire({
            title: 'Location Permission',
            text: 'Please allow location access in the browser dialog.',
            icon: 'info',
            showConfirmButton: false, // Remove the confirm button
            showCancelButton: false, // Remove the cancel button
            timer: 3000 // Optional: auto-close after 3 seconds
        });
       
    };
</script>
</x-guest-layout>