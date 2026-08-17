<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metro extends Model
{
    protected $table = 'metros';
    

    public $timestamps = false; 

    protected $fillable = [
        'numeroMetro',
        'estado'
    ];
}