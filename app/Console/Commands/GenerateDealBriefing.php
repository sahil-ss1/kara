<?php

namespace App\Console\Commands;

use App\Helpers\HubspotClientHelper;
use App\Models\Deal;
use App\Models\User;
use App\Services\AIService;
use App\Services\DealBriefingService;
use Illuminate\Console\Command;

class GenerateDealBriefing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:deal-briefing 
                            {deal_id : The ID of the deal (local ID or HubSpot ID)}
                            {--user= : User ID for HubSpot authentication}
                            {--hubspot : Treat deal_id as HubSpot ID instead of local ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate AI-powered deal briefing for 1-on-1 meeting prep using Groq';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 AI Deal Briefing Generator (Groq)');
        $this->info('=' . str_repeat('=', 50));

        // Get user first (needed for HubSpot API access)
        $userId = $this->option('user');
        $user = $userId ? User::find($userId) : User::whereNotNull('hubspot_refreshToken')->first();

        if (!$user || !$user->hubspot_refreshToken) {
            $this->error('❌ No user found with HubSpot authentication.');
            $this->error('   Please specify --user=ID or connect HubSpot first');
            return 1;
        }

        // Get deal - try both local ID and HubSpot ID
        $dealId = $this->argument('deal_id');
        $useHubspotId = $this->option('hubspot');
        
        // Try to find deal in local database
        if ($useHubspotId) {
            $deal = Deal::where('hubspot_id', $dealId)->first();
        } else {
            // Try local ID first, then HubSpot ID as fallback
            $deal = Deal::find($dealId);
            if (!$deal) {
                $deal = Deal::where('hubspot_id', $dealId)->first();
            }
        }
        
        // If not found locally, fetch from HubSpot
        if (!$deal) {
            $this->warn("⚠️  Deal not found in local database. Fetching from HubSpot...");
            
            try {
                $hubspot = HubspotClientHelper::createFactory($user);
                $hubspotDeal = $hubspot->crm()->deals()->basicApi()->getById(
                    $dealId,
                    'pipeline,dealstage,dealname,amount,closedate,createdate,hubspot_owner_id,hs_lastmodifieddate,hs_is_closed,hs_is_closed_won,hs_next_step,hs_manual_forecast_category'
                );
                
                $properties = $hubspotDeal->getProperties();
                
                // Create a temporary Deal object for processing
                $deal = new Deal();
                $deal->hubspot_id = $dealId;
                $deal->name = $properties['dealname'] ?? 'Unnamed Deal';
                $deal->amount = $properties['amount'] ?? 0;
                $deal->closedate = isset($properties['closedate']) ? \Carbon\Carbon::parse($properties['closedate']) : null;
                $deal->createdate = isset($properties['createdate']) ? \Carbon\Carbon::parse($properties['createdate']) : now();
                $deal->hubspot_updatedAt = isset($properties['hs_lastmodifieddate']) ? \Carbon\Carbon::parse($properties['hs_lastmodifieddate']) : now();
                $deal->hs_next_step = $properties['hs_next_step'] ?? null;
                $deal->hs_is_closed = filter_var($properties['hs_is_closed'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $deal->hs_is_closed_won = filter_var($properties['hs_is_closed_won'] ?? false, FILTER_VALIDATE_BOOLEAN);
                
                // Try to get pipeline/stage info if available
                if (isset($properties['pipeline']) && isset($properties['dealstage'])) {
                    $pipeline = \App\Models\Pipeline::where('hubspot_id', $properties['pipeline'])->first();
                    $stage = \App\Models\Stage::where('hubspot_id', $properties['dealstage'])->first();
                    if ($pipeline) $deal->setRelation('pipeline', $pipeline);
                    if ($stage) $deal->setRelation('stage', $stage);
                }
                
                // Try to get owner info
                if (isset($properties['hubspot_owner_id'])) {
                    $member = \App\Models\Member::where('hubspot_id', $properties['hubspot_owner_id'])->first();
                    if ($member) $deal->setRelation('member', $member);
                }
                
                $this->info("   ✓ Fetched deal from HubSpot: {$deal->name}");
                $this->warn("   Note: Deal is not in local database. Consider syncing deals for better performance.");
                
            } catch (\Exception $e) {
                $this->error("❌ Failed to fetch deal from HubSpot: " . $e->getMessage());
                $this->warn("   Make sure the HubSpot ID is correct and the deal exists.");
                return 1;
            }
        }

        $this->info("📋 Deal: {$deal->name}");
        $this->info("💰 Amount: $" . number_format($deal->amount ?? 0, 2));
        if ($deal->hubspot_id) {
            $this->info("🔗 HubSpot ID: {$deal->hubspot_id}");
        }
        $this->newLine();

        $this->info("👤 Using HubSpot account: {$user->email}");
        $this->newLine();

        try {
            // Gather deal data
            $this->info('📊 Gathering deal data...');
            $briefingService = app(DealBriefingService::class);
            $dealData = $briefingService->gatherDealData($deal, $user);
            
            $this->info("   ✓ Deal info collected");
            $this->info("   ✓ " . count($dealData['recent_activities']) . " recent activities");
            $this->info("   ✓ " . (count($dealData['engagements']['calls']) + count($dealData['engagements']['emails']) + count($dealData['engagements']['meetings'])) . " engagements");
            $this->info("   ✓ " . count($dealData['warnings']) . " warnings identified");
            $this->newLine();

            // Generate briefing
            $this->info('🤖 Generating AI briefing with Groq...');
            $aiService = app(AIService::class);
            $briefing = $aiService->generateDealBriefing($dealData);
            
            $this->newLine();
            $this->info('📝 DEAL BRIEFING');
            $this->info(str_repeat('─', 80));
            $this->line($briefing);
            $this->info(str_repeat('─', 80));
            $this->newLine();

            $this->info('✅ Briefing generated successfully!');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error generating briefing: ' . $e->getMessage());
            if ($this->option('verbose')) {
                $this->error('Stack trace: ' . $e->getTraceAsString());
            }
            return 1;
        }
    }
}
