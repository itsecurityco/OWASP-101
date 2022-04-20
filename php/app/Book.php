<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User', 'owner');
    }

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }

    public function product_type()
    {
        return $this->belongsTo('App\ProductType');
    }

    public function transfers()
    {
        return $this->hasMany('App\Tef', 'destination', 'product_number');
    }
}
