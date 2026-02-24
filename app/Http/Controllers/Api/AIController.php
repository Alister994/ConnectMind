<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\OpenAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AIController extends Controller
{
    public function __construct(
        protected OpenAIService $openAI
    ) {}

    public function stayInTouch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'context' => 'nullable|string|max:1000',
        ]);
        $contact = Contact::find($validated['contact_id']);
        $message = $this->openAI->generateStayInTouchMessage($contact->name, $validated['context'] ?? null);
        return response()->json(['message' => $message ?? 'Unable to generate. Check API key.']);
    }

    public function suggestReminders(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
        ]);
        $contact = Contact::with('interactions')->find($validated['contact_id']);
        $lastSummary = $contact->interactions()->orderByDesc('occurred_at')->value('ai_summary') ?? 'No recent interaction.';
        $suggestions = $this->openAI->suggestFollowUpReminders($contact->name, $lastSummary);
        $lines = $suggestions ? array_filter(array_map('trim', explode("\n", $suggestions))) : [];
        return response()->json(['suggestions' => $lines]);
    }
}
