<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $casts = [
        'information' => 'json'
    ];

    public function batches(){
    	return $this->hasMany('App\ProductBatch', 'product_id');
    }
}
