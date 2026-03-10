<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Admin', 'Manager', 'Laborant', 'QC', 'Client'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $users = [
            ['name' => 'Admin', 'email' => 'admin@test.com', 'role' => 'Admin'],
            ['name' => 'Manager', 'email' => 'manager@test.com', 'role' => 'Manager'],
            ['name' => 'Laborant', 'email' => 'lab@test.com', 'role' => 'Laborant'],
            ['name' => 'QC Reviewer', 'email' => 'qc@test.com', 'role' => 'QC'],
            ['name' => 'Client', 'email' => 'client@test.com', 'role' => 'Client'],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('test123')
                ]
            );
            $user->assignRole($u['role']);
        }
    }
}