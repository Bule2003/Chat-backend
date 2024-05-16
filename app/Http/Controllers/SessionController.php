<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if(!Auth::attempt($attributes)){
            logger($attributes);
            throw ValidationException::withMessages([
                'email' => 'Sorry, those credentials do not match.'
            ]);
        };

        //TODO: add password to the query
        $user = User::where('email', $attributes['email'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Sorry, those credentials do not match.'
            ]);
        }

        /*request()->session()->put('user', $user);*/
        session(['user' => $user]);

        /*request()->session()->regenerate();*/

        return response()->json(['user' => $user]);
    }

    public function destroy()
    {
        /*Auth::logout();*/

        request()->session()->forget('user');

        return redirect('/');
    }
}
