<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Slecic_employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function slecicRegister(){
        return view("slecic.register");
    }

    public function slecicRegisterConfirm(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Registration successful! Please log in.');
    }
}
