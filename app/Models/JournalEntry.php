<?php

namespace App\Models;

use App\Models\Management\Banks;
use App\Models\Management\Product;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use App\Models\Management\Expenses;
use App\Models\Management\Incomes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model
{
    protected $table = 'journal_new';
    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Generate next voucher ID
     */
    public static function generateVoucherId()
    {
        // Get the highest voucher ID number
        $lastEntry = self::where('voucher_id', 'like', 'J%')
            ->whereNotNull('voucher_id')
            ->orderByRaw('CAST(SUBSTRING(voucher_id, 2) AS UNSIGNED) DESC')
            ->first();

        if ($lastEntry && $lastEntry->voucher_id) {
            $lastNumber = (int) substr($lastEntry->voucher_id, 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'J' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user that created this journal entry
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'entery_by_user');
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
            case 4:
                return Expenses::find($this->vendor_id);
            case 5:
                return Incomes::find($this->vendor_id);
            case 6:
                return Banks::find($this->vendor_id);
            case 7:
                return (object)['vendor_name' => 'Cash', 'name' => 'Cash'];
            case 8:
                return (object)['vendor_name' => 'MP', 'name' => 'MP'];
            default:
                return null;
        }
    }

    /**
     * Get the vendor name attribute
     */
    public function getVendorNameAttribute()
    {
        $vendor = $this->getVendorDetailsAttribute();
        if ($vendor) {
            return $vendor->vendor_name ?? $vendor->name ?? 'Unknown';
        }
        return 'Unknown';
    }

    /**
     * Get the vendor type name
     */
    public function getVendorTypeNameAttribute()
    {
        switch ($this->vendor_type) {
            case 1:
                return 'Supplier';
            case 2:
                return 'Customer';
            case 3:
                return 'Product';
            case 4:
                return 'Expense';
            case 5:
                return 'Income';
            case 6:
                return 'Bank';
            case 7:
                return 'Cash';
            case 8:
                return 'MP';
            default:
                return 'Unknown';
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
     * Scope for filtering by debit/credit
     */
    public function scopeByDebitCredit($query, $type)
    {
        return $query->where('debit_credit', $type);
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

    /**
     * Scope for ordering by latest
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
    }
}
