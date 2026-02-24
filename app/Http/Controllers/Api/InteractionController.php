<?php

namespace App\Http\Controllers\Api;

use App\Events\InteractionCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\InteractionResource;
use App\Models\Contact;
use App\Models\Interaction;
use App\Services\InteractionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function __construct(
        protected InteractionService $interactionService
    ) {}

    public function index(Contact $contact): JsonResponse
    {
        $interactions = $this->interactionService->getByContact($contact->id);
        return response()->json(InteractionResource::collection($interactions));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'type' => 'required|in:meeting,call,email',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'occurred_at' => 'required|date',
        ]);
        $interaction = $this->interactionService->create($validated);
        event(new InteractionCreated($interaction));
        return response()->json(new InteractionResource($interaction), 201);
    }

    public function show(Interaction $interaction): JsonResponse
    {
        $interaction = $this->interactionService->find($interaction->id);
        if (!$interaction) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(new InteractionResource($interaction));
    }

    public function update(Request $request, Interaction $interaction): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'sometimes|in:meeting,call,email',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'occurred_at' => 'sometimes|date',
        ]);
        $interaction = $this->interactionService->update($interaction, $validated);
        return response()->json(new InteractionResource($interaction));
    }

    public function destroy(Interaction $interaction): JsonResponse
    {
        $this->interactionService->delete($interaction);
        return response()->json(null, 204);
    }
}
