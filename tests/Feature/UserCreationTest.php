<?php

namespace Tests\Feature;

use App\Events\UserCreated;
use App\Events\ObserverCreated;
use App\Jobs\CreateObserverForUser;
use App\Jobs\CreateOrganizationForObserver;
use App\Models\Observer;
use App\Models\ObserverDetail;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_observer_and_organization_when_created(): void
    {
        Event::fakeFor(function () {
            // テスト前の状態を確認
            $this->assertDatabaseCount('users', 0);
            $this->assertDatabaseCount('observers', 0);
            $this->assertDatabaseCount('organizations', 0);

            // ユーザーを作成
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            Event::assertDispatched(UserCreated::class);

            $observer = Observer::factory()->create();
            ObserverDetail::factory()->create([
                'observer_id' => $observer->id,
                'name' => 'Test Observer Name',
                'description' => 'Test Observer Name',
            ]);
            Event::assertDispatched(ObserverCreated::class);

            $organization = Organization::factory()->create();
            OrganizationDetail::factory()->create([
                'organization_id' => $organization->id,
                'name' => 'Test Organization Name',
                'description' => 'Test Organization Name',
            ]);

            $user->observers()->attach($observer);
            $observer->organizations()->attach($organization);

            // リフレッシュしてリレーションシップを更新
            $user = User::find($user->id);

            // 新しくObserverが取得できるはず
            $observer = $user->getDefaultObserver();
            $this->assertNotNull($observer);

            // 新しくOrganizationが取得できるはず
            $organization = $observer->getDefaultOrganization();
            $this->assertNotNull($organization);

            // リレーションシップが正しいことを確認
            $this->assertTrue($user->observers()->exists());
            $this->assertEquals(1, $user->observers()->count());
            $this->assertTrue($observer->organizations()->exists());
            $this->assertEquals(1, $observer->organizations()->count());
        }, [UserCreated::class, ObserverCreated::class]);
    }

    public function test_observer_and_organization_not_duplicated_when_user_saved(): void
    {
        Event::fakeFor(function () {
            // ユーザーを作成し、Observerとの関連付けを手動で行う
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
            Event::assertDispatched(UserCreated::class);

            // ジョブを手動で実行
            (new CreateObserverForUser($user))->handle();
            $observer = $user->getDefaultObserver();
            $this->assertNotNull($observer);

            (new CreateOrganizationForObserver($observer))->handle();
            $organization = $observer->getDefaultOrganization();
            $this->assertNotNull($organization);

            // 初期状態の確認
            $initialObserver = $user->getDefaultObserver();
            $this->assertNotNull($initialObserver);
            $this->assertDatabaseCount('observers', 1);

            $initialOrganization = $initialObserver->getDefaultOrganization();
            $this->assertNotNull($initialOrganization);
            $this->assertDatabaseCount('organizations', 1);

            // ユーザー情報を更新して保存
            $user->name = 'Updated Name';
            $user->save();

            // ジョブが再度実行されても重複が起きないことの確認
            (new CreateObserverForUser($user))->handle();
            $observer = $user->getDefaultObserver();
            $this->assertDatabaseCount('observers', 1);
            $this->assertEquals($initialObserver->id, $user->getDefaultObserver()->id);

            (new CreateOrganizationForObserver($observer))->handle();
            $organization = $observer->getDefaultOrganization();
            $this->assertDatabaseCount('organizations', 1);
            $this->assertEquals($initialOrganization->id, $observer->getDefaultOrganization()->id);
        }, [UserCreated::class, ObserverCreated::class]);
    }
}
