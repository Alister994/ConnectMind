<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(
        protected ContactService $contactService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'tag_id', 'per_page']);
        $contacts = $this->contactService->list($filters);
        return response()->json(ContactResource::collection($contacts));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);
        $contact = $this->contactService->create($validated);
        return response()->json(new ContactResource($contact), 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        $contact = $this->contactService->get($contact->id);
        if (!$contact) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(new ContactResource($contact));
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);
        $contact = $this->contactService->update($contact, $validated);
        return response()->json(new ContactResource($contact));
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $this->contactService->delete($contact);
        return response()->json(null, 204);
    }
}
