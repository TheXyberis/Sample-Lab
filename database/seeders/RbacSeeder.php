<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    public function run()
    {
        // Create roles using Spatie structure
        $roles = [
            ['name' => 'Admin', 'guard_name' => 'web'],
            ['name' => 'Manager', 'guard_name' => 'web'],
            ['name' => 'Laborant', 'guard_name' => 'web'],
            ['name' => 'QC/Reviewer', 'guard_name' => 'web'],
            ['name' => 'Client', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            if (!\Spatie\Permission\Models\Role::where('name', $role['name'])->exists()) {
                \Spatie\Permission\Models\Role::create($role);
            }
        }

        // Assign permissions to roles
        $rolePermissions = [
            'Admin' => '*', // All permissions
            
            'Manager' => [
                'samples:read', 'samples:update',
                'measurements:plan', 'measurements:start', 'measurements:finish', 'measurements:read',
                'results:review', 'results:approve', 'results:lock', 'results:unlock',
                'reports:generate', 'reports:download',
                'methods:read',
                'audit:read',
            ],
            
            'Laborant' => [
                'samples:create', 'samples:read', 'samples:update',
                'measurements:start', 'measurements:finish', 'measurements:read',
                'results:edit', 'results:submit',
                'methods:read',
            ],
            
            'QC/Reviewer' => [
                'samples:read',
                'measurements:read',
                'results:review', 'results:approve', 'results:reject', 'results:lock', 'results:unlock',
                'reports:generate', 'reports:download',
                'methods:read',
                'audit:read',
            ],
            
            'Client' => [
                'samples:read',
                'measurements:read',
                'reports:download',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            
            if ($permissions === '*') {
                // Give all permissions to Admin
                $allPermissions = \Spatie\Permission\Models\Permission::all();
                $role->syncPermissions($allPermissions);
            } else {
                $permissionModels = \Spatie\Permission\Models\Permission::whereIn('name', $permissions)->get();
                $role->syncPermissions($permissionModels);
            }
        }

        // Create admin user
        if (!\App\Models\User::where('email', 'admin@samplelab.local')->exists()) {
            $adminUser = \App\Models\User::create([
                'name' => 'System Administrator',
                'email' => 'admin@samplelab.local',
                'password' => Hash::make('admin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign admin role to admin user
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
            $adminUser->assignRole($adminRole);
        }
    }
}
