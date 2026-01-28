<?php

namespace App\Services;

use App\Helpers\HubspotClientHelper;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\User;
use Carbon\Carbon;

class DealBriefingService
{
    /**
     * Gather all relevant data for a deal briefing
     * 
     * @param Deal $deal
     * @param User $user
     * @return array
     */
    public function gatherDealData(Deal $deal, User $user): array
    {
        // Get deal basic info
        $dealInfo = $this->getDealInfo($deal);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($deal);
        
        // Get engagement history
        $engagements = $this->getEngagementHistory($deal, $user);
        
        // Calculate warnings
        $warnings = $this->calculateWarnings($deal);
        
        // Calculate time metrics
        $timeMetrics = $this->calculateTimeMetrics($deal);
        
        return [
            'deal_info' => $dealInfo,
            'recent_activities' => $recentActivities,
            'engagements' => $engagements,
            'warnings' => $warnings,
            'time_metrics' => $timeMetrics,
        ];
    }

    /**
     * Get basic deal information
     */
    protected function getDealInfo(Deal $deal): array
    {
        // Handle deals that might not have relationships loaded
        $pipeline = $deal->relationLoaded('pipeline') ? $deal->pipeline : ($deal->pipeline_id ? $deal->pipeline()->first() : null);
        $stage = $deal->relationLoaded('stage') ? $deal->stage : ($deal->stage_id ? $deal->stage()->first() : null);
        $member = $deal->relationLoaded('member') ? $deal->member : ($deal->member_id ? $deal->member()->first() : null);
        
        return [
            'id' => $deal->id ?? null,
            'hubspot_id' => $deal->hubspot_id,
            'name' => $deal->name,
            'amount' => $deal->amount,
            'pipeline' => $pipeline->label ?? 'Unknown',
            'stage' => $stage->label ?? 'Unknown',
            'stage_probability' => $stage->probability ?? null,
            'owner' => $member->full_name ?? 'Unassigned',
            'owner_email' => $member->email ?? null,
            'next_step' => $deal->hs_next_step,
            'forecast_category' => $deal->hs_manual_forecast_category,
            'is_closed' => $deal->hs_is_closed ?? false,
            'is_closed_won' => $deal->hs_is_closed_won ?? false,
            'close_date' => $deal->closedate?->format('Y-m-d'),
            'created_date' => $deal->createdate ? $deal->createdate->format('Y-m-d') : now()->format('Y-m-d'),
        ];
    }

