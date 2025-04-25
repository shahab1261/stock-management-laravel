<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DipChart extends Model
{
    use HasFactory;

    protected $table = "dip_charts";
    protected $guarded = [];

    // Relationships
    public function tank()
    {
        return $this->belongsTo(Tank::class, 'tank_id');
    }
}
