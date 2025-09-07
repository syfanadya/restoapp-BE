<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar Permission
        $permissions = [
            'manage tables',
            'manage orders',
            'manage order items',
            'manage foods',
            'manage payments',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Role Pelayan
        $waiterRole = Role::firstOrCreate(['name' => 'waiter']);
        $waiterPermissions = [
            'manage tables',
            'manage orders',
            'manage order items',
            'manage foods',
        ];
        $waiterRole->syncPermissions($waiterPermissions);

        // Role Kasir
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $cashierPermissions = [
            'manage orders',
            'manage payments',
        ];
        $cashierRole->syncPermissions($cashierPermissions);

        // User Waiter
        $waiterUser = User::create([
            'name' => 'WaiterResto',
            'email' => 'waiter@resto.com',
            'password' => bcrypt('waiter123')
        ]);

        $waiterUser->assignRole($waiterRole);

        // User Cashier
        $cashierUser = User::create([
            'name' => 'CashierResto',
            'email' => 'cashier@resto.com',
            'password' => bcrypt('cashier123')
        ]);

        $cashierUser->assignRole($cashierRole);
    }
}
