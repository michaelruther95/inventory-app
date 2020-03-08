<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{

    protected $casts = [
        'information' => 'json'
    ];

    public function itemInfo(){
    	return $this->belongsTo('App\Product', 'product_id');
    }
}
