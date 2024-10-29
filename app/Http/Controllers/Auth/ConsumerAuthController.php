<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\Notification; // For sending notifications
use App\Notifications\OtpNotification; // Assuming you have a notification class for sending OTP
use Illuminate\Support\Facades\Http;

class ConsumerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.consumer-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'mobile_number' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::guard('consumer')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('grievance.form');
        }

        return back()->withErrors([
            'mobile_number' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {

        // dd("ok");
        Auth::guard('consumer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if(session()->has('mobile_number')){
            session()->forget(['mobile_number']);
        }
       
        return redirect('/consumer/login');
    }

    public function loginWithOTP(Request $request)
    {
        return view('auth.consumer-otp-login');
    }

    public function requestOtp(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
        ]);

        // Generate a random OTP
        $otp = rand(100000, 999999);

        // Prepare the message content with OTP
        $message = "Your OTP is {$otp} - AGCL, Duliajan.";

        // Send OTP via SMS API
        $url = "https://sms6.rmlconnect.net:8443/bulksms/bulksms";
        $params = [
            'username' => 'AssamTrans',
            'password' => '}yA3bI[7',
            'type' => '0',
            'dlr' => '1',
            'destination' => '91' . $request->mobile_number,
            'source' => 'AGCLBG',
            'message' => $message,
            'entityid' => '1201159514504706254',
            'tempid' => '1507167110580384721',
        ];
        
        $response = Http::withOptions(['verify' => false])->get($url, $params);
    

        // Check if the SMS was sent successfully
        if ($response->successful()) {
            // Log the OTP for demonstration purposes (remove in production)
            Log::info("OTP for {$request->mobile_number}: {$otp}");

            // Store the OTP in the session or database for verification
            session(['otp' => $otp, 'mobile_number' => $request->mobile_number]);

            return response()->json(['success' => true, 'message' => 'OTP sent to your mobile number.']);
        } else {
            // Log the error and return a failure response
            Log::error("Failed to send OTP to {$request->mobile_number}. Response: " . $response->body());

            return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again later.']);
        }
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|integer',
        ]);

        // Retrieve the OTP from the session
        $sessionOtp = session('otp');
        $mobileNumber = session('mobile_number');

        if ($request->otp == $sessionOtp) {
            // OTP is valid, log the user in
            Auth::guard('consumer')->loginUsingId($mobileNumber); // Assuming you have a way to get the user by mobile number

            // Clear the OTP from the session
            session()->forget(['otp']);

            return response()->json(['success' => true, 'message' => 'Logged in successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid OTP.'], 400);
    }
}
