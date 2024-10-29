<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Login with OTP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="h-full">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-100">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <div>
                <img class="mx-auto h-12 w-auto" src="{{ asset('logo.jpg') }}" alt="Logo">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Login with OTP</h2>
            </div>

            <form id="otpRequestForm" class="mt-8 space-y-6" method="POST">
                @csrf
                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                    <input id="mobile_number" name="mobile_number" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter your mobile number">
                </div>
                <div>
                    <button type="button" id="requestOtpButton" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Request OTP
                    </button>
                </div>
            </form>

            <div id="otpForm" class="hidden">
                <form id="otpVerificationForm" class="mt-8 space-y-6" method="POST">
                    @csrf
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP</label>
                        <input id="otp" name="otp" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter the OTP">
                    </div>
                    <div>
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Verify OTP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#requestOtpButton').click(function() {
                var mobileNumber = $('#mobile_number').val();
                $.ajax({
                    url: '{{ route("consumer.request.otp") }}', // Define the route for OTP request
                    method: 'POST',
                    data: {
                        mobile_number: mobileNumber,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#otpRequestForm').hide();
                            $('#otpForm').removeClass('hidden');
                            alert('OTP sent to your mobile number.');
                        } else {
                            alert('Failed to send OTP. Please try again.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while sending OTP. Please try again.');
                    }
                });
            });

            $('#otpVerificationForm').submit(function(e) {
                e.preventDefault();
                var otp = $('#otp').val();
                $.ajax({
                    url: '{{ route("consumer.verify.otp") }}', // Define the route for OTP verification
                    method: 'POST',
                    data: {
                        otp: otp,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = '{{ route("grievance.otp.form") }}'; // Redirect on success
                        } else {
                            alert('Invalid OTP. Please try again.');
                        }
                    },
                    error: function() {
                        alert('Error occurred while verifying OTP. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
