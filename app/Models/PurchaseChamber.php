<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseChamber extends Model
{
    use HasFactory;

    protected $table = 'purchase_chambers_details';

    protected $guarded = [];

    /**
     * Get the purchase that owns the chamber.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
