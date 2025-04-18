<?php

namespace App\Models\Management;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = "suppliers";
    protected $guarded = [];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'entery_by_user');
    }
}
