<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    /**
     * Groq API endpoint
     */
    private const GROQ_API_URL = 'https://api.groq.com/openai/v1/chat/completions';

    /**
     * Generate deal briefing using Groq API
     * 
     * @param array $dealData Deal data from DealBriefingService
     * @return string Briefing text
     */
    public function generateDealBriefing(array $dealData): string
    {
        $apiKey = config('services.groq.api_key');
        
        if (!$apiKey) {
            throw new \Exception('GROQ_API_KEY not configured. Please add it to your .env file.');
        }

        $prompt = $this->buildPrompt($dealData);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post(self::GROQ_API_URL, [
                'model' => 'llama-3.3-70b-versatile', // Groq's fast model (updated from deprecated llama-3.1-70b-versatile)
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if (!$response->successful()) {
                $error = $response->json();
                throw new \Exception('Groq API error: ' . ($error['error']['message'] ?? $response->body()));
            }

            $responseData = $response->json();
            $briefing = $responseData['choices'][0]['message']['content'] ?? 'Unable to generate briefing.';

            // Log usage for monitoring
            Log::info('Groq API call', [
                'model' => 'llama-3.3-70b-versatile',
                'tokens_used' => $responseData['usage']['total_tokens'] ?? 0,
                'deal_id' => $dealData['deal_info']['id'] ?? null,
            ]);

            return trim($briefing);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Groq API request error', [
                'error' => $e->getMessage(),
                'deal_id' => $dealData['deal_info']['id'] ?? null,
            ]);

            throw new \Exception('Failed to generate briefing: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Groq API error', [
                'error' => $e->getMessage(),
                'deal_id' => $dealData['deal_info']['id'] ?? null,
            ]);

            throw new \Exception('Failed to generate briefing: ' . $e->getMessage());
        }
    }

    /**
     * Build the user prompt with deal context
     */
    protected function buildPrompt(array $dealData): string
    {
        $dealInfo = $dealData['deal_info'];
        $activities = $dealData['recent_activities'];
        $engagements = $dealData['engagements'];
        $warnings = $dealData['warnings'];
        $timeMetrics = $dealData['time_metrics'];

        $prompt = "Generate a concise manager prep briefing for discussing this deal in a 1-on-1 meeting.\n\n";
        $prompt .= "DEAL INFORMATION:\n";
        $prompt .= "- Name: {$dealInfo['name']}\n";
        $prompt .= "- Amount: $" . number_format($dealInfo['amount'] ?? 0, 2) . "\n";
        $prompt .= "- Pipeline: {$dealInfo['pipeline']}\n";
        $prompt .= "- Stage: {$dealInfo['stage']}";
        if ($dealInfo['stage_probability']) {
            $prompt .= " ({$dealInfo['stage_probability']}% probability)";
        }
        $prompt .= "\n";
        $prompt .= "- Owner: {$dealInfo['owner']}\n";
        if ($dealInfo['next_step']) {
            $prompt .= "- Next Step: {$dealInfo['next_step']}\n";
        }
        if ($dealInfo['close_date']) {
            $prompt .= "- Close Date: {$dealInfo['close_date']}\n";
        }
        $prompt .= "\n";

        // Time metrics
        $prompt .= "TIMELINE:\n";
        $prompt .= "- Created: {$timeMetrics['days_since_creation']} days ago\n";
        $prompt .= "- Last Updated: {$timeMetrics['days_since_last_update']} days ago\n";
        $prompt .= "- In Current Stage: {$timeMetrics['days_in_current_stage']} days\n";
        if ($timeMetrics['days_until_close_date'] !== null) {
            if ($timeMetrics['days_until_close_date'] < 0) {
                $prompt .= "- Close Date: " . abs($timeMetrics['days_until_close_date']) . " days overdue\n";
            } else {
                $prompt .= "- Days Until Close: {$timeMetrics['days_until_close_date']}\n";
            }
        }
        $prompt .= "\n";

        // Warnings
        if (!empty($warnings)) {
            $prompt .= "⚠️ WARNINGS:\n";
            foreach ($warnings as $warning) {
                $prompt .= "- {$warning['message']}\n";
            }
            $prompt .= "\n";
        }

        // Recent activities
        if (!empty($activities)) {
            $prompt .= "RECENT ACTIVITIES (Last " . count($activities) . " tasks):\n";
            foreach (array_slice($activities, 0, 5) as $activity) {
                $prompt .= "- {$activity['subject']}";
                if ($activity['status']) {
                    $prompt .= " [{$activity['status']}]";
                }
                if ($activity['completion_date']) {
                    $prompt .= " - Completed: {$activity['completion_date']}";
                }
                $prompt .= "\n";
            }
            $prompt .= "\n";
        }

        // Engagement summary
        $totalEngagements = count($engagements['calls']) + count($engagements['emails']) + count($engagements['meetings']);
        if ($totalEngagements > 0) {
            $prompt .= "ENGAGEMENT SUMMARY:\n";
            $prompt .= "- Calls: " . count($engagements['calls']) . "\n";
            $prompt .= "- Emails: " . count($engagements['emails']) . "\n";
            $prompt .= "- Meetings: " . count($engagements['meetings']) . "\n";
            
            // Most recent engagement
            $allEngagements = array_merge(
                array_map(fn($e) => array_merge($e, ['type' => 'call']), $engagements['calls']),
                array_map(fn($e) => array_merge($e, ['type' => 'email']), $engagements['emails']),
                array_map(fn($e) => array_merge($e, ['type' => 'meeting']), $engagements['meetings'])
            );
            
            usort($allEngagements, function ($a, $b) {
                return strtotime($b['timestamp'] ?? '1970-01-01') <=> strtotime($a['timestamp'] ?? '1970-01-01');
            });
            
            if (!empty($allEngagements)) {
                $latest = $allEngagements[0];
                $latestDate = $latest['created_at'] ?? $latest['timestamp'] ?? 'Unknown';
                $prompt .= "- Last Engagement: {$latest['type']} - {$latest['title']} ({$latestDate})\n";
            }
            $prompt .= "\n";
        }

        $prompt .= "Generate a brief, actionable summary (2-3 paragraphs) that:\n";
        $prompt .= "1. Highlights key concerns or opportunities\n";
        $prompt .= "2. Suggests discussion points for the 1-on-1\n";
        $prompt .= "3. Provides coaching guidance if there are warnings\n";
        $prompt .= "4. Focuses on what the manager should address with the sales rep\n";

        return $prompt;
    }

    /**
     * Get the system prompt that defines the AI's role
     */
    protected function getSystemPrompt(): string
    {
        return "You are an AI sales coaching assistant helping managers prepare for 1-on-1 meetings with their sales team. " .
               "Your role is to analyze deal data and provide concise, actionable briefings that help managers have productive conversations. " .
               "Focus on identifying risks, opportunities, and specific coaching points. " .
               "Be direct, professional, and practical. " .
               "Keep responses concise (2-3 paragraphs) and action-oriented.";
    }
}
