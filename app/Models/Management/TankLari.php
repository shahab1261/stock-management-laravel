<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TankLari extends Model
{
    use HasFactory;

    protected $table = "tank_lari";
    protected $primaryKey = 'tid';
    protected $guarded = [];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }

    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }
}
