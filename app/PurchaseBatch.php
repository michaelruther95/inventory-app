<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseBatch extends Model
{
    protected $casts = [
        'information' => 'json'
    ];
}
