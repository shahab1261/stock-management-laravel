<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Management\Tank;
use App\Models\Management\Product;
use App\Models\User;

class Dip extends Model
{
    use HasFactory;

    protected $table = "dips";
    protected $guarded = [];

    protected $casts = [
        'dip_date' => 'date',
        'dip_value' => 'decimal:2',
        'liters' => 'decimal:2',
        'previous_stock' => 'decimal:2',
    ];

    // Relationships
    public function tank()
    {
        return $this->belongsTo(Tank::class, 'tankId');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'entery_by_user');
    }

    // Scopes
    public function scopeByTank($query, $tankId)
    {
        return $query->where('tankId', $tankId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('dip_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('dip_date', [$startDate, $endDate]);
    }
}
