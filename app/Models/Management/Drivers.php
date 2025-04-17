<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drivers extends Model
{
    use HasFactory;

    protected $table = "drivers";
    protected $guarded = [];

    // Relationships
    public function tankLaris()
    {
        return $this->hasMany(TankLari::class, 'driver_id');
    }
}
