<?php

namespace App\Models;

use App\Models\Management\Banks;
use Illuminate\Support\Facades\DB;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $guarded = [];
    protected $primaryKey = 'tid';

    /**
     * Get the vendor details based on vendor type
     */
    public function getVendorDetailsAttribute()
    {
        switch ($this->vendor_type) {
            case 1:
                return Suppliers::find($this->vendor_id);
            case 2:
                return Customers::find($this->vendor_id);
            case 6:
                return Banks::find($this->vendor_id);
            default:
                return null;
        }
    }

    /**
     * Get the bank details if payment type is bank
     */
    public function bank()
    {
        return $this->belongsTo(Banks::class, 'bank_id');
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope for filtering by payment type
     */
    public function scopeByPaymentType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by vendor
     */
    public function scopeByVendor($query, $vendorId, $vendorType = null)
    {
        $query->where('vendor_id', $vendorId);

        if ($vendorType) {
            $query->where('vendor_type', $vendorType);
        }

        return $query;
    }
}
