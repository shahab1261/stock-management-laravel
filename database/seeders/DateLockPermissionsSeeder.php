<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DateLockPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // New permissions to add for Date Lock
        $newPermissions = [
            'management.date-lock.view',
            'management.date-lock.edit',
        ];

        // Create new permissions
        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Get SuperAdmin role
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        if ($superAdminRole) {
            // Give all new permissions to SuperAdmin
            $superAdminRole->givePermissionTo($newPermissions);
        }

        // Get Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            // Give all new permissions to Admin as well
            $adminRole->givePermissionTo($newPermissions);
        }

        $this->command->info('Date Lock permissions added and assigned to SuperAdmin and Admin roles successfully!');
    }
}
