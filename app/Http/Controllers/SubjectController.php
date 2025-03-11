<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubjectUpdateRequest;
use App\Models\Observer;
use App\Models\Organization;
use App\Models\Subject;
use App\Models\SubjectDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubjectController extends Controller
{
    /**
     * Display the subject details.
     */
    public function show(Request $request, Organization $organization, Subject $subject): View
    {
        $observer = $request->user()->getDefaultObserver();
        
        if (!$observer || !$observer->organizations->contains($organization->id)) {
            abort(404);
        }
        
        if (!$organization->subjects->contains($subject->id)) {
            abort(404);
        }
        
        return view('subject.show', [
            'organization' => $organization,
            'subject' => $subject,
        ]);
    }
    
    /**
     * Show the form for editing the subject.
     */
    public function edit(Request $request, Organization $organization, Subject $subject): View
    {
        $observer = $request->user()->getDefaultObserver();
        
        if (!$observer || !$observer->organizations->contains($organization->id)) {
            abort(404);
        }
        
        if (!$organization->subjects->contains($subject->id)) {
            abort(404);
        }
        
        return view('subject.edit', [
            'organization' => $organization,
            'subject' => $subject,
        ]);
    }
    
    /**
     * Update the subject details.
     */
    public function update(SubjectUpdateRequest $request, Organization $organization, Subject $subject): RedirectResponse
    {
        $observer = $request->user()->getDefaultObserver();
        
        if (!$observer || !$observer->organizations->contains($organization->id)) {
            abort(404);
        }
        
        if (!$organization->subjects->contains($subject->id)) {
            abort(404);
        }
        
        $validated = $request->validated();
        
        DB::transaction(function () use ($subject, $validated) {
            $subject->details()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
        });
        
        return redirect()->route('organization.subject.show', [
            'organization' => $organization->id,
            'subject' => $subject->id,
        ])->with('status', 'subject-updated');
    }
    
    /**
     * Show the form for creating a new subject.
     */
    public function create(Request $request, Organization $organization): View
    {
        $observer = $request->user()->getDefaultObserver();
        
        if (!$observer || !$observer->organizations->contains($organization->id)) {
            abort(404);
        }
        
        return view('subject.create', [
            'organization' => $organization,
        ]);
    }
    
    /**
     * Store a newly created subject.
     */
    public function store(SubjectUpdateRequest $request, Organization $organization): RedirectResponse
    {
        $observer = $request->user()->getDefaultObserver();
        
        if (!$observer || !$observer->organizations->contains($organization->id)) {
            abort(404);
        }
        
        $validated = $request->validated();
        
        $subject = DB::transaction(function () use ($organization, $validated) {
            $subject = Subject::create();
            
            $subject->details()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
            
            $organization->subjects()->attach($subject);
            
            return $subject;
        });
        
        return redirect()->route('organization.subject.show', [
            'organization' => $organization->id,
            'subject' => $subject->id,
        ])->with('status', 'subject-created');
    }
}