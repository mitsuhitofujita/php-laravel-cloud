<?php

namespace Tests\Feature;

use App\Jobs\CreateObserverForUser;
use App\Jobs\CreateOrganizationForObserver;
use App\Models\OrganizationDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class UserCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_observer_and_organization_when_created(): void
    {
        // テスト前の状態を確認
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('observers', 0);
        $this->assertDatabaseCount('organizations', 0);

        // ユーザーを作成
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // リフレッシュしてリレーションシップを更新
        $user = User::find($user->id);

        // 新しくObserverが取得できるはず
        $observer = $user->getDefaultObserver();
        $this->assertNotNull($observer);

        // ObserverDetailが作成されたことを確認
        $this->assertDatabaseHas('observer_details', [
            'observer_id' => $observer->id,
            'name' => 'Test User',
            'description' => 'Default observer for test@example.com',
        ]);

        // 新しくOrganizationが取得できるはず
        $organization = $observer->getDefaultOrganization();
        $this->assertNotNull($organization);

        // OrganizationDetailが作成されたことを確認
        $this->assertDatabaseHas('organization_details', [
            'organization_id' => $organization->id,
        ]);
        $orgDetail = OrganizationDetail::where('organization_id', $organization->id)->first();
        $this->assertStringContainsString('Test User', $orgDetail->name);
        $this->assertStringContainsString('Test User', $orgDetail->description);

        // リレーションシップが正しいことを確認
        $this->assertTrue($user->observers()->exists());
        $this->assertEquals(1, $user->observers()->count());
        $this->assertTrue($observer->organizations()->exists());
        $this->assertEquals(1, $observer->organizations()->count());
    }

    public function test_observer_and_organization_not_duplicated_when_user_saved(): void
    {
        // ユーザーを作成し、Observerとの関連付けを手動で行う
        $user = User::factory()->create([
            'name' => 'Initial Name',
        ]);

        // ジョブを手動で実行
        (new CreateObserverForUser($user))->handle();
        $observer = $user->getDefaultObserver();
        $this->assertNotNull($observer);

        (new CreateOrganizationForObserver($observer))->handle();

        // 初期状態の確認
        $initialObserver = $user->getDefaultObserver();
        $this->assertNotNull($initialObserver);
        $this->assertDatabaseCount('observers', 1);
        $this->assertDatabaseCount('organizations', 1);

        // ユーザー情報を更新して保存
        $user->name = 'Updated Name';
        $user->save();

        // ジョブが再度実行されても重複が起きないことの確認
        (new CreateObserverForUser($user))->handle();
        $observer = $user->getDefaultObserver();
        (new CreateOrganizationForObserver($observer))->handle();

        // Observer数とOrganization数が増えていないことを確認
        $this->assertDatabaseCount('observers', 1);
        $this->assertDatabaseCount('organizations', 1);

        // 同じObserverが関連付けられていることを確認
        $this->assertEquals($initialObserver->id, $user->getDefaultObserver()->id);
    }
}
