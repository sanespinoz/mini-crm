<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Http\Resources\ContactCollection;
use Illuminate\Http\JsonResponse;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Jobs\ProcessContactScore;

class ContactController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $contacts = Contact::orderByDesc('id')->paginate(10);
        return $this->success(new ContactCollection($contacts));
    }

    public function show($id): JsonResponse
    {
        try {
            $contact = Contact::findOrFail($id);
            return $this->success(new ContactResource($contact));
        } catch (ModelNotFoundException $e) {
            return $this->error('Contact not found', 404);
        }
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create($request->validated());
        return $this->success(new ContactResource($contact), 'Contact created successfully', 201);
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $contact->update($request->validated());
        return $this->success(new ContactResource($contact), 'Contact updated successfully');
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();
        return $this->success(null, 'Contact deleted successfully');
    }

    public function processScore($id)
    {
        $contact = Contact::findOrFail($id);

        ProcessContactScore::dispatch($contact)->onQueue('contacts');

        return $this->success(null, 'Score processing has begun.');
    }
}
