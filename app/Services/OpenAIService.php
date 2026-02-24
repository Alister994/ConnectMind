<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';
    protected int $maxTokens = 500;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
    }

    public function summarizeMeetingNotes(string $notes): ?string
    {
        $prompt = "Summarize the following meeting notes in 2-4 concise bullet points. Keep professional tone.\n\n" . $notes;
        return $this->chat($prompt);
    }

    public function generateStayInTouchMessage(string $contactName, ?string $context = null): ?string
    {
        $prompt = "Generate a short, friendly 'stay in touch' message (1-2 sentences) to send to someone named {$contactName}. ";
        if ($context) {
            $prompt .= "Context: {$context}. ";
        }
        $prompt .= "Do not include subject or salutation, just the message body.";
        return $this->chat($prompt);
    }

    public function suggestFollowUpReminders(string $contactName, string $lastInteractionSummary): ?string
    {
        $prompt = "For a contact named {$contactName}, based on this last interaction summary: \"{$lastInteractionSummary}\". "
            . "Suggest 1-3 specific follow-up reminder ideas (e.g. 'Send birthday wish in 2 weeks'). One per line, brief.";
        return $this->chat($prompt);
    }

    public function calculateRelationshipHealthScore(string $contactName, array $recentInteractionsSummary): ?float
    {
        $summary = implode("\n", $recentInteractionsSummary);
        $prompt = "Based on these recent interaction summaries for {$contactName}:\n{$summary}\n\n"
            . "Respond with ONLY a number from 0 to 100 representing relationship health (100 = very strong). No explanation.";
        $response = $this->chat($prompt);
        if ($response === null) {
            return null;
        }
        $score = (float) trim(preg_replace('/[^0-9.]/', '', $response));
        return max(0, min(100, $score));
    }

    protected function chat(string $prompt): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('OpenAI API key not set');
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post("{$this->baseUrl}/chat/completions", [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $this->maxTokens,
            'temperature' => 0.5,
        ]);

        if (!$response->successful()) {
            Log::error('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;
        return $content ? trim($content) : null;
    }

    public function estimateTokens(string $text): int
    {
        return (int) ceil(str_word_count($text) * 1.35);
    }
}
