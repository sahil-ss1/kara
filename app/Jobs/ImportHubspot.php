<?php

namespace App\Jobs;

use App\Helpers\HubspotClientHelper;
use App\Imports\HubspotDeals;
use App\Imports\HubspotForecastCategories;
use App\Imports\HubspotOwners;
use App\Imports\HubspotPipelines;
use App\Models\Organization;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportHubspot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $organization_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $organization_id)
    {
        $this->user = $user;
        $this->organization_id = $organization_id;
    }

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 900; // 15 minutes (increased from 540 for large imports)

    /**
     * The unique ID of the job to prevent duplicate processing.
     *
     * @return string
     */
    public function uniqueId()
    {
        return 'import-hubspot-' . $this->organization_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function handle()
    {
        $organization = Organization::find($this->organization_id);
        
        // Prevent duplicate simultaneous imports
        if ($organization->synchronizing) {
            \Log::info('HubSpot import already in progress', [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name
            ]);
            return;
        }

        $organization->synchronizing = true;
        $organization->save();

        try {
            $hubspot = HubspotClientHelper::createFactory($this->user);

            // Each sync method handles its own transactions internally
            HubspotPipelines::sync_with_hubspot($hubspot, $this->user, $organization->id);
            HubspotForecastCategories::sync_with_hubspot($hubspot, $this->user, $organization->id);
            HubspotOwners::sync_with_hubspot($hubspot, $this->user, $organization->id);
            HubspotDeals::sync_with_hubspot($hubspot, $this->user, $organization->id);

            $organization->last_sync = Carbon::now();
            $organization->synchronizing = false;
            $organization->save();

            \Log::info('HubSpot import completed successfully', [
                'organization_id' => $organization->id,
                'organization_name' => $organization->name,
                'synced_at' => $organization->last_sync
            ]);
        } catch (\Exception $e) {
            $organization->synchronizing = false;
            $organization->save();
            
            // Log the detailed error
            \Log::error('HubSpot import failed', [
                'organization_id' => $this->organization_id,
                'user_id' => $this->user->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Re-throw to mark job as failed in queue
            throw $e;
        }
    }
}
