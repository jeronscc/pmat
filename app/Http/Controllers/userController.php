<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);
    
        // Find user by username
        $user = DB::table('user_accs')->where('username', $incomingFields['username'])->first();
    
        // Check if user exists and password matches
        if ($user && Hash::check($incomingFields['password'], $user->password)) {
            Auth::loginUsingId($user->user_id);
            $request->session()->regenerate();
    
            return redirect()->intended('/select-project');
        } else {
            return redirect()->back()->with('error', 'Invalid username or password');
        }
    }

    public function logout(Request $request)
    {
        // Log out the user
        Auth::logout();
        
        // Invalidate the session and regenerate the token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect the user to the homepage (or login page)
        return redirect('/');
    }
}