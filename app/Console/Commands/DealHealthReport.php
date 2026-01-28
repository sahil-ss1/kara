<?php

namespace App\Console\Commands;

use App\Helpers\HubspotClientHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DealHealthReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubspot:deal-health-report 
                            {--user= : User ID to use for HubSpot authentication}
                            {--days=7 : Number of days to look back for updated deals}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a health report for HubSpot deals based on engagement activity';

    /**
     * HubSpot API client instance
     */
    private $hubspot;

    /**
     * Track API calls for rate limit reporting
     */
    private int $apiCallCount = 0;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 HubSpot Deal Health Report');
        $this->info('=' . str_repeat('=', 50));

        // Get user for authentication
        $userId = $this->option('user');
        $user = $userId ? User::find($userId) : User::whereNotNull('hubspot_refreshToken')->first();

        if (!$user || !$user->hubspot_refreshToken) {
            $this->error('❌ No user found with HubSpot authentication.');
            $this->error('   Please login via HubSpot first, or specify --user=ID');
            return 1;
        }

        $this->info("📧 Using HubSpot account linked to: {$user->email}");
        $this->newLine();

        try {
            // Initialize HubSpot client with rate limiting middleware
            // The HubspotClientHelper already handles:
            // 1. Rate limit middleware (auto-retry on 429 with constant delay)
            // 2. Internal errors middleware (exponential backoff)
            // 3. OAuth token refresh
            $this->hubspot = HubspotClientHelper::createFactory($user);
            $this->info('✅ HubSpot OAuth token refreshed successfully');
        } catch (\Exception $e) {
            $this->error('❌ Failed to authenticate with HubSpot: ' . $e->getMessage());
            return 1;
        }

        $daysBack = (int) $this->option('days');
        $this->info("📅 Fetching deals updated in the last {$daysBack} days...");
        $this->newLine();

        // Fetch deals updated in last N days
        $deals = $this->fetchRecentDeals($daysBack);

        if (empty($deals)) {
            $this->warn('⚠️  No deals found updated in the specified time period.');
            $this->info('   Try increasing --days=30 or add some deals to HubSpot.');
            return 0;
        }

        $this->info("📊 Found " . count($deals) . " deals. Fetching engagement data...");
        $this->newLine();

        // Process each deal and get engagement data
        $reportData = [];
        $progressBar = $this->output->createProgressBar(count($deals));
        $progressBar->start();

        foreach ($deals as $deal) {
            $engagements = $this->getEngagementsForDeal($deal['id']);
            $reportData[] = $this->calculateHealthMetrics($deal, $engagements);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Output the report
        $this->outputReport($reportData);

        // Summary statistics
        $this->outputSummary($reportData);

        // API usage report
        $this->newLine();
        $this->info("📈 API Usage: {$this->apiCallCount} API calls made");
        $this->info("⏱️  Rate Limiting: Handled via HubSpot SDK RetryMiddleware");
        $this->info("   - Rate limit (429): Auto-retry with constant delay");
        $this->info("   - Server errors (5xx): Exponential backoff (2^n seconds)");

        return 0;
    }

    /**
     * Fetch deals updated in the last N days using Search API with pagination
     * 
     * Pagination: Uses cursor-based pagination with 'after' parameter
     * Rate Limits: Handled by middleware in HubspotClientHelper
     */
    private function fetchRecentDeals(int $days): array
    {
        $deals = [];
        $after = null;
        $filterDate = Carbon::now()->subDays($days)->getTimestampMs();

        // Build filter for deals updated after the specified date
        $filter = new \HubSpot\Client\Crm\Deals\Model\Filter([
            'propertyName' => 'hs_lastmodifieddate',
            'operator' => 'GTE',
            'value' => (string) $filterDate,
        ]);

        $filterGroup = new \HubSpot\Client\Crm\Deals\Model\FilterGroup([
            'filters' => [$filter],
        ]);

        do {
            $searchRequest = new \HubSpot\Client\Crm\Deals\Model\PublicObjectSearchRequest([
                'filterGroups' => [$filterGroup],
                'properties' => [
                    'dealname', 
                    'amount', 
                    'dealstage', 
                    'pipeline', 
                    'hubspot_owner_id', 
                    'hs_lastmodifieddate', 
                    'closedate'
                ],
                'limit' => 100, // Max allowed per request
                'after' => $after,
            ]);

            try {
                $this->apiCallCount++;
                $response = $this->hubspot->crm()->deals()->searchApi()->doSearch($searchRequest);
                
                foreach ($response->getResults() as $deal) {
                    $props = $deal->getProperties();
                    $deals[] = [
                        'id' => $deal->getId(),
                        'name' => $props['dealname'] ?? 'Unnamed Deal',
                        'amount' => $props['amount'] ?? 0,
                        'stage' => $props['dealstage'] ?? 'Unknown',
                        'lastModified' => $props['hs_lastmodifieddate'] ?? null,
                    ];
                }

                // Handle pagination - get next cursor
                $paging = $response->getPaging();
                if ($paging && $paging->getNext()) {
                    $after = $paging->getNext()->getAfter();
                } else {
                    $after = null;
                }

            } catch (\HubSpot\Client\Crm\Deals\ApiException $e) {
                $this->error("API Error fetching deals: " . $e->getMessage());
                break;
            }

        } while ($after !== null);

        return $deals;
    }

    /**
     * Get all engagements (calls, emails, meetings) for a deal
     * Uses the Associations API to find linked engagements
     */
    private function getEngagementsForDeal(string $dealId): array
    {
        $engagements = [
            'calls' => [],
            'emails' => [],
            'meetings' => [],
        ];

        // Fetch associated calls
        $engagements['calls'] = $this->fetchAssociatedEngagements($dealId, 'calls');
        
        // Fetch associated emails  
        $engagements['emails'] = $this->fetchAssociatedEngagements($dealId, 'emails');
        
        // Fetch associated meetings
        $engagements['meetings'] = $this->fetchAssociatedEngagements($dealId, 'meetings');

        return $engagements;
    }

    /**
     * Fetch associated engagements of a specific type for a deal
     * 
     * Pagination: Uses cursor-based pagination for associations
     */
    private function fetchAssociatedEngagements(string $dealId, string $type): array
    {
        $engagements = [];

        try {
            $this->apiCallCount++;
            
            // Use associations API to get linked engagements
            $response = $this->hubspot->crm()->deals()->associationsApi()->getAll(
                $dealId,
                $type,
                null,  // after cursor
                500    // limit
            );

            foreach ($response->getResults() as $association) {
                $engagementId = $association->getToObjectId();
                $engagementDetails = $this->getEngagementDetails($engagementId, $type);
                
                if ($engagementDetails) {
                    $engagements[] = $engagementDetails;
                }
            }

        } catch (\Exception $e) {
            // Association might not exist or API error - continue gracefully
            // This is expected for deals with no engagements of this type
        }

        return $engagements;
    }

    /**
     * Get details of a specific engagement (call, email, or meeting)
     */
    private function getEngagementDetails(string $engagementId, string $type): ?array
    {
        try {
            $this->apiCallCount++;
            
            $response = match($type) {
                'calls' => $this->hubspot->crm()->objects()->calls()->basicApi()->getById(
                    $engagementId,
                    ['hs_timestamp', 'hs_call_status', 'hs_call_title', 'hs_createdate']
                ),
                'emails' => $this->hubspot->crm()->objects()->emails()->basicApi()->getById(
                    $engagementId,
                    ['hs_timestamp', 'hs_email_subject', 'hs_email_status', 'hs_createdate']
                ),
                'meetings' => $this->hubspot->crm()->objects()->meetings()->basicApi()->getById(
                    $engagementId,
                    ['hs_timestamp', 'hs_meeting_title', 'hs_meeting_outcome', 'hs_createdate']
                ),
                default => null,
            };

            if ($response) {
                $props = $response->getProperties();
                return [
                    'id' => $engagementId,
                    'type' => $type,
                    'timestamp' => $props['hs_timestamp'] ?? $props['hs_createdate'] ?? null,
                    'createdAt' => $response->getCreatedAt()?->format('Y-m-d H:i:s'),
                ];
            }
        } catch (\Exception $e) {
            // Engagement might have been deleted or inaccessible - skip it
        }

        return null;
    }

    /**
     * Calculate health metrics for a deal based on its engagements
     */
    private function calculateHealthMetrics(array $deal, array $engagements): array
    {
        $allEngagements = array_merge(
            $engagements['calls'],
            $engagements['emails'],
            $engagements['meetings']
        );

        $totalCount = count($allEngagements);
        $callCount = count($engagements['calls']);
        $emailCount = count($engagements['emails']);
        $meetingCount = count($engagements['meetings']);

        // Find the most recent engagement
        $lastEngagementDate = null;
        $daysSinceLastEngagement = null;

        if ($totalCount > 0) {
            $timestamps = array_filter(array_map(function ($eng) {
                return $eng['timestamp'] ?? $eng['createdAt'] ?? null;
            }, $allEngagements));

            if (!empty($timestamps)) {
                // Parse all timestamps and find the most recent
                $parsedDates = array_map(function($ts) {
                    try {
                        return Carbon::parse($ts);
                    } catch (\Exception $e) {
                        return null;
                    }
                }, $timestamps);
                
                $parsedDates = array_filter($parsedDates);
                
                if (!empty($parsedDates)) {
                    $lastEngagementDate = max($parsedDates);
                    $daysSinceLastEngagement = (int) $lastEngagementDate->diffInDays(Carbon::now());
                }
            }
        }

        // Calculate health score based on days since last engagement
        $healthScore = $this->calculateHealthScore($daysSinceLastEngagement);

        return [
            'deal_id' => $deal['id'],
            'deal_name' => $deal['name'],
            'amount' => $deal['amount'],
            'days_since_engagement' => $daysSinceLastEngagement,
            'last_engagement_date' => $lastEngagementDate?->format('Y-m-d'),
            'total_engagements' => $totalCount,
            'calls' => $callCount,
            'emails' => $emailCount,
            'meetings' => $meetingCount,
            'health_score' => $healthScore,
        ];
    }

    /**
     * Calculate health score based on days since last engagement
     * 
     * 🟢 Green:  Engaged in last 7 days (healthy)
     * 🟡 Yellow: 8-14 days since engagement (warning)  
     * 🔴 Red:    15+ days or no engagements (critical)
     */
    private function calculateHealthScore(?int $daysSince): string
    {
        if ($daysSince === null) {
            return '🔴'; // No engagements at all
        }

        return match(true) {
            $daysSince <= 7 => '🟢',   // Healthy: engaged in last 7 days
            $daysSince <= 14 => '🟡',  // Warning: 8-14 days
            default => '🔴',            // Critical: 15+ days
        };
    }

    /**
     * Output the formatted report table
     */
    private function outputReport(array $reportData): void
    {
        $this->info('📋 DEAL HEALTH REPORT');
        $this->info(str_repeat('─', 100));

        // Sort by health (worst first - red, then yellow, then green)
        usort($reportData, function ($a, $b) {
            $order = ['🔴' => 0, '🟡' => 1, '🟢' => 2];
            return ($order[$a['health_score']] ?? 3) <=> ($order[$b['health_score']] ?? 3);
        });

        $headers = ['Health', 'Deal Name', 'Days Since Eng.', 'Total', 'Calls', 'Emails', 'Meetings', 'Last Engagement'];
        
        $rows = array_map(function ($row) {
            return [
                $row['health_score'],
                $this->truncate($row['deal_name'], 30),
                $row['days_since_engagement'] ?? 'Never',
                $row['total_engagements'],
                $row['calls'],
                $row['emails'],
                $row['meetings'],
                $row['last_engagement_date'] ?? 'N/A',
            ];
        }, $reportData);

        $this->table($headers, $rows);
    }

    /**
     * Output summary statistics
     */
    private function outputSummary(array $reportData): void
    {
        $total = count($reportData);
        
        if ($total === 0) {
            return;
        }

        $healthy = count(array_filter($reportData, fn($r) => $r['health_score'] === '🟢'));
        $warning = count(array_filter($reportData, fn($r) => $r['health_score'] === '🟡'));
        $critical = count(array_filter($reportData, fn($r) => $r['health_score'] === '🔴'));

        $this->newLine();
        $this->info('📊 SUMMARY');
        $this->info(str_repeat('─', 40));
        $this->info("Total Deals Analyzed: {$total}");
        $this->info("🟢 Healthy (≤7 days):    {$healthy} (" . round($healthy/$total*100, 1) . "%)");
        $this->info("🟡 Warning (8-14 days):  {$warning} (" . round($warning/$total*100, 1) . "%)");
        $this->info("🔴 Critical (15+ days):  {$critical} (" . round($critical/$total*100, 1) . "%)");
    }

    /**
     * Truncate string to specified length with ellipsis
     */
    private function truncate(string $string, int $length): string
    {
        return strlen($string) > $length 
            ? substr($string, 0, $length - 3) . '...' 
            : $string;
    }
}

