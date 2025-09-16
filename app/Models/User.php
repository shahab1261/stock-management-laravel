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
        'status' => 'integer',
    ];

    /**
     * Get the user's role name
     */
    public function getRoleNameAttribute()
    {
        // Prefer Spatie role name if assigned
        $role = $this->roles()->first();
        if ($role) {
            return $role->name;
        }
        // Fallback to stored string in user_type (post-migration it stores role name)
        return (string)($this->user_type ?? '');
    }

    /**
     * Check if user is SuperAdmin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('SuperAdmin') || (string)$this->user_type === 'SuperAdmin';
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin') || (string)$this->user_type === 'Admin';
    }

    /**
     * Check if user is Employee
     */
    public function isEmployee()
    {
        return $this->hasRole('Employee') || (string)$this->user_type === 'Employee';
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

