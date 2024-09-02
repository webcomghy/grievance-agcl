<x-guest-layout>
    <section id="mobile-section" class="p-4">
        <h2 class="text-xl font-bold mb-4">Enter Mobile Number</h2>
        <input type="text" id="mobile" placeholder="Enter Mobile Number" class="border p-2 mb-4 w-full">
        <button id="send-otp-btn" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Send OTP</button>
    </section>

    <section id="otp-section" class="p-4 hidden">
        <h2 class="text-xl font-bold mb-4">Verify OTP</h2>
        <input type="text" id="otp" placeholder="Enter OTP" class="border p-2 mb-4 w-full">
        <button id="verify-otp-btn" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Verify</button>
    </section>

    <section id="grievance-form" class="p-4 hidden">
        <h2 class="text-xl font-bold mb-4">Create Grievance</h2>
        <form method="post" action="{{ route('grievances.store') }}">
            @csrf
            <div class="mb-4">
                <label for="consumer_no" class="block text-sm font-medium">Consumer No</label>
                <input type="text" id="consumer_no" name="consumer_no" required class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="ca_number" class="block text-sm font-medium">CA Number</label>
                <input type="text" id="ca_number" name="ca_number" required class="border p-2 w-full">
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
                <label for="name" class="block text-sm font-medium">Name</label>
                <input type="text" id="name" name="name" required class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium">Address</label>
                <input type="text" id="address" name="address" required class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium">Phone</label>
                <input type="text" id="phone" name="phone" required class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" id="email" name="email" required class="border p-2 w-full">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium">Description</label>
                <textarea id="description" name="description" required class="border p-2 w-full"></textarea>
            </div>
           
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Submit</button>
        </form>
    </section>

    <!-- Add loader element -->
    <div id="loader" class="hidden fixed inset-0 bg-gray-800 bg-opacity-25 flex items-center justify-center">
        <div class="loader"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function showLoader() {
                $('#loader').removeClass('hidden');
            }

            function hideLoader() {
                $('#loader').addClass('hidden');
            }

            $('#send-otp-btn').click(function() {
                var mobile = $('#mobile').val();
                showLoader();
                $.ajax({
                    url: '{{ route("send.otp") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mobile: mobile
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.success) {
                            $('#mobile-section').hide();
                            $('#otp-section').show();
                        } else {
                            alert('Failed to send OTP');
                        }
                    },
                    error: function() {
                        hideLoader();
                        alert('An error occurred');
                    }
                });
            });

            $('#verify-otp-btn').click(function() {
                var otp = $('#otp').val();
                showLoader();
                $.ajax({
                    url: '{{ route("verify.otp") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        otp: otp
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.success) {
                            $('#otp-section').hide();
                            $('#grievance-form').show();
                        } else {
                            alert('Invalid OTP');
                        }
                    },
                    error: function() {
                        hideLoader();
                        alert('An error occurred');
                    }
                });
            });
        });
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
</x-guest-layout>