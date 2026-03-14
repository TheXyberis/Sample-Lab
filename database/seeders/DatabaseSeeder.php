<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in correct order
        $this->call([
            RbacSeeder::class,        // Create RBAC system (roles, permissions)
            MethodSeeder::class,      // Create test methods with schema_json
            TestDataSeeder::class,    // Create test samples and measurements
            RoleAndUserSeeder::class,  // Create existing users (for compatibility)
        ]);
    }
}
