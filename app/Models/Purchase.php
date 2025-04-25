<?php

namespace App\Models;

use App\Models\User;
use App\Models\Management\Banks;
use App\Models\Management\Incomes;
use App\Models\Management\Product;
use App\Models\Management\Expenses;
use App\Models\Management\Customers;
use App\Models\Management\Suppliers;
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
                $vendorDetails = User::where('user_type', 3)->first();
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
