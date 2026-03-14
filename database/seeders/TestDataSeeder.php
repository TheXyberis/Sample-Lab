<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sample;
use App\Models\Client;
use App\Models\Project;
use App\Models\Measurement;
use App\Models\Method;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create test clients
        $clients = [
            ['name' => 'PharmaCorp International', 'contact_email' => 'lab@pharmacorp.com'],
            ['name' => 'Acme Food Solutions', 'contact_email' => 'quality@acmefood.com'],
            ['name' => 'BioTech Solutions', 'contact_email' => 'testing@biotech.com'],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate(['name' => $client['name']], $client);
        }

        // Create test projects
        $projects = [
            ['name' => 'Vax-Alpha-Freeze-Dry Study', 'client_id' => 1],
            ['name' => 'Food Safety Validation', 'client_id' => 2],
            ['name' => 'Biotech Process Optimization', 'client_id' => 3],
        ];

        foreach ($projects as $project) {
            Project::firstOrCreate(['name' => $project['name']], $project);
        }

        // Create test samples
        $samples = [
            [
                'sample_code' => 'S-2026-0006',
                'name' => 'Vax-Alpha-Freeze-Dry',
                'type' => 'Vaccine Sample',
                'status' => 'REGISTERED',
                'quantity' => 50,
                'unit' => 'mL',
                'client_id' => 1,
                'project_id' => 1,
                'created_by' => 1,
            ],
            [
                'sample_code' => 'S-2026-0005',
                'name' => 'Raw Milk Batch-B14',
                'type' => 'Food Sample',
                'status' => 'REGISTERED',
                'quantity' => 100,
                'unit' => 'L',
                'client_id' => 2,
                'project_id' => 2,
                'created_by' => 1,
            ],
            [
                'sample_code' => 'S-2025-0001',
                'name' => 'Sample A1',
                'type' => 'Test Sample',
                'status' => 'IN_PROGRESS',
                'quantity' => 25,
                'unit' => 'g',
                'client_id' => 1,
                'project_id' => 1,
                'created_by' => 1,
            ],
            [
                'sample_code' => 'S-2025-0002',
                'name' => 'Sample A2',
                'type' => 'Test Sample',
                'status' => 'COMPLETED',
                'quantity' => 30,
                'unit' => 'g',
                'client_id' => 1,
                'project_id' => 1,
                'created_by' => 1,
            ],
            [
                'sample_code' => 'S-2025-0003',
                'name' => 'Batch B1',
                'type' => 'Production Batch',
                'status' => 'COMPLETED',
                'quantity' => 200,
                'unit' => 'kg',
                'client_id' => 2,
                'project_id' => 2,
                'created_by' => 1,
            ],
            [
                'sample_code' => 'S-2025-0004',
                'name' => 'Validation V1',
                'type' => 'Validation Sample',
                'status' => 'REGISTERED',
                'quantity' => 15,
                'unit' => 'mL',
                'client_id' => 3,
                'project_id' => 3,
                'created_by' => 1,
            ],
        ];

        foreach ($samples as $sample) {
            Sample::firstOrCreate(['sample_code' => $sample['sample_code']], $sample);
        }

        // Create measurements for samples
        $methods = Method::all();
        $samples = Sample::all();

        foreach ($samples as $sample) {
            // Add 2-4 measurements per sample
            $measurementCount = rand(2, 4);
            $selectedMethods = $methods->random($measurementCount);

            foreach ($selectedMethods as $index => $method) {
                Measurement::firstOrCreate([
                    'sample_id' => $sample->id,
                    'method_id' => $method->id,
                ], [
                    'status' => in_array($sample->status, ['IN_PROGRESS', 'COMPLETED']) ? 'DONE' : 'PLANNED',
                    'assignee_id' => 1,
                    'planned_at' => now()->addDays($index),
                    'priority' => 1, // normal priority
                ]);
            }
        }

        // Create additional test users with roles
        $testUsers = [
            ['name' => 'John Laborant', 'email' => 'laborant@samplelab.local', 'role' => 'Laborant'],
            ['name' => 'Jane QC', 'email' => 'qc@samplelab.local', 'role' => 'QC/Reviewer'],
            ['name' => 'Bob Manager', 'email' => 'manager@samplelab.local', 'role' => 'Manager'],
            ['name' => 'Alice Client', 'email' => 'client@samplelab.local', 'role' => 'Client'],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(['email' => $userData['email']], [
                'name' => $userData['name'],
                'password' => Hash::make('password123'),
            ]);

            // Assign role
            $role = \Spatie\Permission\Models\Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
            }
        }
    }
}
