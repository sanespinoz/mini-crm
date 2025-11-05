<?php

namespace App\Observers;

use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class ContactObserver
{
    public function saving(Contact $contact)
    {
        if ($contact->phone) {
            $contact->phone = preg_replace('/\D+/', '', $contact->phone);
        }
    }

    public function created(Contact $contact)
    {
        Log::info('New contact created', [
            'id' => $contact->id,
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone,
        ]);
    }
}
