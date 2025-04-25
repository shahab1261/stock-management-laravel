<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function tanks()
    {
        return $this->hasMany(Tank::class); 
    }
}
