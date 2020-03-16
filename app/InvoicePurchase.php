<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicePurchase extends Model
{
    public function purchaseInfo(){
    	return $this->belongsTo('App\Purchase', 'purchase_id');
    }
}
