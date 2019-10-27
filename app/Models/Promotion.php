<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $guarded = ['id'];
    protected $table = 'promotions';
    public $timestamps = true;
}
