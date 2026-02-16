<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $client = Role::findByName('client');
        $provider = Role::findByName('provider');
        $admin = Role::findByName('admin');

        // client permissions
        $client->syncPermissions([
            'create projects',
            'edit own projects',
            'delete own projects',
        ]);

        // provider permissions (inherits conceptually, but we re-assign explicitly)
        $provider->syncPermissions([
            'create projects',
            'edit own projects',

            'offer services',
            'manage services',
        ]);

        // admin = everything
        $admin->syncPermissions(Permission::all());
    }
}
