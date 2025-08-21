<?php

namespace App\Models;

use App\Models\Management\Banks;
use App\Models\Management\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    protected $table = 'ledger';
    protected $guarded = [];

    /**
     * Get the transaction that owns the ledger entry
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'tid');
    }

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
            case 3:
                return Product::find($this->vendor_id);
            case 6:
                return Banks::find($this->vendor_id);
            case 7:
                return (object)['name' => 'Cash'];
            case 8:
                return (object)['name' => 'MP'];
            default:
                return null;
        }
    }

    /**
     * Scope for filtering by vendor type
     */
    public function scopeByVendorType($query, $type)
    {
        return $query->where('vendor_type', $type);
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
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
