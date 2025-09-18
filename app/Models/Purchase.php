<?php

namespace App\Models;

use App\Models\User;
use App\Models\Management\Banks;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Expenses;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
use App\Models\Management\Tank;
use App\Models\Management\Drivers;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchase';

    protected $guarded = [];

    /**
     * Get the chambers for this purchase
     */
    public function chambers()
    {
        return $this->hasMany(PurchaseChamber::class);
    }

    /**
     * Get the product for this purchase
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the tank for this purchase
     */
    public function tank()
    {
        return $this->belongsTo(Tank::class, 'tank_id');
    }

    /**
     * Get the driver for this purchase
     */
    public function driver()
    {
        return $this->belongsTo(Drivers::class, 'driver_no');
    }

    /**
     * Get vendor details by type (similar to old project's getvendorbytype function)
     */
    public function getVendorByType($vendorType, $supplierId)
    {
        $vendorDetails = [];
        $vendorName = '';
        $vendorTypeName = '';

        switch ($vendorType) {
            case 1:
                $vendorDetails = Suppliers::find($supplierId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'Supplier';
                break;
            case 2:
                $vendorDetails = Customers::find($supplierId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'customer';
                break;
            case 3:
                $vendorDetails = Product::find($supplierId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'product';
                break;
            case 4:
                $vendorDetails = Expenses::find($supplierId);
                $vendorName = $vendorDetails->expense_name ?? '';
                $vendorTypeName = 'expense';
                break;
            case 5:
                $vendorDetails = Incomes::find($supplierId);
                $vendorName = $vendorDetails->income_name ?? '';
                $vendorTypeName = 'income';
                break;
            case 6:
                $vendorDetails = Banks::find($supplierId);
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'bank';
                break;
            case 7:
                $vendorName = 'cash';
                $vendorTypeName = 'cash';
                break;
            case 8:
                $vendorName = 'MP';
                $vendorTypeName = 'MP';
                break;
            case 9:
                $vendorDetails = User::where('user_type','Employee')->first();
                $vendorName = $vendorDetails->name ?? '';
                $vendorTypeName = 'employee';
                break;
        }

        return (object)[
            'vendor_details' => $vendorDetails,
            'vendor_name' => $vendorName,
            'vendor_type' => $vendorTypeName
        ];
    }
}
