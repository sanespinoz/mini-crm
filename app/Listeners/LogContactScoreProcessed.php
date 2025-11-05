<?php

namespace App\Listeners;

use App\Events\ContactScoreProcessed;
use Illuminate\Support\Facades\Log;

class LogContactScoreProcessed
{
    public function handle(ContactScoreProcessed $event): void
    {
        $c = $event->contact;
        Log::channel('single')->info("Contact {$c->id} ({$c->email}) score {$c->score} processed at {$c->processed_at}");

        // Register in contact.log
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/contact.log'),
        ])->info("Contact {$c->id} ({$c->email}) => Score: {$c->score} at {$c->processed_at}");
    }
}
