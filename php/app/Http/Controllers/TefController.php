<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tef;

class TefController extends Controller
{

    public function store(Request $request) {

        $validator  = \Validator::make($request->all(), [
            'origin'      => 'required|integer',
            'destination' => 'required|integer',
            'amount'      => 'required|numeric|min:1',
            'bank'        => 'required',
        ]);

        // The requests is not valid...
        if ($validator->fails())
        {
            $errors = $validator->errors();
            $errorMessage = "<ul>";
            foreach ($errors->all() as $message) $errorMessage .= "<li>$message</li>";
            $errorMessage .= "</ul>";
            return array(
                'code' => '0001',
                'message' => array(
                    'type' => 'alert-warning',
                    'content' => $errorMessage,
                ),
            );
        }

        $origin = \App\Product::where('id', $request->input('origin'))->first();
        
        // product origin is not valid
        if (is_null($origin)) 
        {
            return array(
                'code' => '0001',
                'message' => array(
                    'type' => 'alert-danger',
                    'content' => 'Error making the transfer.',
                ),
            );
        }

        // check if product origin has sufficient money to make the transfer
        if ($origin->balance < $request->input('amount')) 
        {
            return array(
                'code' => '0001',
                'message' => array(
                    'type' => 'alert-warning',
                    'content' => 'Insufficient funds.',
                ),
            );
        }

        // Withdraw the amount from product origin
        $origin->balance -= $request->input('amount');
        
        // the money go to a external bank...
        $external_bank = (strtolower($request->input('bank')) != strtolower(\Config::get('app.bank_name'))) ? true : false;

        if (! $external_bank) 
        {
            $dest = \App\Product::where('id', $request->input('destination'))->first();
            
            // product destination is not valid
            if (is_null($dest)) {
                return array(
                    'code' => '0001',
                    'message' => array(
                        'type' => 'alert-danger',
                        'content' => 'Error making the transfer.',
                    ),
                );
            }

            // Inject amount to product destination
            $dest->balance += $request->input('amount');
        }
    
        $tef = new Tef;
        $tef->origin      = $request->input('origin');
        $tef->destination = $request->input('destination');
        $tef->amount      = $request->input('amount');
        $tef->message     = $request->input('message');
        $tef->date        = date('Y-m-d');
        $tef->time        = date('h:i:s');

        // Check that origin account belongs to the current user.
        if ($tef->origin != \Auth::user()->product->id) {
            return array(
                'code' => '0001',
                'message' => array(
                    'type' => 'alert-danger',
                    'content' => 'Possible bank fraud. Your IP address has been logged.',
                ),
            );
        }

        // Save origin status
        if (! $origin->save())
        {
            return array(
                'code' => '0001',
                'message' => array(
                    'type' => 'alert-danger',
                    'content' => 'Error subtracting funds from the origin account. Contact the Administrator.',
                ),
            );
        }

        // Save destination status
        if (! $external_bank)
        {
            if (! $dest->save()) 
            {
                return array(
                    'code' => '0001',
                    'message' => array(
                        'type' => 'alert-danger',
                        'content' => 'Error adding funds to beneficiary account. Contact the Administrator.',
                    ),
                );
            }
        }

        // Save tef status
        if (! $tef->save()) 
        {
            return array(
                'code' => '0001',
                'message' => array(
                    'type' => 'alert-danger',
                    'content' => 'Error registering the transfer. Contact the Administrator.',
                ),
            ); 
        }

        return array(
            'code' => '0000',
            'message' => array(
                'type' => 'alert-success',
                'content' => 'Successfull transfer.',
            ),
            'originAccountBalance' => $origin->balance,
        ); 
    }
}