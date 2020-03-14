<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $casts = [
        'information' => 'json'
    ];

    public function productSuppliers(){
    	return $this->hasMany('App\ProductSupplier', 'supplier_id');
    }
}
