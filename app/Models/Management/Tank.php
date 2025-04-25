<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Tank extends Model
{
    use HasFactory;

    protected $table = "tanks";
    protected $guarded = [];

    // Relationships
    public function nozzles()
    {
        return $this->hasMany(Nozzle::class, 'tank_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function dipCharts()
    {
        return $this->hasMany(DipChart::class, 'tank_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entery_by_user');
    }
}
