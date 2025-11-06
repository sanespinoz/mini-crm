<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use App\Jobs\ProcessContactScore;
use App\Events\ContactScoreProcessed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_contacts()
    {
        Contact::factory()->count(3)->create();

        $response = $this->getJson('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'data' => [['id', 'name', 'email', 'phone', 'score', 'processed_at', 'created_at', 'updated_at']]
                ]
            ]);
    }

    /** @test */
    public function it_shows_single_contact()
    {
        $contact = Contact::factory()->create();

        $response = $this->getJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_contact()
    {
        $response = $this->getJson("/api/contacts/9999");

        $response->assertStatus(404)
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Contact not found',
            ]);
    }

    /** @test */
    public function it_creates_contact_through_form_request()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+5511987654321'
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '5511987654321'
            ]);

        $this->assertDatabaseHas('contacts', ['email' => 'test@example.com']);
    }

    /** @test */
    public function it_fails_to_create_contact_with_invalid_data()
    {
        $payload = [
            'name' => '',   // Name required
            'email' => 'not-an-email',
            'phone' => 'abc'
        ];

        $response = $this->postJson('/api/contacts', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    /** @test */
    public function it_updates_contact_through_form_request()
    {
        $contact = Contact::factory()->create();

        $payload = [
            'name' => 'Updated Name',
            'phone' => '+5511999999999'
        ];

        $response = $this->putJson("/api/contacts/{$contact->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Name',
                'phone' => '5511999999999'
            ]);

        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'name' => 'Updated Name']);
    }

    /** @test */
    public function it_soft_deletes_contact()
    {
        $contact = Contact::factory()->create();

        $response = $this->deleteJson("/api/contacts/{$contact->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
    }

    /** @test */
    public function it_restores_soft_deleted_contact()
    {
        $contact = Contact::factory()->create();
        $contact->delete();

        $contact->restore();

        $this->assertDatabaseHas('contacts', ['id' => $contact->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_dispatches_process_contact_score_job_and_event()
    {

    Queue::fake();
    Event::fake();

   
    $contact = Contact::factory()->create([
        'score' => 0,
        'processed_at' => null,
    ]);

    
    $response = $this->postJson("/api/contacts/{$contact->id}/process-score");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Score processing has begun.',
        ]);

    
    Queue::assertPushedOn('contacts', ProcessContactScore::class, function ($job) use ($contact) {
       
        $job->handle();
        return $job->contact->id === $contact->id;
    });

 
    Event::assertDispatched(ContactScoreProcessed::class, function ($event) use ($contact) {
        return $event->contact->id === $contact->id;
    });

    
    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'score' => $contact->fresh()->score, 
    ]);
}

    /** @test */
    public function it_returns_404_when_processing_nonexistent_contact()
    {
        $response = $this->postJson("/api/contacts/9999/process-score");

        $response->assertStatus(404)
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Contact not found',
            ]);
    }
}
