<?php

namespace App\Events;

use App\Models\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class ContactScoreProcessed implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }


    public function broadcastOn()
    {
        // Channel public
        return new Channel('contacts.' . $this->contact->id);
    }

    public function broadcastAs()
    {
        return 'ContactScoreProcessed';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->contact->id,
            'email' => $this->contact->email,
            'score' => $this->contact->score,
            'processed_at' => $this->contact->processed_at,
        ];
    }
}