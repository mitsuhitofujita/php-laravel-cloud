<?php

namespace Tests\Feature;

use App\Events\ObserverCreated;
use App\Events\UserCreated;
use App\Models\Observer;
use App\Models\ObserverDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_observer_show_page_is_displayed(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
                'name' => 'Test Observer Name',
                'description' => 'Test Observer Description',
            ]);

            $user->observers()->attach($observer);

            $response = $this
                ->actingAs($user)
                ->get(route('observer.show'));

            $response->assertOk();
            $response->assertViewIs('observer.show');
            $response->assertSee('Test Observer Name');
            $response->assertSee('Test Observer Description');
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_observer_edit_page_is_displayed(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
                'name' => 'Test Observer Name',
                'description' => 'Test Observer Description',
            ]);

            $user->observers()->attach($observer);

            $response = $this
                ->actingAs($user)
                ->get(route('observer.edit'));

            $response->assertOk();
            $response->assertViewIs('observer.edit');
            $response->assertSee('Test Observer Name');
            $response->assertSee('Test Observer Description');
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_observer_can_be_updated(): void
    {
        Event::fakeFor(function () {
            $originalUser = User::factory()->create();
            $originalObserver = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $originalObserver->id,
                'name' => 'Test Original Observer Name',
                'description' => 'Test Original Observer Description',
            ]);

            $originalUser->observers()->attach($originalObserver);

            $response = $this
                ->actingAs($originalUser)
                ->put(route('observer.update'), [
                    'name' => 'Test Updated Observer Name',
                    'description' => 'Test Updated Observer Description',
                ]);

            $response->assertRedirect(route('observer.show'));
            $response->assertSessionHas('status', 'observer-updated');

            // 新しいObserverDetailが作成されたことを確認
            $this->assertDatabaseHas('observer_details', [
                'observer_id' => $originalObserver->id,
                'name' => 'Test Updated Observer Name',
                'description' => 'Test Updated Observer Description',
            ]);

            // 元のObserverDetailも残っていることを確認
            $this->assertDatabaseHas('observer_details', [
                'observer_id' => $originalObserver->id,
                'name' => 'Test Original Observer Name',
                'description' => 'Test Original Observer Description',
            ]);
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_observer_show_with_missing_observer_returns_404(): void
    {
        Event::fakeFor(function () {
            // Observerを持たないユーザーを作成
            $user = User::factory()->create();

            // Observerが存在しない状態で閲覧ページにアクセス
            $response = $this
                ->actingAs($user)
                ->get(route('observer.show'));

            // 404エラーが返されることを確認
            $response->assertNotFound();
        }, [UserCreated::class]);
    }

    public function test_observer_edit_with_missing_observer_returns_404(): void
    {
        Event::fakeFor(function () {
            // Observerを持たないユーザーを作成
            $user = User::factory()->create();

            // Observerが存在しない状態で編集ページにアクセス
            $response = $this
                ->actingAs($user)
                ->get(route('observer.edit'));

            // 404エラーが返されることを確認
            $response->assertNotFound();
        }, [UserCreated::class]);
    }

    public function test_observer_update_with_missing_observer_returns_404(): void
    {
        Event::fakeFor(function () {
            // Observerを持たないユーザーを作成
            $user = User::factory()->create();

            // Observerが存在しない状態で更新を試みる
            $response = $this
                ->actingAs($user)
                ->put(route('observer.update'), [
                    'name' => 'Test Observer Name',
                    'description' => 'Test Observer Description',
                ]);

            // 404エラーが返されることを確認
            $response->assertNotFound();
        }, [UserCreated::class]);
    }

    public function test_observer_update_validation_errors(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
            ]);

            $user->observers()->attach($observer);

            // 名前が未入力の場合
            $response = $this
                ->actingAs($user)
                ->put(route('observer.update'), [
                    'name' => '',
                    'description' => 'Test Description',
                ]);

            $response->assertSessionHasErrors('name');

            // 名前が長すぎる場合
            $response = $this
                ->actingAs($user)
                ->put(route('observer.update'), [
                    'name' => str_repeat('a', 256),
                    'description' => 'Test Description',
                ]);

            $response->assertSessionHasErrors('name');

            // 説明が長すぎる場合
            $response = $this
                ->actingAs($user)
                ->put(route('observer.update'), [
                    'name' => 'Test Name',
                    'description' => str_repeat('a', 1001),
                ]);

            $response->assertSessionHasErrors('description');
        }, [UserCreated::class]);
    }
}
