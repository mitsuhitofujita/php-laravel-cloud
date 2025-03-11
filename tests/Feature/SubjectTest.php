<?php

namespace Tests\Feature;

use App\Models\Observer;
use App\Models\Organization;
use App\Models\Subject;
use App\Models\SubjectDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_subject_show_page_is_displayed(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            $organization = Organization::factory()->create();
            $subject = Subject::factory()->create();
            $subjectDetail = SubjectDetail::factory()->create([
                'subject_id' => $subject->id,
                'name' => 'Test Subject',
                'description' => 'Test Description',
            ]);

            $observer->organizations()->attach($organization);
            $organization->subjects()->attach($subject);
            $user->observers()->attach($observer);

            $response = $this
                ->actingAs($user)
                ->get(route('organization.subject.show', [
                    'organization' => $organization->id,
                    'subject' => $subject->id,
                ]));

            $response->assertOk();
        });
    }

    public function test_subject_information_can_be_updated(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            $organization = Organization::factory()->create();
            $subject = Subject::factory()->create();
            $subjectDetail = SubjectDetail::factory()->create([
                'subject_id' => $subject->id,
                'name' => 'Test Subject',
                'description' => 'Test Description',
            ]);

            $observer->organizations()->attach($organization);
            $organization->subjects()->attach($subject);
            $user->observers()->attach($observer);

            $response = $this
                ->actingAs($user)
                ->put(route('organization.subject.update', [
                    'organization' => $organization->id,
                    'subject' => $subject->id,
                ]), [
                    'name' => 'Updated Subject Name',
                    'description' => 'Updated Subject Description',
                ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('organization.subject.show', [
                    'organization' => $organization->id,
                    'subject' => $subject->id,
                ]));

            $this->assertDatabaseHas('subject_details', [
                'subject_id' => $subject->id,
                'name' => 'Updated Subject Name',
                'description' => 'Updated Subject Description',
            ]);
        });
    }

    public function test_subject_can_be_created(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            $organization = Organization::factory()->create();

            $observer->organizations()->attach($organization);
            $user->observers()->attach($observer);

            $response = $this
                ->actingAs($user)
                ->post(route('organization.subject.store', [
                    'organization' => $organization->id,
                ]), [
                    'name' => 'New Subject',
                    'description' => 'New Subject Description',
                ]);

            $subject = Subject::first();

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('organization.subject.show', [
                    'organization' => $organization->id,
                    'subject' => $subject->id,
                ]));

            $this->assertDatabaseHas('subject_details', [
                'subject_id' => $subject->id,
                'name' => 'New Subject',
                'description' => 'New Subject Description',
            ]);

            $this->assertDatabaseHas('organization_subject', [
                'organization_id' => $organization->id,
                'subject_id' => $subject->id,
            ]);
        });
    }

    public function test_unauthorized_user_cannot_view_subject(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $anotherUser = User::factory()->create();
            $observer = Observer::factory()->create();
            $organization = Organization::factory()->create();
            $subject = Subject::factory()->create();

            $observer->organizations()->attach($organization);
            $organization->subjects()->attach($subject);
            $user->observers()->attach($observer);

            $response = $this
                ->actingAs($anotherUser)
                ->get(route('organization.subject.show', [
                    'organization' => $organization->id,
                    'subject' => $subject->id,
                ]));

            $response->assertNotFound();
        });
    }

    public function test_validation_errors_when_updating_subject(): void
    {
        Event::fakeFor(function () {
            $user = User::factory()->create();
            $observer = Observer::factory()->create();
            $organization = Organization::factory()->create();
            $subject = Subject::factory()->create();

            $observer->organizations()->attach($organization);
            $organization->subjects()->attach($subject);
            $user->observers()->attach($observer);

            $response = $this
                ->actingAs($user)
                ->put(route('organization.subject.update', [
                    'organization' => $organization->id,
                    'subject' => $subject->id,
                ]), [
                    'name' => '',
                    'description' => 'Test Description',
                ]);

            $response
                ->assertSessionHasErrors('name');
        });
    }
}
