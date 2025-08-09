<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;


class AuthUserController extends Controller
{
    // show login page
    public function showLogin()
    {
        return view('pages.login');
    }

    // handle login form
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        if (Auth::attempt($data, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])
                     ->onlyInput('email');
    }

    // handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('status', 'Logged out.');
    }
}
