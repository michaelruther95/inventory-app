<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    public function productInfo(){
    	return $this->belongsTo('App\Product', 'product_id');
    }
}
