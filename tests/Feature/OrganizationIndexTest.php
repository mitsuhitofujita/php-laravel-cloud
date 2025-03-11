<?php

namespace Tests\Feature;

use App\Models\Observer;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrganizationIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizations_index_page_shows_all_organizations(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            
            // Create multiple organizations
            $organization1 = Organization::factory()->create();
            $organization2 = Organization::factory()->create();
            
            OrganizationDetail::factory()->create([
                'organization_id' => $organization1->id,
                'name' => 'First Test Organization',
                'description' => 'First Test Description',
            ]);
            
            OrganizationDetail::factory()->create([
                'organization_id' => $organization2->id,
                'name' => 'Second Test Organization',
                'description' => 'Second Test Description',
            ]);
            
            // Associate organizations with observer
            $observer->organizations()->attach([$organization1->id, $organization2->id]);
            
            // Associate observer with user
            $user->observers()->attach($observer);
            
            $response = $this
                ->actingAs($user)
                ->get(route('organization.index'));
                
            $response->assertOk();
            $response->assertViewIs('organization.index');
            
            // Check if both organizations are displayed
            $response->assertSee('First Test Organization');
            $response->assertSee('First Test Description');
            $response->assertSee('Second Test Organization');
            $response->assertSee('Second Test Description');
        });
    }
    
    public function test_can_create_new_organization(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            $user->observers()->attach($observer);
            
            $response = $this
                ->actingAs($user)
                ->post(route('organization.store'), [
                    'name' => 'New Organization',
                    'description' => 'New Organization Description',
                ]);
                
            $organization = Organization::first();
            
            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('organization.show', ['organizationId' => $organization->id]));
                
            $this->assertDatabaseHas('organization_details', [
                'organization_id' => $organization->id,
                'name' => 'New Organization',
                'description' => 'New Organization Description',
            ]);
            
            $this->assertDatabaseHas('observer_organization', [
                'observer_id' => $observer->id,
                'organization_id' => $organization->id,
            ]);
        });
    }
}
