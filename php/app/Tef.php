<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tef extends Model
{
    protected $table = 'tef';

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function book()
    {
        return $this->belongsTo('App\Book', 'product_number', 'destination');
    }
}
