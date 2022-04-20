<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    public $timestamps = false;
    
    public function products()
    {
        return $this->hasMany('App\Product', 'product_type_id');
    }

    public function books()
    {
        return $this->hasMany('App\Book', 'product_type_id');
    }
}
