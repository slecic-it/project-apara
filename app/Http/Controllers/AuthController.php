<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view("login");
    }

    public function authenticate(Request $request)
    {

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect('https://www.slecic.lk/');
        }

        return redirect('/login')->with('error', 'Invalid credentials. Please try again.');
    }
}
