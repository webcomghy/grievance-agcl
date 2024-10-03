<x-consumer-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-xl font-bold mb-4">Create Grievance</h2>
                    <form method="post" action="{{ route('grievances.store') }}">
                        @csrf
                        <input type="hidden" name="is_grid_admin" value=0>
                        <div class="mb-4 flex space-x-4">
                            <div class="w-1/2">
                                <label for="consumer_no" class="block text-sm font-medium">Consumer No</label>
                                <input type="text" id="consumer_no" name="consumer_no" value="{{ Auth::guard('consumer')->user()->consumer_number }}" class="border p-2 w-full">
                            </div>
                            <div class="w-1/2">
                                <label for="ca_no" class="block text-sm font-medium">CA Number</label>
                                <input type="text" id="ca_no" name="ca_no" value="{{ Auth::guard('consumer')->user()->ca_no }}"  class="border p-2 w-full">
                            </div>
                        </div>
                        <div class="mb-4 flex space-x-4">
                            <div class="w-1/2">
                                <label for="name" class="block text-sm font-medium">Name</label>
                                <input type="text" id="name" name="name" required class="border p-2 w-full">
                            </div>
                            <div class="w-1/2">
                                <label for="phone" class="block text-sm font-medium">Phone</label>
                                <input type="text" id="phone" name="phone" value="{{ Auth::guard('consumer')->user()->mobile_number }}"  required class="border p-2 w-full">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium">Email</label>
                            <input type="email" id="email" name="email" value="{{ Auth::guard('consumer')->user()->email }}"  class="border p-2 w-full">
                        </div>
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium">Category</label>
                            <select id="category" name="category" required class="border p-2 w-full">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                      
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium">Address</label>
                            <input type="text" id="address" name="address" required class="border p-2 w-full">
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium">Description</label>
                            <textarea id="description" name="description" required class="border p-2 w-full"></textarea>
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
</x-consumer-layout>