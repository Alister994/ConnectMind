@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600">Your AI Personal Relationship Manager</p>
</div>

<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-sm font-medium text-gray-500">Total contacts</h2>
        <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ $contactsCount }}</p>
    </div>
</div>

<div class="mt-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming reminders</h2>
    @if($upcomingReminders->isEmpty())
        <p class="text-gray-500">No upcoming reminders.</p>
    @else
        <ul class="bg-white rounded-lg shadow divide-y">
            @foreach($upcomingReminders as $reminder)
                <li class="px-6 py-4 flex justify-between items-center">
                    <div>
                        <span class="font-medium">{{ $reminder->contact->name }}</span>
                        <span class="text-sm text-gray-500 ml-2">{{ $reminder->remind_at->diffForHumans() }}</span>
                    </div>
                    <span class="text-sm text-gray-500">{{ $reminder->remind_at->format('M j, Y') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
