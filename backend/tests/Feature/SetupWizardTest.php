<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetupWizardTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_owner_sees_dashboard_without_forced_setup(): void
    {
        $clinic = Clinic::factory()->create(['settings' => null]);
        $owner = User::factory()->create([
            'clinic_id' => $clinic->id,
            'role' => 'owner',
        ]);

        $response = $this->actingAs($owner)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_completed_owner_sees_dashboard(): void
    {
        $clinic = Clinic::factory()->create(['settings' => ['setup_completed' => true]]);
        $owner = User::factory()->create([
            'clinic_id' => $clinic->id,
            'role' => 'owner',
        ]);

        $response = $this->actingAs($owner)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_skip_marks_setup_completed_and_shows_dashboard(): void
    {
        $clinic = Clinic::factory()->create(['settings' => null]);
        $owner = User::factory()->create([
            'clinic_id' => $clinic->id,
            'role' => 'owner',
        ]);

        $response = $this->actingAs($owner)->get('/setup/skip');
        $response->assertRedirect(route('dashboard'));

        $clinic->refresh();
        $this->assertTrue((bool) data_get($clinic->settings, 'setup_completed', false));
    }

    public function test_wizard_save_step_works(): void
    {
        $clinic = Clinic::factory()->create(['settings' => null]);
        $owner = User::factory()->create([
            'clinic_id' => $clinic->id,
            'role' => 'owner',
        ]);

        $response = $this->actingAs($owner)->postJson('/setup/save', [
            'step' => 'clinic-info',
            'name' => 'Apollo Hospital',
            'phone' => '9876543210',
            'city' => 'Pune',
        ]);

        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('clinics', ['id' => $clinic->id, 'name' => 'Apollo Hospital']);
    }
}
