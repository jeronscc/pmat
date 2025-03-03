<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OtherexpenseController extends Controller
{
    public function showOtherexpenseForm()
    {
        return view('otherexpenseform');
    }
}
