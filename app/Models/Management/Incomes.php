<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incomes extends Model
{
    use HasFactory;

    protected $table = "incomes";
    protected $primaryKey = "id";
    protected $guarded = [];

    public function getIncomeNameAttribute($value)
    {
        return str_replace('&amp;', '&', $value);
    }
}
