<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $table = "customers";

    protected $guarded = [];

    public function getNameAttribute($value)
    {
        return str_replace('&amp;', '&', $value);
    }
}
