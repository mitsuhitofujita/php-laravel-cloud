<?php

namespace App\Http\Controllers;

use App\Models\Observer;
use App\Models\ObserverDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ObserverController extends Controller
{
    /**
     * 現在のユーザーに関連するObserverを表示する
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();
        
        if (!$observer) {
            abort(404, 'Observer not found');
        }
        
        return view('observer.show', [
            'observer' => $observer,
            'detail' => $observer->latestDetail,
        ]);
    }
    
    /**
     * 現在のユーザーに関連するObserverの編集フォームを表示する
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();
        
        if (!$observer) {
            abort(404, 'Observer not found');
        }
        
        return view('observer.edit', [
            'observer' => $observer,
            'detail' => $observer->latestDetail,
        ]);
    }
    
    /**
     * 現在のユーザーに関連するObserverを更新する
     * 更新時には新しいObserverDetailレコードを作成する
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();
        
        if (!$observer) {
            abort(404, 'Observer not found');
        }
        
        // リクエストを検証
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
        
        // トランザクションを使用してデータの整合性を確保
        DB::transaction(function () use ($observer, $validated) {
            // 新しいObserverDetailを作成
            ObserverDetail::create([
                'observer_id' => $observer->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
        });
        
        return redirect()->route('observer.show')
            ->with('status', 'observer-updated');
    }
}