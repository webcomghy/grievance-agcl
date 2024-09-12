<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        Auth::guard('consumer')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
