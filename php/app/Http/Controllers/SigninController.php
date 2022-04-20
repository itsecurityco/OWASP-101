<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SigninController extends Controller
{
    public function authenticate(Request $request) {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt([
            'username' => $request->input('username'),
            'password' => $request->input('password')]
        )) {
            // Authentication passed...
            return redirect()->intended('/');
        }

        return redirect('/signin')->with('status', array(
            'alert' => 'warning',
            'message' => 'Authentication failed.',
        ));
    }
}
