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

    public function purchases(){
    	return $this->hasMany('App\Purchase', 'product_id');
    }



    public static function getAllProducts(){
        $products = Product::with('batches.supplierInfo', 'purchases')->get();

        foreach ($products as $product) {
            $product->is_selected = false;
            $product->stocks_to_buy = 0;
            $product->validation_error = '';
        }

        return $products;
    }
}
