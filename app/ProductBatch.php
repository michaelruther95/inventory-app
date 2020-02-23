<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBatch extends Model
{
    use SoftDeletes;
	
    protected $casts = [
        'information' => 'json'
    ];

    public function purchases(){
        return $this->hasMany('App\Purchase', 'product_batch_id');
    }

    // --------------------------------------------------------------------
    // ACCESSORS
    // --------------------------------------------------------------------
    public function getInformationAttribute($val){
    	$current_value = (array)json_decode($val);
    	$current_value['is_expired'] = false;
    	if(date('Y-m-d') > $current_value['expiration_date']){
    		$current_value['is_expired'] = true;
    	}

    	$current_value['remaining_stocks'] = $current_value['number_of_stocks'] - $current_value['stocks_purchased'];
    	return $current_value;
    }
    // --------------------------------------------------------------------
}
