<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set up your workspace – {{ config('app.name', 'ConnectMind') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-indigo-600 mb-6">ConnectMind</h1>
        <div class="bg-white shadow rounded-lg p-8">
            <h2 class="text-lg font-semibold mb-2">Set up your workspace</h2>
            <p class="text-gray-600 text-sm mb-6">Your account doesn’t have a workspace yet. Click below to create one and continue.</p>
            <form method="POST" action="{{ route('workspace.setup.store') }}">
                @csrf
                <button type="submit" class="w-full bg-indigo-600 text-white rounded-md py-2 px-4 hover:bg-indigo-700">Create workspace</button>
            </form>
        </div>
    </div>
</body>
</html>
