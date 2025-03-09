<?php

namespace Tests\Feature;

use App\Events\UserCreated;
use App\Events\ObserverCreated;
use App\Models\Observer;
use App\Models\ObserverDetail;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_show_page_is_displayed(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
                'name' => 'Test Observer Name',
                'description' => 'Test Observer Description',
            ]);

            $organization = Organization::factory()->create();
            OrganizationDetail::factory()->create([
                'organization_id' => $organization->id,
                'name' => 'Test Organization Name',
                'description' => 'Test Organization Description',
            ]);

            $user->observers()->attach($observer);
            $observer->organizations()->attach($organization);

            $this->assertDatabaseCount('observer_user', 1);
            $this->assertDatabaseCount('observer_organization', 1);

            $response = $this
                ->actingAs($user)
                ->get(route('organization.show'));

            $response->assertOk();
            $response->assertViewIs('organization.show');
            $response->assertSee('Test Organization Name');
            $response->assertSee('Test Organization Description');
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_organization_edit_page_is_displayed(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);

            $organization = Organization::factory()->create();
            OrganizationDetail::factory()->create([
                'organization_id' => $organization->id,
                'name' => 'Test Organization Name',
                'description' => 'Test Organization Description',
            ]);

            $user->observers()->attach($observer);
            $observer->organizations()->attach($organization);

            $response = $this
                ->actingAs($user)
                ->get(route('organization.edit'));

            $response->assertOk();
            $response->assertViewIs('organization.edit');
            $response->assertSee('Test Organization Name');
            $response->assertSee('Test Organization Description');
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_organization_can_be_updated(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);

            $organization = Organization::factory()->create();
            OrganizationDetail::factory()->create([
                'organization_id' => $organization->id,
                'name' => 'Original Name',
                'description' => 'Original Description',
            ]);

            $user->observers()->attach($observer);
            $observer->organizations()->attach($organization);

            $response = $this
                ->actingAs($user)
                ->put(route('organization.update'), [
                    'name' => 'Updated Name',
                    'description' => 'Updated Description',
                ]);

            $response->assertRedirect(route('organization.show'));
            $response->assertSessionHas('status', 'organization-updated');

            // 新しいOrganizationDetailが作成されたことを確認
            $this->assertDatabaseHas('organization_details', [
                'organization_id' => $organization->id,
                'name' => 'Updated Name',
                'description' => 'Updated Description',
            ]);

            // 元のOrganizationDetailも残っていることを確認
            $this->assertDatabaseHas('organization_details', [
                'organization_id' => $organization->id,
                'name' => 'Original Name',
                'description' => 'Original Description',
            ]);
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_organization_show_with_missing_organization_returns_404(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);

            $user->observers()->attach($observer);
            // Organizationは作成しない

            $response = $this
                ->actingAs($user)
                ->get(route('organization.show'));

            $response->assertNotFound();
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_organization_edit_with_missing_organization_returns_404(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);

            $user->observers()->attach($observer);
            // Organizationは作成しない

            $response = $this
                ->actingAs($user)
                ->get(route('organization.edit'));

            $response->assertNotFound();
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_organization_update_with_missing_organization_returns_404(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);

            $user->observers()->attach($observer);
            // Organizationは作成しない

            $response = $this
                ->actingAs($user)
                ->put(route('organization.update'), [
                    'name' => 'Test Name',
                    'description' => 'Test Description',
                ]);

            $response->assertNotFound();
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_organization_update_validation_errors(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);

            $organization = Organization::factory()->create();
            OrganizationDetail::factory()->create([
                'organization_id' => $organization->id,
            ]);

            $user->observers()->attach($observer);
            $observer->organizations()->attach($organization);

            // 名前が未入力の場合
            $response = $this
                ->actingAs($user)
                ->put(route('organization.update'), [
                    'name' => '',
                    'description' => 'Test Description',
                ]);

            $response->assertSessionHasErrors('name');

            // 名前が長すぎる場合
            $response = $this
                ->actingAs($user)
                ->put(route('organization.update'), [
                    'name' => str_repeat('a', 256),
                    'description' => 'Test Description',
                ]);

            $response->assertSessionHasErrors('name');

            // 説明が長すぎる場合
            $response = $this
                ->actingAs($user)
                ->put(route('organization.update'), [
                    'name' => 'Test Name',
                    'description' => str_repeat('a', 1001),
                ]);

            $response->assertSessionHasErrors('description');
        }, [UserCreated::class, ObserverCreated::class]);
    }
}
