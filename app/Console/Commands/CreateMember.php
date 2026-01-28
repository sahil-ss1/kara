<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Organization;
use Illuminate\Console\Command;

class CreateMember extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:create 
                            {organization : Organization ID, name, or hubspot_portalId}
                            {email : Member email address}
                            {firstName : Member first name}
                            {lastName : Member last name}
                            {--hubspot_id= : HubSpot ID (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a member in an organization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orgIdentifier = $this->argument('organization');
        $email = $this->argument('email');
        $firstName = $this->argument('firstName');
        $lastName = $this->argument('lastName');
        $hubspotId = $this->option('hubspot_id') ?? 'manual_' . time();

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

        // Check if member already exists
        $existingMember = Member::where('organization_id', $organization->id)
            ->whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($email))])
            ->first();

        if ($existingMember) {
            $this->warn("Member with email '{$email}' already exists in organization '{$organization->name}' (ID: {$existingMember->id})");
            $this->info("Updating existing member...");
            
            $existingMember->update([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'active' => true,
            ]);
            
            if ($this->option('hubspot_id')) {
                $existingMember->update(['hubspot_id' => $hubspotId]);
            }
            
            $this->info("✓ Member updated successfully!");
            return 0;
        }

        // Create new member
        $member = Member::create([
            'organization_id' => $organization->id,
            'hubspot_id' => $hubspotId,
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'active' => true,
            'hubspot_archived' => false,
            'hubspot_createdAt' => now(),
            'hubspot_updatedAt' => now(),
        ]);

        $this->info("✓ Successfully created member:");
        $this->line("  ID: {$member->id}");
        $this->line("  Name: {$member->firstName} {$member->lastName}");
        $this->line("  Email: {$member->email}");
        $this->line("  Organization: {$organization->name} (ID: {$organization->id})");

        return 0;
    }
}

