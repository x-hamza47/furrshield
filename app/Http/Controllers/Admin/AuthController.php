<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('dashboard.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:3|max:10'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard.show');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function showRegister(){
        return view('dashboard.auth.register');
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'address'  => ['nullable', 'string'],
            'role'     => ['required', 'in:owner,vet,shelter'],
        ]);

        User::create($credentials);

        return redirect()->route('login');
    }

    public function logout()
    {
        Auth::logout();
        return view('dashboard.auth.login');
    }

    public function dashboard(){
        return view('dashboard.dashboard');
    }
}
