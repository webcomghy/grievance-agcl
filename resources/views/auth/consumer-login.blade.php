<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-100">
        <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
            <div>
                <img class="mx-auto h-12 w-auto" src="{{asset('logo.jpg')}}" alt="Logo">
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Consumer Login</h2>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form class="mt-8 space-y-6" action="{{ route('consumer.login') }}" method="POST">
                @csrf
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="mobile_number" class="sr-only">{{ __('Mobile Number') }}</label>
                        <input id="mobile_number" name="mobile_number" type="text" autocomplete="mobile_number" required autofocus value="{{ old('mobile_number') }}" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="{{ __('Mobile Number') }}">
                        <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
                    </div>
                    <div>
                        <label for="password" class="sr-only">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="{{ __('Password') }}">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
            
            {{-- <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    Not a consumer? 
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Login here
                    </a>
                </p>
            </div> --}}
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="https://assamgas.co.in/login" target="_blank" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Register here
                    </a>
                </p>
            </div>

        </div>
    </div>
</body>
</html>