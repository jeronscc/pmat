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

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user_accs,user_id',
            'username' => 'required|string|unique:user_accs,username,' . $request->user_id . ',user_id',
            'email' => 'required|email|unique:user_accs,email,' . $request->user_id . ',user_id',
            'role' => 'required|string',
            'password' => 'nullable|string|min:6', // Password is optional but must be at least 6 characters
        ]);
    
        $user = User::where('user_id', $request->user_id)->firstOrFail();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;
    
        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        return redirect()->back()->with('success', 'User updated successfully.');
    }
    
}
