<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrganizationUpdateRequest;
use App\Models\OrganizationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * 現在のユーザーに関連するOrganizationを表示する
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $organization = $observer->organizations()->first();

        if (! $organization) {
            abort(404, 'Organization not found');
        }

        return view('organization.show', [
            'organization' => $organization,
            'detail' => $organization->latestDetail,
        ]);
    }

    /**
     * 現在のユーザーに関連するOrganizationの編集フォームを表示する
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $organization = $observer->organizations()->first();

        if (! $organization) {
            abort(404, 'Organization not found');
        }

        return view('organization.edit', [
            'organization' => $organization,
            'detail' => $organization->latestDetail,
        ]);
    }

    /**
     * 現在のユーザーに関連するOrganizationを更新する
     * 更新時には新しいOrganizationDetailレコードを作成する
     */
    public function update(OrganizationUpdateRequest $request)
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $organization = $observer->organizations()->first();

        if (! $organization) {
            abort(404, 'Organization not found');
        }

        // バリデーション済みデータを取得
        $validated = $request->validated();

        // トランザクションを使用してデータの整合性を確保
        DB::transaction(function () use ($organization, $validated) {
            // 新しいOrganizationDetailを作成
            OrganizationDetail::create([
                'organization_id' => $organization->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
        });

        return redirect()->route('organization.show')
            ->with('status', 'organization-updated');
    }
}
