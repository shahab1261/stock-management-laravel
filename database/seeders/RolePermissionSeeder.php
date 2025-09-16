<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get existing roles
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $employeeRole = Role::firstOrCreate(['name' => 'Employee']);

        // Create permissions for each module
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Daybook
            'daybook.view',

            // Purchase
            'purchase.view',
            'purchase.create',
            'purchase.edit',
            'purchase.delete',

            // Sales
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'sales.nozzle.view',
            'sales.nozzle.create',
            'sales.nozzle.delete',
            'sales.lubricant.view',
            'sales.lubricant.create',
            'sales.lubricant.delete',
            'sales.credit.view',
            'sales.credit.create',
            'sales.credit.edit',
            'sales.credit.delete',

            // Journal Vouchers
            'journal.view',
            'journal.create',
            'journal.edit',
            'journal.delete',

            // Trial Balance
            'trial-balance.view',
            'trial-balance.export',

            // Profit and Loss
            'profit.view',
            'profit.update-rates',

            // Dips
            'dips.view',
            'dips.create',
            'dips.edit',
            'dips.delete',

            // Wet Stock
            'wet-stock.view',
            'wet-stock.export',

            // Billing
            'billing.view',
            'billing.export',

            // Payments
            'payments.bank-receiving.view',
            'payments.bank-receiving.create',
            'payments.bank-payments.view',
            'payments.bank-payments.create',
            'payments.cash-receiving.view',
            'payments.cash-receiving.create',
            'payments.cash-payments.view',
            'payments.cash-payments.create',
            'payments.transaction.delete',

            // Ledgers
            'ledger.product.view',
            'ledger.supplier.view',
            'ledger.customer.view',
            'ledger.bank.view',
            'ledger.cash.view',
            'ledger.mp.view',
            'ledger.expense.view',
            'ledger.income.view',
            'ledger.employee.view',

            // History
            'history.purchases.view',
            'history.sales.view',
            'history.bank-receivings.view',
            'history.bank-payments.view',
            'history.cash-receipts.view',
            'history.cash-payments.view',
            'history.journal-vouchers.view',

            // Reports
            'reports.account-history.view',
            'reports.all-stocks.view',
            'reports.summary.view',
            'reports.purchase-transport.view',
            'reports.sale-transport.view',

            // Management
            'management.customers.view',
            'management.customers.create',
            'management.customers.edit',
            'management.customers.delete',

            'management.banks.view',
            'management.banks.create',
            'management.banks.edit',
            'management.banks.delete',

            'management.tanklari.view',
            'management.tanklari.create',
            'management.tanklari.edit',
            'management.tanklari.delete',

            'management.drivers.view',
            'management.drivers.create',
            'management.drivers.edit',
            'management.drivers.delete',

            'management.expenses.view',
            'management.expenses.create',
            'management.expenses.edit',
            'management.expenses.delete',

            'management.incomes.view',
            'management.incomes.create',
            'management.incomes.edit',
            'management.incomes.delete',

            'management.nozzles.view',
            'management.nozzles.create',
            'management.nozzles.edit',
            'management.nozzles.delete',

            'management.suppliers.view',
            'management.suppliers.create',
            'management.suppliers.edit',
            'management.suppliers.delete',

            'management.users.view',
            'management.users.create',
            'management.users.edit',
            'management.users.delete',

            'management.terminals.view',
            'management.terminals.create',
            'management.terminals.edit',
            'management.terminals.delete',

            'management.employees.view',
            'management.employees.create',
            'management.employees.edit',
            'management.employees.delete',

            'management.transports.view',
            'management.transports.create',
            'management.transports.edit',
            'management.transports.delete',

            'management.products.view',
            'management.products.create',
            'management.products.edit',
            'management.products.delete',

            'management.tanks.view',
            'management.tanks.create',
            'management.tanks.edit',
            'management.tanks.delete',

            'management.settings.view',
            'management.settings.edit',
            'system_locked',

            // Profile
            'profile.view',
            'profile.edit',
            'profile.change-password',

            // Logs
            'logs.view',

            // Role and Permission Management (SuperAdmin only)
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'permissions.view',
            'permissions.assign',
        ];

        // Create all permissions (skip if they exist)
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to SuperAdmin
        $superAdminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to Admin
        $adminPermissions = [
            'dashboard.view',
            'daybook.view',
            'purchase.view', 'purchase.create', 'purchase.edit',
            'sales.view', 'sales.create', 'sales.edit',
            'sales.nozzle.view', 'sales.nozzle.create', 'sales.nozzle.delete',
            'sales.lubricant.view', 'sales.lubricant.create', 'sales.lubricant.delete',
            'sales.credit.view', 'sales.credit.create', 'sales.credit.edit', 'sales.credit.delete',
            'journal.view', 'journal.create', 'journal.edit',
            'trial-balance.view', 'trial-balance.export',
            'profit.view', 'profit.update-rates',
            'dips.view', 'dips.create', 'dips.edit',
            'wet-stock.view', 'wet-stock.export',
            'billing.view', 'billing.export',
            'payments.bank-receiving.view', 'payments.bank-receiving.create',
            'payments.bank-payments.view', 'payments.bank-payments.create',
            'payments.cash-receiving.view', 'payments.cash-receiving.create',
            'payments.cash-payments.view', 'payments.cash-payments.create',
            'ledger.product.view', 'ledger.supplier.view', 'ledger.customer.view',
            'ledger.bank.view', 'ledger.cash.view', 'ledger.mp.view',
            'ledger.expense.view', 'ledger.income.view', 'ledger.employee.view',
            'history.purchases.view', 'history.sales.view',
            'history.bank-receivings.view', 'history.bank-payments.view',
            'history.cash-receipts.view', 'history.cash-payments.view',
            'history.journal-vouchers.view',
            'reports.account-history.view', 'reports.all-stocks.view',
            'reports.summary.view', 'reports.purchase-transport.view',
            'reports.sale-transport.view',
            'management.customers.view', 'management.customers.create', 'management.customers.edit',
            'management.banks.view', 'management.banks.create', 'management.banks.edit',
            'management.tanklari.view', 'management.tanklari.create', 'management.tanklari.edit',
            'management.drivers.view', 'management.drivers.create', 'management.drivers.edit',
            'management.expenses.view', 'management.expenses.create', 'management.expenses.edit',
            'management.incomes.view', 'management.incomes.create', 'management.incomes.edit',
            'management.nozzles.view', 'management.nozzles.create', 'management.nozzles.edit',
            'management.suppliers.view', 'management.suppliers.create', 'management.suppliers.edit',
            'management.users.view', 'management.users.create', 'management.users.edit',
            'management.terminals.view', 'management.terminals.create', 'management.terminals.edit',
            'management.employees.view', 'management.employees.create', 'management.employees.edit',
            'management.transports.view', 'management.transports.create', 'management.transports.edit',
            'management.products.view', 'management.products.create', 'management.products.edit',
            'management.tanks.view', 'management.tanks.create', 'management.tanks.edit',
            'management.settings.view', 'management.settings.edit',
            'profile.view', 'profile.edit', 'profile.change-password',
            'logs.view',
        ];

        $adminRole->givePermissionTo($adminPermissions);

        // Assign limited permissions to Employee
        $employeePermissions = [
            'dashboard.view',
            'daybook.view',
            'purchase.view',
            'sales.view', 'sales.create',
            'sales.nozzle.view', 'sales.nozzle.create',
            'sales.lubricant.view', 'sales.lubricant.create',
            'sales.credit.view', 'sales.credit.create',
            'dips.view', 'dips.create',
            'wet-stock.view',
            'billing.view',
            'payments.bank-receiving.view', 'payments.bank-receiving.create',
            'payments.cash-receiving.view', 'payments.cash-receiving.create',
            'ledger.product.view', 'ledger.customer.view',
            'history.purchases.view', 'history.sales.view',
            'profile.view', 'profile.edit', 'profile.change-password',
        ];

        $employeeRole->givePermissionTo($employeePermissions);

        // Ensure users have a role matching user_type string if present
        $users = User::all();
        foreach ($users as $user) {
            $roleName = (string) $user->user_type;
            if (in_array($roleName, ['SuperAdmin', 'Admin', 'Employee'])) {
                $user->syncRoles([$roleName]);
            }
        }
    }
}
