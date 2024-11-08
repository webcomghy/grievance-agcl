@extends('layouts.admin')
@section('title', 'Add Grievance')
@section('content')
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
    <section id="grievance-form" class="p-4 max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Create Grievance</h2>
        <form method="post" action="{{ route('grievances.store') }}" class="space-y-6"  enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="is_grid_admin" value=1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="consumer_no" class="block text-sm font-medium mb-1">Consumer No</label>
                    <input type="text" id="consumer_no" name="consumer_no" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" onblur="checkConsumerNo()">
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium mb-1">Name</label>
                    <input type="text" id="name" name="name" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="phone" class="block text-sm font-medium mb-1">Phone</label>
                    <input type="text" id="phone" name="phone" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"  onblur="validateMobileNumber()">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" id="email" name="email" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"  onblur="validateEmail()">
                </div>
            </div>

            

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <select id="category" name="category" required class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500" onchange="updateSubcategories()">
                    <option value="">Select a category</option>
                    @foreach (array_keys(App\Models\Grievance::$categories) as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="subcategory" class="block text-sm font-medium text-gray-700">Subcategory</label>
                <select id="subcategory" name="subcategory" required class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a subcategory</option>
                </select>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium mb-1">Address</label>
                <textarea id="address" name="address" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3"></textarea>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium mb-1">Description</label>
                <textarea id="description" name="description" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="4"></textarea>
            </div>
            <div>
                <label for="file_upload" class="block text-sm font-medium text-gray-700">Upload File (optional)</label>
                <input type="file" id="file_upload" name="file_upload" class="border border-gray-300 rounded-md p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-sm text-gray-500">Max file size: 2 MB</p> <!-- Added message -->
            </div>
            <div>
                <label for="admin_remark" class="block text-sm font-medium mb-1">Remark (optional)</label>
                <textarea id="admin_remark" name="admin_remark" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3"></textarea>
            </div>
           
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-sm uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Submit Grievance</button>
        </form>
    </section>

    <!-- Add loader element -->
    <div id="loader" class="hidden fixed inset-0 bg-gray-800 bg-opacity-25 flex items-center justify-center">
        <div class="loader"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Added SweetAlert2 -->
    <script>
        const categories = @json(App\Models\Grievance::$categories);
    
        function updateSubcategories() {
            const categorySelect = document.getElementById('category');
            const subcategorySelect = document.getElementById('subcategory');
            const selectedCategory = categorySelect.value;
    
            // Clear previous subcategory options
            subcategorySelect.innerHTML = '<option value="">Select a subcategory</option>';
    
            if (selectedCategory && categories[selectedCategory]) {
                const subcategories = categories[selectedCategory];
                for (const subcategory of subcategories) {
                    const option = document.createElement('option');
                    option.value = subcategory;
                    option.textContent = subcategory;
                    subcategorySelect.appendChild(option);
                }
            }
        }

        function checkConsumerNo() {
            const consumerNo = document.getElementById('consumer_no').value;
            const loader = document.getElementById('loader');
           

            if (consumerNo) {
                loader.classList.remove('hidden'); // Show loader
                $.ajax({
                    url: '{{ route("consumers.check") }}',
                    method: 'GET',
                    data: { consumer_no: consumerNo },
                    success: function(response) {
                        loader.classList.add('hidden'); // Hide loader
                        if (response.success) {
                            document.getElementById('email').value = response.data.email;
                            document.getElementById('name').value = response.data.name;
                        } else {
                            Swal.fire('Consumer Not Found', 'The consumer number you entered was not found.', 'error'); // Updated alert to SweetAlert
                        }
                    },
                    error: function() {
                        loader.classList.add('hidden'); // Hide loader
                        Swal.fire('Error', 'Error checking consumer number', 'error'); // SweetAlert for error
                    }
                });
            }
        }

        function validateMobileNumber() {
            const mobileNumber = document.getElementById('phone').value; // Assuming phone input has id 'phone'
            if (!mobileNumber) return; // Skip validation if input is empty
            let regex = new RegExp(/(0|91)?[6-9][0-9]{9}/);
            if (!regex.test(mobileNumber) || mobileNumber.length !== 10) {
                Swal.fire('Invalid Mobile Number', 'Please enter a valid 10-digit mobile number.', 'error'); // SweetAlert for invalid mobile number
            }
        }

        function validateEmail() {
            const email = document.getElementById('email').value; // Assuming email input has id 'email'
            if (!email) return; // Skip validation if input is empty
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire('Invalid Email', 'Please enter a valid email address.', 'error'); // SweetAlert for invalid email
            }
        }
    </script>

    
@endsection
