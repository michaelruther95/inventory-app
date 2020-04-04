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

            foreach($product->batches as $product_batch){
                $date_expiry_notif_limit = date('Y-m-d',strtotime('+30 days',strtotime(date('Y-m-d'))));

                $expiry_date = date('Y-m-d', strtotime($product_batch->information['expiration_date']));
                if($expiry_date <= $date_expiry_notif_limit){
                    $product_batch->is_near_to_expire = true;
                    $product->is_near_to_expire = true;
                }
                else{
                    $product_batch->is_near_to_expire = false;
                }
            }
        }

        return $products;
    }

    public static function getProductInfo($product_id){
        $product = Product::with('batches.supplierInfo', 'purchases')
                    ->where('id', $product_id)
                    ->first();

        $product->is_selected = false;
        $product->stocks_to_buy = 0;
        $product->validation_error = '';

        foreach($product->batches as $product_batch){
            $date_expiry_notif_limit = date('Y-m-d',strtotime('+30 days',strtotime(date('Y-m-d'))));

            $expiry_date = date('Y-m-d', strtotime($product_batch->information['expiration_date']));
            if($expiry_date <= $date_expiry_notif_limit){
                $product_batch->is_near_to_expire = true;
                $product->is_near_to_expire = true;
            }
            else{
                $product_batch->is_near_to_expire = false;
            }
        }

        return $product;
    }
}
