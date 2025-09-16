<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class NewPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // New permissions to add
        $newPermissions = [
            // Sales specific actions
            'sales.nozzle.delete',
            'sales.lubricant.delete',
            'sales.credit.edit',

            // Billing module
            'billing.view',
            'billing.export',
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

        $this->command->info('New permissions added and assigned to SuperAdmin and Admin roles successfully!');
    }
}
