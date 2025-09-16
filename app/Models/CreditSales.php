<?php

namespace App\Models;

use App\Models\Management\Product;
use App\Models\Management\Tank;
use App\Models\Management\TankLari;
use App\Models\Management\Customers;
use Illuminate\Database\Eloquent\Model;

class CreditSales extends Model
{
    protected $table = 'credit_sales';

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'transasction_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function tank()
    {
        return $this->belongsTo(Tank::class, 'tank_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(TankLari::class, 'vehicle_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'vendor_id');
    }

    /**
     * Get vendor details by type
     */
    public function getVendorByType($vendorType, $vendorId)
    {
        $vendorDetails = [];
        $vendorName = '';
        $vendorTypeName = '';

        switch ($vendorType) {
            case 1: // supplier
                $vendor = \App\Models\Management\Suppliers::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'supplier';
                break;
            case 2: // customer
                $vendor = \App\Models\Management\Customers::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'customer';
                break;
            case 3: // product
                $vendor = \App\Models\Management\Product::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'product';
                break;
            case 4: // expense
                $vendor = \App\Models\Management\Expenses::find($vendorId);
                $vendorName = $vendor->expense_name ?? 'Not found';
                $vendorTypeName = 'expense';
                break;
            case 5: // income
                $vendor = \App\Models\Management\Incomes::find($vendorId);
                $vendorName = $vendor->income_name ?? 'Not found';
                $vendorTypeName = 'income';
                break;
            case 6: // bank
                $vendor = \App\Models\Management\Banks::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'bank';
                break;
            case 7: // cash
                $vendorName = 'Cash';
                $vendorTypeName = 'cash';
                break;
            case 8: // MP
                $vendorName = 'MP';
                $vendorTypeName = 'MP';
                break;
            case 9: // employee
                $vendor = \App\Models\User::find($vendorId);
                $vendorName = $vendor->name ?? 'Not found';
                $vendorTypeName = 'employee';
                break;
            default:
                $vendorName = 'Unknown';
                $vendorTypeName = 'unknown';
        }

        return (object) [
            'vendor_details' => $vendorDetails,
            'vendor_name' => $vendorName,
            'vendor_type' => $vendorTypeName,
        ];
    }
}
