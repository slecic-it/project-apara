<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /* SLEC USER REGISTRATION */

    public function createSlecic()
    {
        return view('slecic.register');
    }

    public function slecicRegister()
    {
        return $this->createSlecic();
    }

    public function sleRegisterConfirm(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')
            ->with('success', 'Registration successful! Please log in.');
    }

    public function slecicRegisterConfirm(Request $request)
    {
        return $this->sleRegisterConfirm($request);
    }
    
    /* BANK REGISTRATION */

    public function createBank()
    {
        return view('bank.register');
    }

    public function bankRegisterConfirm(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')
            ->with('success', 'Bank registration successful! Please log in.');
    }
}
