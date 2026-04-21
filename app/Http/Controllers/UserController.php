<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        $user = $this->storeUserRecord([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 0,
            'status' => 1,
            'first_login' => 'yes',
            'mobile_no' => 0,
        ]);

        $this->storeEmployeeRecord('slecic_employees', [
            'name' => $request->name,
            'user_id' => $user->id,
            'status' => 1,
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
            'name'     => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'branch_name' => 'nullable|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $displayName = $request->input('name')
            ?: $request->input('branch_name')
            ?: $request->input('bank_name')
            ?: 'Bank User';

        $user = $this->storeUserRecord([
            'name' => $displayName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 1,
            'status' => 1,
            'first_login' => 'yes',
            'mobile_no' => 0,
        ]);

        $this->storeEmployeeRecord('bank_employees', [
            'name' => $displayName,
            'user_id' => $user->id,
            'branch_id' => 0,
            'role' => 'bank_user',
            'role_id' => 0,
            'status' => 'active',
        ]);

        return redirect()->route('bank.login.form')
            ->with('success', 'Bank registration successful! Please log in.');
    }

    private function storeUserRecord(array $attributes): User
    {
        $columns = Schema::getColumnListing('users');
        $user = new User();

        foreach ($attributes as $column => $value) {
            if (in_array($column, $columns, true)) {
                $user->{$column} = $value;
            }
        }

        $user->save();

        return $user;
    }

    private function storeEmployeeRecord(string $table, array $attributes): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $columns = Schema::getColumnListing($table);
        $payload = [];

        foreach ($attributes as $column => $value) {
            if (in_array($column, $columns, true)) {
                $payload[$column] = $value;
            }
        }

        if (in_array('created_at', $columns, true)) {
            $payload['created_at'] = now();
        }

        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        if (!empty($payload)) {
            DB::table($table)->insert($payload);
        }
    }
}
