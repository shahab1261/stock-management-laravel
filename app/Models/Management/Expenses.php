<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    use HasFactory;

    protected $table = "expenses";
    protected $primaryKey = "id";
    protected $guarded = [];

    public function getExpenseNameAttribute($value)
    {
        return str_replace('&amp;', '&', $value);
    }
}
