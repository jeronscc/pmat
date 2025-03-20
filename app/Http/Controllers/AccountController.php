<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::orderByRaw("CASE WHEN role = 'Admin' THEN 1 ELSE 2 END")->get();
        return view('accounts', compact('users'));
    }
}
