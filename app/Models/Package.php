<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = ['id'];
    protected $table = 'packages';
    public $timestamps = true;
}
