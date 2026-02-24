<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReminderResource;
use App\Models\Contact;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Reminder::with('contact')->orderBy('remind_at');
        if ($request->boolean('upcoming')) {
            $query->where('remind_at', '>=', now())->where('is_sent', false);
        }
        $reminders = $query->paginate($request->input('per_page', 15));
        return response()->json(ReminderResource::collection($reminders));
    }

    public function store(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|string|in:follow_up,stay_in_touch',
            'message' => 'nullable|string',
            'remind_at' => 'required|date|after:now',
        ]);
        $validated['contact_id'] = $contact->id;
        $validated['type'] = $validated['type'] ?? 'follow_up';
        $reminder = Reminder::create($validated);
        return response()->json(new ReminderResource($reminder), 201);
    }
}
