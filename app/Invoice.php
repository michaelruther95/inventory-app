<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	protected $casts = [
        'information' => 'json'
    ];
    
    public function invoicePurchases(){
    	return $this->hasMany('App\InvoicePurchase', 'invoice_id');
    }
}
