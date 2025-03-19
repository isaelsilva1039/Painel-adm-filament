<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'product_resource.view_any',
            'product_resource.view',
            'product_resource.create',
            'product_resource.update',
            'product_resource.delete',
            'product_resource.restore',
            'product_resource.force_delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web', // Certifique-se de que o guard é 'web'
            ]);
        }
    }
}
