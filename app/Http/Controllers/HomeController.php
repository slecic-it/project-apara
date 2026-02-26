<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return redirect()->route('login.form');
    }
    public function dashboard()
    {
        $employee = session('employee');
        return view('slecic.dashboard', compact('employee'));
    }
}