<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function indexPage(){
        return redirect()->route('dashboard.index');
    }
    public function loginView()
    {
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard.index');
        }

        return back()->withErrors(['email' => 'Yanlış parol!']);
    }
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
