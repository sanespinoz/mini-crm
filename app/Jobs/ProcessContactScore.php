<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Events\ContactScoreProcessed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessContactScore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function handle(): void
    {
        
        sleep(2);

        $score = rand(0, 100);

        $this->contact->update([
            'score' => $score,
            'processed_at' => now(),
        ]);

        // Dispatch event
        event(new ContactScoreProcessed($this->contact));
    }
}
