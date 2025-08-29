<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = "users";

    protected $guarded = [];

    protected $casts = [
        'user_type' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Get the user's role name
     */
    public function getRoleNameAttribute()
    {
        $roleMap = [
            0 => 'SuperAdmin',
            1 => 'Admin',
            2 => 'Employee'
        ];

        return $roleMap[$this->user_type] ?? 'Unknown';
    }

    /**
     * Check if user is SuperAdmin
     */
    public function isSuperAdmin()
    {
        return $this->user_type === 0;
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin()
    {
        return $this->user_type === 1;
    }

    /**
     * Check if user is Employee
     */
    public function isEmployee()
    {
        return $this->user_type === 2;
    }

    /**
     * Get user status text
     */
    public function getStatusTextAttribute()
    {
        return (int)$this->status == 1 ? 'Active' : 'Inactive';
    }

    /**
     * Get user status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return (int)$this->status == 1 ? 'bg-success' : 'bg-danger';
    }
}

