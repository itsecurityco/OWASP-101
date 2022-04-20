<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function bank()
    {
        return $this->belongsTo('App\Bank');
    }

    public function type()
    {
        return $this->belongsTo('App\ProductType', 'product_type_id');
    }

    public function withdraw()
    {
        return $this->hasMany('App\Tef', 'origin');
    }

    public function deposit()
    {
        return $this->hasMany('App\Tef', 'destination');
    }
}
