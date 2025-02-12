<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only(['user_id', 'password']);
        //dd($credentials);
        if (Auth::attempt($credentials)) {
            toastr()->success('Login successful');
            return redirect()->route('home.dashboard');
        }
        toastr()->error('Invalid Credentials');
        return redirect()->route('login');
    }

    public function logout()
    {
        Session::flush();
        auth()->logout();
        return redirect()->route('login');
    }

}

