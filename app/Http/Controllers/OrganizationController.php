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
     * 新しいOrganizationを作成するためのフォームを表示する
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        return view('organization.create');
    }

    /**
     * 新しいOrganizationを保存する
     */
    public function store(OrganizationUpdateRequest $request)
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $validated = $request->validated();

        $organization = DB::transaction(function () use ($observer, $validated) {
            // 新しいOrganizationを作成
            $organization = \App\Models\Organization::create();
            
            // Observerと関連付け
            $observer->organizations()->attach($organization);
            
            // OrganizationDetailを作成
            $detail = OrganizationDetail::create([
                'organization_id' => $organization->id,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
            
            return $organization;
        });

        return redirect()->route('organization.show', ['organizationId' => $organization->id])
            ->with('status', 'organization-created');
    }
    /**
     * 現在のユーザーに関連するすべてのOrganizationを表示する
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $organizations = $observer->organizations()->with('latestDetail')->get();

        return view('organization.index', [
            'organizations' => $organizations,
        ]);
    }

    /**
     * 特定のOrganizationの詳細を表示する
     */
    public function show(Request $request, $organizationId): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $organization = $observer->organizations()->with('subjects.latestDetail')->findOrFail($organizationId);

        return view('organization.show', [
            'organization' => $organization,
            'detail' => $organization->latestDetail,
        ]);
    }

    /**
     * 特定のOrganizationの編集フォームを表示する
     */
    public function edit(Request $request, $organizationId): View
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $organization = $observer->organizations()->findOrFail($organizationId);

        return view('organization.edit', [
            'organization' => $organization,
            'detail' => $organization->latestDetail,
        ]);
    }

    /**
     * 特定のOrganizationを更新する
     * 更新時には新しいOrganizationDetailレコードを作成する
     */
    public function update(OrganizationUpdateRequest $request, $organizationId)
    {
        $user = $request->user();
        $observer = $user->getDefaultObserver();

        if (! $observer) {
            abort(404, 'Observer not found');
        }

        $organization = $observer->organizations()->findOrFail($organizationId);

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

        return redirect()->route('organization.show', ['organizationId' => $organization->id])
            ->with('status', 'organization-updated');
    }
}
