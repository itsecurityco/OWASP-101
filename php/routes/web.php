<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Guest routes
Route::get('/signin', function() {
    return view('sign_in');
})->name('signin')->middleware('guest');

Route::post('/signin', 'SigninController@authenticate')->middleware('guest');

Route::get('/signup', function() {
    return view('sign_up');
})->middleware('guest');

Route::post('/signup', 'SignupController@store')->middleware('guest');

// Auth routes
Route::get('/logout', function (){
    Auth::logout();
    return redirect('/signin');
})->middleware('auth');

Route::get('/', function () {
    return redirect('/home');
})->middleware('auth');

Route::get('/home', function () {
    return redirect('/received');
})->name('home')->middleware('auth');

Route::get('/sent', function () {
    return view('sent');
})->name('sent')->middleware('auth');

Route::get('/received', function () {
    return view('received');
})->name('received')->middleware('auth');

Route::get('/transfer', function () {
    return view('transfer');
})->name('transfer')->middleware('auth');

// add user to book
Route::post('/agenda', 'BookController@store');

// API
// make transfer
Route::post('/api/transfer', 'TefController@store')->middleware('auth');

// get user products
Route::get('/api/accounts', function () {
    $current_user = Auth::user();
    return [
        'bank'    => $current_user->product->bank->name,
        'type'    => $current_user->product->type->name,
        'id'      => $current_user->product->id,
        'balance' => $current_user->product->balance,
    ];
})->middleware('auth');

// get user books
Route::get('/api/beneficiaries', function () {
    $book = array();
    foreach (Auth::user()->books as $destinatary) {
        array_push($book, array(
            'fullname' => $destinatary->fullname,
            'bank' => $destinatary->bank->name,
            'product_type' => $destinatary->product_type->name,
            'product_number' => $destinatary->product_number,
        ));
    }

    return $book;
});

// get banks
Route::get('/api/banks', function () {
    return DB::select('SELECT * FROM banks');
});

// get product types
Route::get('/api/product_types', function () {
    return DB::select('SELECT * FROM product_types');
});

// get transfers sent
Route::get('/api/sent', function () {
    $destination_name = '';
    $destination_bank = '';
    $transactions = array();
    $current_user = Auth::user();
    foreach ($current_user->product->withdraw as $transaction) {

        $destination = \App\Book::where('product_number', $transaction->destination)->where('owner', $current_user->id)->first();

        if (! is_null($destination))
        {
            $destination_name = $destination->fullname;
            $destination_bank = $destination->bank->name;
        }

        array_push($transactions, array(
            'id' => $transaction->id,
            'origin' => $transaction->origin,
            'destination' => $transaction->destination,
            'destination_name' => $destination_name,
            'destination_bank' => $destination_bank,
            'amount' => $transaction->amount,
            'message' => $transaction->message,
            'date' => $transaction->date,
            'time' => $transaction->time,
        ));
    }

    return $transactions;
})->middleware('auth');

// get transfers received
Route::get('/api/received', function () {
    $transactions = array();
    foreach (Auth::user()->product->deposit as $transaction) {

        $origin = \App\Product::where('id', $transaction->origin)->first();

        array_push($transactions, array(
            'id' => $transaction->id,
            'origin' => $transaction->origin,
            'origin_name' => $origin->user->fullname,
            'origin_bank' => $origin->bank->name,
            'destination' => $transaction->destination,
            'amount' => $transaction->amount,
            'message' => $transaction->message,
            'date' => $transaction->date,
            'time' => $transaction->time,
        ));
    }

    return $transactions;
})->middleware('auth');

// get transfer detail
Route::get('/api/transfer/{id}', function ($id) {
    $transaction = \App\Tef::where('id', $id)->first();
    if (is_null($transaction))
        return array();

    $origin = \App\Product::where('id', $transaction->origin)->first();
    $destination = \App\Product::where('id', $transaction->destination)->first();

    return array(
        'id' => $transaction->id,
        'origin' => array(
            'fullname' => $origin->user->fullname,
            'product' => $origin->id,
        ),
        'destination' => array(
            'fullname' => $destination->user->fullname,
            'product' => $destination->id,
        ),
        'amount' => $transaction->amount,
        'message' => $transaction->message,
        'date' => $transaction->date,
        'time' => $transaction->time,
    );
})->name('transfer-detail')->middleware('auth');
