<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Console\Command;

class AssignOrganization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-organization 
                            {user : User ID or email}
                            {organization : Organization ID, name, or hubspot_portalId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a user to an organization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userIdentifier = $this->argument('user');
        $orgIdentifier = $this->argument('organization');

        // Find user
        $user = is_numeric($userIdentifier) 
            ? User::find($userIdentifier)
            : User::where('email', $userIdentifier)->first();

        if (!$user) {
            $this->error("User not found: {$userIdentifier}");
            return 1;
        }

        // Find organization
        $organization = null;
        if (is_numeric($orgIdentifier)) {
            $organization = Organization::find($orgIdentifier);
        } else {
            // Try by name first
            $organization = Organization::where('name', $orgIdentifier)->first();
            
            // If not found, try by hubspot_portalId
            if (!$organization) {
                $organization = Organization::where('hubspot_portalId', $orgIdentifier)->first();
            }
        }

        if (!$organization) {
            $this->error("Organization not found: {$orgIdentifier}");
            $this->info("Available organizations:");
            Organization::all(['id', 'name', 'hubspot_portalId'])->each(function ($org) {
                $this->line("  ID: {$org->id}, Name: {$org->name}, Portal ID: {$org->hubspot_portalId}");
            });
            return 1;
        }

        // Check if already attached
        if ($user->organizations()->where('organization_id', $organization->id)->exists()) {
            $this->warn("User {$user->email} is already assigned to organization {$organization->name}");
            return 0;
        }

        // Attach organization
        $user->organizations()->attach($organization->id);

        $this->info("✓ Successfully assigned user '{$user->email}' (ID: {$user->id}) to organization '{$organization->name}' (ID: {$organization->id})");

        return 0;
    }
}

