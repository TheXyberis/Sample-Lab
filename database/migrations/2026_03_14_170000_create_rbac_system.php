<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('group');
            $table->timestamps();
        });

        // Create roles table (if not exists)
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Create role_has_permissions table
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        // Create model_has_permissions table
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->primary(['permission_id', 'model_id', 'model_type']);

            $table->index(['model_id', 'model_type']);
        });

        // Create model_has_roles table
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['role_id', 'model_id', 'model_type']);

            $table->index(['model_id', 'model_type']);
        });

        // Seed permissions
        $this->seedPermissions();
    }

    private function seedPermissions()
    {
        $permissions = [
            // Samples permissions
            ['name' => 'samples:create', 'display_name' => 'Create Samples', 'group' => 'Samples'],
            ['name' => 'samples:read', 'display_name' => 'View Samples', 'group' => 'Samples'],
            ['name' => 'samples:update', 'display_name' => 'Update Samples', 'group' => 'Samples'],
            ['name' => 'samples:archive', 'display_name' => 'Archive Samples', 'group' => 'Samples'],
            ['name' => 'samples:import', 'display_name' => 'Import Samples', 'group' => 'Samples'],

            // Measurements permissions
            ['name' => 'measurements:plan', 'display_name' => 'Plan Measurements', 'group' => 'Measurements'],
            ['name' => 'measurements:start', 'display_name' => 'Start Measurements', 'group' => 'Measurements'],
            ['name' => 'measurements:finish', 'display_name' => 'Finish Measurements', 'group' => 'Measurements'],
            ['name' => 'measurements:read', 'display_name' => 'View Measurements', 'group' => 'Measurements'],

            // Results permissions
            ['name' => 'results:edit', 'display_name' => 'Edit Results', 'group' => 'Results'],
            ['name' => 'results:submit', 'display_name' => 'Submit Results', 'group' => 'Results'],
            ['name' => 'results:review', 'display_name' => 'Review Results', 'group' => 'Results'],
            ['name' => 'results:approve', 'display_name' => 'Approve Results', 'group' => 'Results'],
            ['name' => 'results:reject', 'display_name' => 'Reject Results', 'group' => 'Results'],
            ['name' => 'results:lock', 'display_name' => 'Lock Results', 'group' => 'Results'],
            ['name' => 'results:unlock', 'display_name' => 'Unlock Results', 'group' => 'Results'],

            // Reports permissions
            ['name' => 'reports:generate', 'display_name' => 'Generate Reports', 'group' => 'Reports'],
            ['name' => 'reports:download', 'display_name' => 'Download Reports', 'group' => 'Reports'],

            // Methods permissions
            ['name' => 'methods:create', 'display_name' => 'Create Methods', 'group' => 'Methods'],
            ['name' => 'methods:version', 'display_name' => 'Version Methods', 'group' => 'Methods'],
            ['name' => 'methods:publish', 'display_name' => 'Publish Methods', 'group' => 'Methods'],
            ['name' => 'methods:read', 'display_name' => 'View Methods', 'group' => 'Methods'],

            // Users permissions
            ['name' => 'users:manage', 'display_name' => 'Manage Users', 'group' => 'Administration'],

            // Audit permissions
            ['name' => 'audit:read', 'display_name' => 'View Audit Log', 'group' => 'Administration'],

            // Integrations permissions
            ['name' => 'integrations:manage', 'display_name' => 'Manage Integrations', 'group' => 'Administration'],
        ];

        foreach ($permissions as $permission) {
            \DB::table('permissions')->insert($permission);
        }
    }

    public function down()
    {
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
    }
};
