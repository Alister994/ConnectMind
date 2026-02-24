<?php

namespace App\Jobs;

use App\Models\Interaction;
use App\Models\Tenant;
use App\Services\OpenAIService;
use App\Services\TenantManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SummarizeInteractionNotesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $tenantId,
        public int $interactionId
    ) {}

    public function handle(OpenAIService $openAI, TenantManager $tenantManager): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) {
            return;
        }
        $tenantManager->setTenant($tenant);

        $interaction = Interaction::find($this->interactionId);
        if (!$interaction || empty($interaction->notes)) {
            return;
        }

        $summary = $openAI->summarizeMeetingNotes($interaction->notes);
        if ($summary) {
            $interaction->update(['ai_summary' => $summary]);
        }
    }
}
