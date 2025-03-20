<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::orderByRaw("CASE WHEN role = 'Admin' THEN 1 ELSE 2 END")->get();
        return view('accounts', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:user_accs,username',
            'email' => 'required|email|unique:user_accs,email',
            'role' => 'required|string',
            'password' => 'required|string|min:6'
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password), // Hash password before saving
        ]);

        return redirect()->back()->with('success', 'User added successfully!');
    }
}
