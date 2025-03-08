<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\Observer;
use App\Models\ObserverDetail;
use Illuminate\Support\Facades\DB;

class EnsureUserHasObserver
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
    public function handle(UserCreated $event): void
    {
        $user = $event->user;
        
        // ユーザーに関連付けられたObserverが存在するかチェック
        if ($user->observers()->exists()) {
            return;
        }
        
        // トランザクションを使用して処理の整合性を確保
        DB::transaction(function () use ($user) {
            // 新しいObserverを作成
            $observer = Observer::create();
            
            // ObserverDetailを作成して関連付け
            ObserverDetail::create([
                'observer_id' => $observer->id,
                'name' => $user->name,
                'description' => 'Default observer for ' . $user->email,
            ]);
            
            // UserとObserverを関連付け
            $user->observers()->attach($observer->id);
        });
    }
}