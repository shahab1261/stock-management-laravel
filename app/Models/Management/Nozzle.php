<?php

namespace App\Models\Management;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nozzle extends Model
{
    use HasFactory;

    protected $table = "nozzle";
    protected $guarded = [];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class, 'tank_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entery_by_user');
    }
}
