<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suppliers extends Model
{
    use HasFactory;

    protected $table = "suppliers";
    protected $guarded = [];

    // Relationships
    public function tankLaris()
    {
        return $this->hasMany(TankLari::class, 'supplier_id');
    }
    public function getNameAttribute($value)
    {
        return str_replace('&amp;', '&', $value);
    }
}
