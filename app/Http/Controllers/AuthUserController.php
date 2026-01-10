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
        // If user is already authenticated, redirect to intended page or dashboard
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }
        
        return view('pages.login');
    }

    // handle login form
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $remember = $request->boolean('remember');

        if (Auth::attempt($data, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])
                     ->onlyInput('email', 'remember');
    }

    // handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // use a specific key so it's only used for logout
        return redirect('/')->with('logout_success', __('You have been logged out.'));
    }
}
