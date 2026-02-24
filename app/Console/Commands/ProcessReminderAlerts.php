<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\Tenant;
use App\Services\TenantManager;
use Illuminate\Console\Command;

class ProcessReminderAlerts extends Command
{
    protected $signature = 'reminders:process {--days=30 : Days since last contact to trigger alert}';

    protected $description = 'Find contacts not contacted in N days and create/trigger reminder alerts';

    public function handle(TenantManager $tenantManager): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $tenants = Tenant::where('is_active', true)->get();

        foreach ($tenants as $tenant) {
            $tenantManager->setTenant($tenant);
            $contacts = Contact::where(function ($q) use ($cutoff) {
                $q->whereNull('last_interaction_at')
                    ->orWhere('last_interaction_at', '<=', $cutoff);
            })->get();

            foreach ($contacts as $contact) {
                $this->info("Tenant {$tenant->name}: Contact {$contact->name} (id {$contact->id}) - no contact in {$days}+ days.");
                // Could dispatch a job to send notification or create reminder record
                // Reminder::create([...]) or event(new ContactDueReminder($contact));
            }

            $tenantManager->clearTenant();
        }

        return self::SUCCESS;
    }
}
