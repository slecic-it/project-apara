<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthController extends Controller
{   
    // Show forms
    public function showLoginForm() {
        return view('loginForm');
    }
    public function showRegisterForm() {}

    // Actions
    public function login(Request $request) {
        $email = $request->input('email');
        $password = $request->input('password');

        $credentials = [
            'email' => $email,
            'password' => $password
        ];

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            return view('dashboard');
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    public function register(Request $request) {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('login.form');
    }
    public function logout() {}

    // Password
    public function showForgotPasswordForm() {}
    public function sendResetLinkEmail(Request $request) {}
    public function showResetPasswordForm($token) {}
    public function resetPassword(Request $request) {}

    // Optional
    public function verifyEmail($token) {}
}