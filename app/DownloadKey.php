<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DownloadKey extends Model
{
    protected $casts = [
        'information' => 'json'
    ];
}
