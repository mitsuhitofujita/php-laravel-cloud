<?php

namespace App\Jobs;

use App\Models\Observer;
use App\Models\ObserverDetail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateObserverForUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Userインスタンス
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     * ユーザーに関連するObserverを作成します。
     */
    public function handle(): void
    {
        // ユーザーに関連付けられたObserverが存在するかチェック
        if ($this->user->observers()->exists()) {
            return;
        }
        
        // トランザクションを使用して処理の整合性を確保
        DB::transaction(function () {
            // 新しいObserverを作成
            $observer = Observer::create();
            
            // ObserverDetailを作成して関連付け
            ObserverDetail::create([
                'observer_id' => $observer->id,
                'name' => $this->user->name,
                'description' => 'Default observer for ' . $this->user->email,
            ]);
            
            // UserとObserverを関連付け
            $this->user->observers()->attach($observer->id);
        });
    }
}