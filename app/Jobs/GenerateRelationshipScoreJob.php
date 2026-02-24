<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Tenant;
use App\Services\OpenAIService;
use App\Services\TenantManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateRelationshipScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tenantId,
        public int $contactId
    ) {}

    public function handle(OpenAIService $openAI, TenantManager $tenantManager): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) {
            return;
        }
        $tenantManager->setTenant($tenant);

        $contact = Contact::with('interactions')->find($this->contactId);
        if (!$contact) {
            return;
        }

        $summaries = $contact->interactions()
            ->orderByDesc('occurred_at')
            ->limit(10)
            ->pluck('ai_summary')
            ->filter()
            ->values()
            ->all();

        if (empty($summaries)) {
            return;
        }

        $score = $openAI->calculateRelationshipHealthScore($contact->name, $summaries);
        if ($score !== null) {
            $contact->update(['relationship_strength_score' => $score]);
        }
    }
}