    /**
     * Get recent activities (tasks) for the deal
     */
    protected function getRecentActivities(Deal $deal, int $limit = 10): array
    {
        $activities = Activity::where('deal_id', $deal->id)
            ->where('type', 'TASK')
            ->orderBy('hubspot_createdAt', 'desc')
            ->limit($limit)
            ->get();

        return $activities->map(function ($activity) {
            return [
                'subject' => $activity->hubspot_task_subject,
                'body' => $activity->hubspot_task_body,
                'status' => $activity->hubspot_status,
                'priority' => $activity->hubspot_task_priority?->value ?? 'NORMAL',
                'completion_date' => $activity->hubspot_task_completion_date?->format('Y-m-d H:i:s'),
                'created_at' => $activity->hubspot_createdAt->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    /**
     * Get engagement history (calls, emails, meetings) from HubSpot
     */
    protected function getEngagementHistory(Deal $deal, User $user): array
    {
        try {
            $hubspot = HubspotClientHelper::createFactory($user);
            
            $engagements = [
                'calls' => [],
                'emails' => [],
                'meetings' => [],
            ];

            // Fetch associations
            $types = ['calls', 'emails', 'meetings'];
            
            foreach ($types as $type) {
                try {
                    $response = $hubspot->crm()->deals()->associationsApi()->getAll(
                        $deal->hubspot_id,
                        $type,
                        null,
                        20 // Limit to last 20 of each type
                    );

                    foreach ($response->getResults() as $association) {
                        $engagementId = $association->getToObjectId();
                        $details = $this->getEngagementDetails($hubspot, $engagementId, $type);
                        
                        if ($details) {
                            $engagements[$type][] = $details;
                        }
                    }
                } catch (\Exception $e) {
                    // No engagements of this type - continue
                }
            }

            // Sort by timestamp (most recent first)
            foreach ($engagements as $type => $items) {
                usort($engagements[$type], function ($a, $b) {
                    return strtotime($b['timestamp'] ?? '1970-01-01') <=> strtotime($a['timestamp'] ?? '1970-01-01');
                });
            }

            return $engagements;
        } catch (\Exception $e) {
            \Log::error('Failed to fetch engagement history', [
                'deal_id' => $deal->id,
                'error' => $e->getMessage(),
            ]);
            return ['calls' => [], 'emails' => [], 'meetings' => []];
        }
    }

    /**
     * Get details of a specific engagement
     */
    protected function getEngagementDetails($hubspot, string $engagementId, string $type): ?array
    {
        try {
            $fields = match($type) {
                'calls' => ['hs_timestamp', 'hs_call_status', 'hs_call_title', 'hs_call_body', 'hs_createdate'],
                'emails' => ['hs_timestamp', 'hs_email_subject', 'hs_email_text', 'hs_email_status', 'hs_createdate'],
                'meetings' => ['hs_timestamp', 'hs_meeting_title', 'hs_meeting_body', 'hs_meeting_outcome', 'hs_createdate'],
                default => [],
            };

            $response = match($type) {
                'calls' => $hubspot->crm()->objects()->calls()->basicApi()->getById($engagementId, $fields),
                'emails' => $hubspot->crm()->objects()->emails()->basicApi()->getById($engagementId, $fields),
                'meetings' => $hubspot->crm()->objects()->meetings()->basicApi()->getById($engagementId, $fields),
                default => null,
            };

            if ($response) {
                $props = $response->getProperties();
                return [
                    'id' => $engagementId,
                    'type' => $type,
                    'title' => match($type) {
                        'calls' => $props['hs_call_title'] ?? 'Call',
                        'emails' => $props['hs_email_subject'] ?? 'Email',
                        'meetings' => $props['hs_meeting_title'] ?? 'Meeting',
                        default => 'Engagement',
                    },
                    'body' => match($type) {
                        'calls' => $props['hs_call_body'] ?? '',
                        'emails' => $props['hs_email_text'] ?? '',
                        'meetings' => $props['hs_meeting_body'] ?? '',
                        default => '',
                    },
                    'status' => $props['hs_call_status'] ?? $props['hs_email_status'] ?? $props['hs_meeting_outcome'] ?? null,
                    'timestamp' => $props['hs_timestamp'] ?? $props['hs_createdate'] ?? null,
                    'created_at' => $response->getCreatedAt()?->format('Y-m-d H:i:s'),
                ];
            }
        } catch (\Exception $e) {
            // Engagement might have been deleted - skip it
        }

        return null;
    }

    /**
     * Calculate warnings for the deal
     */
    protected function calculateWarnings(Deal $deal): array
    {
        $warnings = [];
        $now = Carbon::now();

        // Last activity warning (30+ days)
        if ($deal->hubspot_updatedAt) {
            $daysSinceUpdate = $now->diffInDays($deal->hubspot_updatedAt);
            if ($daysSinceUpdate > 30) {
                $warnings[] = [
                    'type' => 'last_activity',
                    'message' => "No activity for {$daysSinceUpdate} days",
                    'severity' => $daysSinceUpdate > 60 ? 'high' : 'medium',
                ];
            }
        }

        // Stage time warning (stuck in stage)
        if ($deal->hs_date_entered) {
            $daysInStage = $now->diffInDays($deal->hs_date_entered);
            if ($daysInStage > 30) {
                $stage = $deal->relationLoaded('stage') ? $deal->stage : ($deal->stage_id ? $deal->stage()->first() : null);
                $stageName = $stage->label ?? 'current stage';
                $warnings[] = [
                    'type' => 'stage_time',
                    'message' => "In {$stageName} for {$daysInStage} days",
                    'severity' => $daysInStage > 60 ? 'high' : 'medium',
                ];
            }
        }

        // Close date warning (past close date)
        if ($deal->closedate && $now->gt($deal->closedate) && !$deal->hs_is_closed) {
            $daysPastClose = $now->diffInDays($deal->closedate);
            $warnings[] = [
                'type' => 'close_date',
                'message' => "Close date passed {$daysPastClose} days ago",
                'severity' => 'high',
            ];
        }

        // Creation date warning (old deal)
        $daysSinceCreation = $now->diffInDays($deal->createdate);
        if ($daysSinceCreation > 180) {
            $warnings[] = [
                'type' => 'creation_date',
                'message' => "Deal created {$daysSinceCreation} days ago",
                'severity' => 'medium',
            ];
        }

        return $warnings;
    }

    /**
     * Calculate time-based metrics
     */
    protected function calculateTimeMetrics(Deal $deal): array
    {
        $now = Carbon::now();

        return [
            'days_since_creation' => $deal->createdate ? $now->diffInDays($deal->createdate) : 0,
            'days_since_last_update' => $deal->hubspot_updatedAt ? $now->diffInDays($deal->hubspot_updatedAt) : 0,
            'days_in_current_stage' => $deal->hs_date_entered ? $now->diffInDays($deal->hs_date_entered) : 0,
            'days_until_close_date' => $deal->closedate ? $now->diffInDays($deal->closedate, false) : null,
        ];
    }
}
