<?php

namespace App\Listeners;

use App\Events\ObserverCreated;
use App\Models\Organization;
use App\Models\OrganizationDetail;
use Illuminate\Support\Facades\DB;

class EnsureObserverHasOrganization
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ObserverCreated $event): void
    {
        $observer = $event->observer;
        
        // Observerに関連付けられたOrganizationが存在するかチェック
        if ($observer->organizations()->exists()) {
            return;
        }
        
        // Observerの詳細情報を取得
        $observerDetail = $observer->latestDetail;
        $observerName = $observerDetail?->name ?? 'Unknown';
        
        // トランザクションを使用して処理の整合性を確保
        DB::transaction(function () use ($observer, $observerName) {
            // 新しいOrganizationを作成
            $organization = Organization::create();
            
            // OrganizationDetailを作成して関連付け
            OrganizationDetail::create([
                'organization_id' => $organization->id,
                'name' => $observerName . '\'s Private Organization',
                'description' => 'Private organization for ' . $observerName,
            ]);
            
            // ObserverとOrganizationを関連付け
            $observer->organizations()->attach($organization->id);
        });
    }
}