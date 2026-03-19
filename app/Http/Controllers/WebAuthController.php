<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required|string'
        ]);

        // Check if user exists by email
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email not found in our system'
            ])->withInput($request->only('email'));
        }

        // Check if password is correct
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'password' => 'Incorrect password'
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}