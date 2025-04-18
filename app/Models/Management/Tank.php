<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
