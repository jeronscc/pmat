<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        // Validate the incoming request fields
        $incomingFields = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        // Attempt to find the user by username
        $user = DB::table('user_accs')->where('username', $incomingFields['username'])->first();

        // Check if the user exists and if the password matches
        if ($user && $user->password === $incomingFields['password']) {
            // Log in the user
            Auth::loginUsingId($user->user_id);
            
            // Regenerate the session to prevent session fixation attacks
            $request->session()->regenerate();

            // Redirect the user to the homepage or their intended destination
            return redirect()->intended('/homepage-ilcdb');
        } else {
    // If the credentials are invalid, pass a custom error message to the session
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