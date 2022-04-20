<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;

class BookController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fullname'     => 'required',
            'bank'         => 'required|integer',
            'product_type' => 'required|integer',
            'product'      => 'required|integer',
        ]);

        // Request is valid...
        
        $book = new Book;
        $book->owner = \Auth::user()->id;
        $book->fullname = $request->input('fullname');
        $book->bank_id = $request->input('bank');
        $book->product_type_id = $request->input('product_type');
        $book->product_number = $request->input('product');
        
        if (! $book->save())
        {
            return redirect('/transfer')->with('status', array(
                'alert'   => 'warning',
                'message' => 'Error adding beneficiary.',
            ));
        }

        return redirect('/transfer')->with('status', array(
            'alert'   => 'success',
            'message' => 'Beneficiary correctly added.',
        ));
    }
}
