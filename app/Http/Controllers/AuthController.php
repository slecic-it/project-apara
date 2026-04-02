<?php

namespace App\Http\Controllers;
  
use App\Models\User;
use App\Models\Slecic_employee;
use App\Models\Bank_employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{   
    // Show forms
    public function showLoginForm() {
        return view('login');
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
            $user = User::where('email', $email)->first();
            if ($user->type == 0) {
                $employee = Slecic_employee::where('user_id', $user->id)->first();
                session(['employee' => $employee]);
                return redirect()->route('dashboard');    
            
            }elseif ($user->type == 1) {
                $employee = Bank_employee::where('user_id', $user->id)->first();
                session(['employee' => $employee]);
                return redirect()->route('bank.dashboard');
            }
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    public function register(Request $request) {
        $user = new User();
        // $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('login.form');    
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }

    // Password
    public function showForgotPasswordForm() {}
    public function sendResetLinkEmail(Request $request) {}
    public function showResetPasswordForm($token) {}
    public function resetPassword(Request $request) {}

    // Optional
    public function verifyEmail($token) {}
}
