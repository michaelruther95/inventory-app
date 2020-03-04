<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiseaseFinding extends Model
{
    public function disease(){
    	return $this->belongsTo('App\Disease');
    }
}
