<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseChamber extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'chamber_number',
        'capacity',
        'dip',
        'rec_dip',
        'gain_loss',
        'ltr'
    ];

    /**
     * Get the purchase that owns the chamber.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
