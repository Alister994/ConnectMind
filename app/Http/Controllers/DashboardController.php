<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Reminder;

class DashboardController extends Controller
{
    public function index()
    {
        $contactsCount = Contact::count();
        $upcomingReminders = Reminder::where('remind_at', '>=', now())
            ->where('is_sent', false)
            ->with('contact')
            ->orderBy('remind_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'contactsCount' => $contactsCount,
            'upcomingReminders' => $upcomingReminders,
        ]);
    }
}
