<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sample;
use App\Models\Measurement;
use App\Models\Method;
use App\Models\ResultSet;
use App\Models\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResultWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user with permissions
        $this->user = User::factory()->create();
        $this->user->assignRole('Laborant');
        
        // Create QC user
        $this->qcUser = User::factory()->create();
        $this->qcUser->assignRole('QC/Reviewer');
        
        // Create test method with schema
        $this->method = Method::create([
            'name' => 'Test Method',
            'version' => '1.0',
            'status' => 'PUBLISHED',
            'schema_json' => [
                'fields' => [
                    [
                        'key' => 'ph_value',
                        'label' => 'pH Value',
                        'type' => 'number',
                        'required' => true,
                        'unit' => 'pH',
                        'min' => 0,
                        'max' => 14
                    ],
                    [
                        'key' => 'temperature',
                        'label' => 'Temperature',
                        'type' => 'number',
                        'required' => true,
                        'unit' => '°C',
                        'min' => 0,
                        'max' => 100
                    ]
                ]
            ],
            'created_by' => $this->user->id
        ]);
        
        // Create test sample
        $this->sample = Sample::create([
            'sample_code' => 'TEST-001',
            'name' => 'Test Sample',
            'type' => 'Test',
            'status' => 'REGISTERED',
            'quantity' => 100,
            'unit' => 'mL',
            'created_by' => $this->user->id
        ]);
        
        // Create measurement
        $this->measurement = Measurement::create([
            'sample_id' => $this->sample->id,
            'method_id' => $this->method->id,
            'status' => 'PLANNED',
            'assignee_id' => $this->user->id,
            'priority' => 1
        ]);
    }

    public function test_laborant_can_create_draft_results()
    {
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 7.5,
                    'temperature' => 25.0
                ]
            ])
            ->assertSuccessful()
            ->assertJson(['success' => true]);
            
        $resultSet = ResultSet::where('measurement_id', $this->measurement->id)->first();
        $this->assertNotNull($resultSet);
        $this->assertEquals('DRAFT', $resultSet->status);
        
        $results = $resultSet->results()->pluck('value_num', 'field_key')->toArray();
        $this->assertEquals(7.5, $results['ph_value']);
        $this->assertEquals(25.0, $results['temperature']);
    }

    public function test_laborant_can_submit_results()
    {
        // First create draft
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 7.5,
                    'temperature' => 25.0
                ]
            ]);

        $resultSet = ResultSet::where('measurement_id', $this->measurement->id)->first();
        
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/submit")
            ->assertSuccessful()
            ->assertJson(['success' => true]);
            
        $resultSet->refresh();
        $this->assertEquals('SUBMITTED', $resultSet->status);
        $this->assertEquals($this->user->id, $resultSet->submitted_by);
    }

    public function test_qc_can_approve_submitted_results()
    {
        // Create and submit results
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 7.5,
                    'temperature' => 25.0
                ]
            ]);

        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/submit");

        $resultSet = ResultSet::where('measurement_id', $this->measurement->id)->first();
        
        $this->actingAs($this->qcUser)
            ->post("/measurements/{$this->measurement->id}/results/approve")
            ->assertSuccessful()
            ->assertJson(['success' => true]);
            
        $resultSet->refresh();
        $this->assertEquals('APPROVED', $resultSet->status);
        $this->assertEquals($this->qcUser->id, $resultSet->approved_by);
    }

    public function test_qc_can_reject_submitted_results()
    {
        // Create and submit results
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 7.5,
                    'temperature' => 25.0
                ]
            ]);

        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/submit");

        $resultSet = ResultSet::where('measurement_id', $this->measurement->id)->first();
        
        $this->actingAs($this->qcUser)
            ->post("/measurements/{$this->measurement->id}/results/reject", [
                'reason' => 'Test rejection'
            ])
            ->assertSuccessful()
            ->assertJson(['success' => true]);
            
        $resultSet->refresh();
        $this->assertEquals('REJECTED', $resultSet->status);
        $this->assertEquals($this->qcUser->id, $resultSet->rejected_by);
        $this->assertEquals('Test rejection', $resultSet->rejection_reason);
    }

    public function test_cannot_edit_locked_results_without_permission()
    {
        // Create, submit, and approve results
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 7.5,
                    'temperature' => 25.0
                ]
            ]);

        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/submit");

        $this->actingAs($this->qcUser)
            ->post("/measurements/{$this->measurement->id}/results/approve");

        // Try to edit as laborant (should fail)
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 8.0,
                    'temperature' => 26.0
                ]
            ])
            ->assertStatus(403);
    }

    public function test_audit_log_is_created_for_result_changes()
    {
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 7.5,
                    'temperature' => 25.0
                ]
            ]);

        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => 'result_set',
            'action' => 'save_draft',
            'user_id' => $this->user->id
        ]);
    }

    public function test_validation_works_for_required_fields()
    {
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 7.5
                    // Missing required temperature field
                ]
            ])
            ->assertStatus(422);
    }

    public function test_validation_works_for_numeric_ranges()
    {
        $this->actingAs($this->user)
            ->post("/measurements/{$this->measurement->id}/results/save-draft", [
                'results' => [
                    'ph_value' => 15.0, // Above max of 14
                    'temperature' => 25.0
                ]
            ])
            ->assertStatus(422);
    }
}
