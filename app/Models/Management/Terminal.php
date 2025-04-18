<?php

namespace App\Models\Management;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Terminal extends Model
{
    use HasFactory;

    protected $table = "terminals";
    protected $guarded = [];

    // Relationship to User who added the terminal
    public function entryBy()
    {
        return $this->belongsTo(User::class, 'entery_by_user');
    }
}
