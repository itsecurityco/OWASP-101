<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function books()
    {
        return $this->hasMany('App\Book');
    }
}
