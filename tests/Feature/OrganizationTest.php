<?php

namespace Tests\Feature;

use App\Events\UserCreated;
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
        // イベントリスナーをモックして、自動Observer作成を防止
        Event::fake([UserCreated::class]);

        $user = User::factory()->create();
        $observer = Observer::factory()->create();
        ObserverDetail::factory()->create([
            'observer_id' => $observer->id,
        ]);

        $organization = Organization::factory()->create();
        $detail = OrganizationDetail::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Test Organization',
            'description' => 'Test Description',
        ]);

        $user->observers()->attach($observer);
        $observer->organizations()->attach($organization);

        $response = $this
            ->actingAs($user)
            ->get(route('organization.show'));

        $response->assertOk();
        $response->assertViewIs('organization.show');
        $response->assertSee('Test Organization');
        $response->assertSee('Test Description');
    }

    public function test_organization_edit_page_is_displayed(): void
    {
        // イベントリスナーをモックして、自動Observer作成を防止
        Event::fake([UserCreated::class]);

        $user = User::factory()->create();
        $observer = Observer::factory()->create();
        ObserverDetail::factory()->create([
            'observer_id' => $observer->id,
        ]);

        $organization = Organization::factory()->create();
        $detail = OrganizationDetail::factory()->create([
            'organization_id' => $organization->id,
            'name' => 'Test Organization',
            'description' => 'Test Description',
        ]);

        $user->observers()->attach($observer);
        $observer->organizations()->attach($organization);

        $response = $this
            ->actingAs($user)
            ->get(route('organization.edit'));

        $response->assertOk();
        $response->assertViewIs('organization.edit');
        $response->assertSee('Test Organization');
        $response->assertSee('Test Description');
    }

    public function test_organization_can_be_updated(): void
    {
        // イベントリスナーをモックして、自動Observer作成を防止
        Event::fake([UserCreated::class]);

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
    }

    public function test_organization_show_with_missing_organization_returns_404(): void
    {
        // イベントリスナーをモックして、自動Observer作成を防止
        Event::fake([UserCreated::class]);

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
    }

    public function test_organization_edit_with_missing_organization_returns_404(): void
    {
        // イベントリスナーをモックして、自動Observer作成を防止
        Event::fake([UserCreated::class]);

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
    }

    public function test_organization_update_with_missing_organization_returns_404(): void
    {
        // イベントリスナーをモックして、自動Observer作成を防止
        Event::fake([UserCreated::class]);

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
    }

    public function test_organization_update_validation_errors(): void
    {
        // イベントリスナーをモックして、自動Observer作成を防止
        Event::fake([UserCreated::class]);

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
    }
}
