<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;
use App\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function store(Request $request) {

        $request->validate([
            'fullname' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required',
        ]);

        // The requests is valid...

        $input = array(
            'fullname' => $request->input('fullname'),
            'username' => $request->input('username'),
            'password' => Hash::make($request->input('password')),
        );

        // Create user
        $user = new User;
        $user->fullname = $request->input('fullname');
        $user->username = $request->input('username');
        $user->password = Hash::make($request->input('password'));

        if (! $user->save()) {
            return redirect('/signup')->with('status', array(
                'alert'   => 'warning',
                'message' => 'Error creating user.',
            ));
        }

        // Create user's product
        $product = new Product;
        $product->user_id = $user->id;
        $product->balance = \Config::get('app.inital_deposit');
        $product->bank_id = 1;
        $product->product_type_id = 1;
        if (! $product->save()) {
            return redirect('/signup')->with('status', array(
                'alert'   => 'warning',
                'message' => 'Error creating user product.',
            ));
        }

        // Create a default contact in book
        $book = new Book;
        $book->owner = $user->id;
        $book->fullname = \Config::get('app.flag_user_name');
        $book->bank_id = 1;
        $book->product_type_id = 1;
        $book->product_number = \Config::get('app.flag_user_account');
        if (! $book->save()) {
            return redirect('/signup')->with('status', array(
                'alert'   => 'warning',
                'message' => "Error when creating the user's address book."
            ));
        }

        return redirect('/signin')->with('status', array(
            'alert' => 'success',
            'message' => 'User successfully registered.',
        ));

    }
}
