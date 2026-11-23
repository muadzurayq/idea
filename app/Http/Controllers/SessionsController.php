<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SessionsController extends Controller
{
    //
    public function create()
    {
        return view('auth.login');
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email' => ['required', 'email', 'max:255', 'min:3',],
            'password' => ['required', 'min:4'],
        ]);
        if(! Auth::attempt($validatedData)){
            return back()->withErrors([])->withInput();
        }
        $request->session()->regenerate();

        return redirect()->route('ideas.index')->with('success', 'You are now logged in');
    }


    public function destroy()
    {
        auth()->logout();
        return redirect('/login');
    }
}
