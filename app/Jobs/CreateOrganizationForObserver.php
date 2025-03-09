<?php

namespace App\Jobs;

use App\Models\Observer;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateOrganizationForObserver implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Observerインスタンス
     *
     * @var \App\Models\Observer
     */
    protected $observer;

    /**
     * Create a new job instance.
     */
    public function __construct(Observer $observer)
    {
        $this->observer = $observer;
    }

    /**
     * Execute the job.
     * Observerに関連する個人用Organizationを作成します。
     */
    public function handle(): void
    {
        // Observerに関連付けられたOrganizationが存在するかチェック
        if ($this->observer->organizations()->exists()) {
            return;
        }

        // Observerの詳細情報を取得
        $observerDetail = $this->observer->latestDetail;
        $observerName = $observerDetail?->name ?? 'Unknown';

        // トランザクションを使用して処理の整合性を確保
        DB::transaction(function () use ($observerName) {
            // 新しいOrganizationを作成
            $organization = Organization::create();

            // OrganizationDetailを作成して関連付け
            OrganizationDetail::create([
                'organization_id' => $organization->id,
                'name' => $observerName.'\'s Private Organization',
                'description' => 'Private organization for '.$observerName,
            ]);

            // ObserverとOrganizationを関連付け
            $this->observer->organizations()->attach($organization->id);
        });
    }
}
