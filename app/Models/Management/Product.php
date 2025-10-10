<?php

namespace App\Models\Management;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";
    protected $guarded = [];

    // Relationships
    public function nozzles()
    {
        return $this->hasMany(Nozzle::class, 'product_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }

    public function getNameAttribute($value)
    {
        return str_replace('&amp;', '&', $value);
    }
}
