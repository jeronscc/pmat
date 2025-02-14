<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $user = DB::table('user_accs')->where('username', $incomingFields['username'])->first();

        if ($user->password === $incomingFields['password']) {  
            Auth::loginUsingId($user->user_id);
            $request->session()->regenerate();
    
            return view('homepage');
        }
        if ($user !== $incomingFields['username'] || $user->password !== $incomingFields['password']) {
            return view('login', ['error' => 'Invalid username or password']);
            return redirect('/login');
        }
    }
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

