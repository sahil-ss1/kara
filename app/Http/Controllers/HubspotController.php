<?php

namespace App\Http\Controllers;

use App\Helpers\HubspotClientHelper;
use App\Imports\HubspotDeals;
use App\Imports\HubspotForecastCategories;
use App\Imports\HubspotOwners;
use App\Imports\HubspotPipelines;
use App\Models\Organization;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Hash;
use Laravel\Socialite\Facades\Socialite;

class HubspotController extends Controller
{
    /**
     * Show the HubSpot install/connect page
     * This is the landing page for marketplace installs
     */
    public function install()
    {
        return view('hubspot.install');
    }

    public function hubspotRedirect()
    {
        $parameters = [
            'prompt' => 'consent', // Always ask for consent to ensure refresh token is obtained
        ];

        // OAuth scopes requested for HubSpot integration
        // Scope justification:
        // - oauth: Required for OAuth authentication
        // - crm.objects.deals.read: Read deals, pipelines, stages, and deal associations
        // - crm.objects.owners.read: Read HubSpot owners (team members)
        // - crm.schemas.deals.read: Read deal property schemas to understand deal structure
        // - crm.objects.deals.write: Update deal properties (stage changes, etc.)
        // - crm.lists.read: Read HubSpot lists (planned for future use)
        // - crm.objects.contacts.read: Read contact records (planned for future use)
        // - crm.objects.companies.read: Read company records (planned for future use)
        // - crm.schemas.contacts.read: Read contact property schemas (planned for future use)
        // - crm.schemas.companies.read: Read company property schemas (planned for future use)
        $scopes = [
            'oauth',
            'crm.objects.deals.read',
            'crm.objects.owners.read',
            'crm.schemas.deals.read',
            'crm.objects.deals.write',
            'crm.lists.read', // Planned for future use
            'crm.objects.contacts.read', // Planned for future use
            'crm.objects.companies.read', // Planned for future use
            'crm.schemas.contacts.read', // Planned for future use
            'crm.schemas.companies.read', // Planned for future use
        ];

        // Get redirect URI from config or construct it from app URL
        $redirectUri = config('services.hubspot.redirect');
        if (!$redirectUri) {
            $redirectUri = url('/hubspot/callback');
        }

        // Ensure redirect URI doesn't have trailing slash (HubSpot is strict about this)
        $redirectUri = rtrim($redirectUri, '/');

        // Log the redirect URI being used (for debugging - remove in production if needed)
        if (config('app.debug')) {
            \Log::info('HubSpot OAuth redirect', [
                'redirect_uri' => $redirectUri,
                'config_value' => config('services.hubspot.redirect'),
                'app_url' => config('app.url')
            ]);
        }

        return Socialite::driver('hubspot')
            ->scopes($scopes)
            ->with(['redirect_uri' => $redirectUri])
            ->redirect();
    }

    public function hubspotCallback()
    {
        try {
            // Use stateless() to avoid InvalidStateException with OAuth
            $hubspotUser = Socialite::driver('hubspot')->stateless()->user();

            $account_details = $this->account_details($hubspotUser->token);
            session(['hubspot_portalId' => $account_details->portalId]);

            $user = User::where('email', $hubspotUser->getEmail())->first();
            if (! $user) {
                $user = User::updateOrCreate([
                    'email' => $hubspotUser->getEmail(),
                ], [
                    'name' => $hubspotUser->getEmail(),
                    'hubspot_id' => $hubspotUser->getId(),
                    'password' => Hash::make($hubspotUser->getEmail().'@'.$hubspotUser->getId()),
                    'hubspot_token' => $hubspotUser->token,
                    'hubspot_refreshToken' => $hubspotUser->refreshToken,
                    'active' => true,
                    'role_id' => 2,
                ]);
            } else {
                $user->update([
                    'hubspot_id' => $hubspotUser->getId(),
                    'hubspot_token' => $hubspotUser->token,
                    'hubspot_refreshToken' => $hubspotUser->refreshToken,
                ]);
            }

            $organization = $user->organization();
            if (! $organization) {
                $organization = Organization::where('hubspot_portalId', $account_details->portalId)->first();
                if ($organization) {
                    $user->organizations()->attach($organization);
                } else {
                    $var = explode('@', $user->email);
                    $domain_name = array_pop($var);
                    $domain_name = $domain_name.'_'.$account_details->portalId;
                    $organization = Organization::create([
                        'name' => $domain_name,
                        'currency' => 'EUR',
                        'hubspot_portalId' => $account_details->portalId,
                        'hubspot_uiDomain' => $account_details->uiDomain,
                        'timezone' => $account_details->timeZone,
                    ]);
                    $user->role_id = 3;
                    $user->organizations()->attach($organization);
                    $user->save();
                }
            }

            Auth::loginUsingId($user->id);

            // Trigger initial sync after HubSpot connection
            $shouldSync = false;
            if ($organization) {
                // Check if this is first sync or if sync hasn't happened
                if (!$organization->last_sync) {
                    $shouldSync = true;
                }
            }

            if ($shouldSync) {
                if (config('app.env') == 'local') {
                    // For local environment, redirect to sync route (runs synchronously)
                    return redirect()->route('hubspot.sync')->with('success', 'HubSpot connected! Starting initial data synchronization...');
                } else {
                    // For production, dispatch background job
                    \App\Jobs\ImportHubspot::dispatch($user, $organization->id);
                    return redirect()->route('home')->with('success', 'HubSpot account connected successfully! Your data synchronization has been started in the background. This may take a few minutes.');
                }
            }

            // If already synced before, just redirect normally
            return redirect()->route('home')->with('success', 'HubSpot account reconnected successfully!');

        } catch (\Throwable $th) {
            \Log::error('HubSpot OAuth callback error', [
                'message' => $th->getMessage(),
                'trace' => config('app.debug') ? $th->getTraceAsString() : null
            ]);
            
            // Redirect to install page with error message
            $errorMessage = config('app.debug') 
                ? 'Failed to connect HubSpot account: ' . $th->getMessage()
                : 'Failed to connect HubSpot account. Please try again.';
            
            return redirect()->route('hubspot.install')->with('error', $errorMessage);
        }
    }

    public function sync_all()
    {
        if (config('app.env') == 'local') {
            $hubspot = HubspotClientHelper::createFactory(Auth::user());

            $organization = Auth::user()->organization();
            if (!$organization) {
                return redirect()->route('home')->with('error', 'No organization found');
            }
            $organization->synchronizing = true;
            $organization->save();

            DB::beginTransaction();
            try {
                HubspotPipelines::sync_with_hubspot($hubspot, Auth::user(), $organization->id);
                HubspotForecastCategories::sync_with_hubspot($hubspot, Auth::user(), $organization->id);
                HubspotOwners::sync_with_hubspot($hubspot, Auth::user(), $organization->id);
                HubspotDeals::sync_with_hubspot($hubspot, Auth::user(), $organization->id);

                $organization->last_sync = Carbon::now();
                $organization->synchronizing = false;
                $organization->save();
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $organization->synchronizing = false;
                $organization->save();
            }
        } else {
            $organization = Auth::user()->organization();
            if (!$organization) {
                return redirect()->route('home')->with('error', 'No organization found');
            }
            if (! $organization->isSynchronizing() && (($organization->getDayFromLastSync() == null) || ($organization->getDayFromLastSync() > 1))) {
                \App\Jobs\ImportHubspot::dispatch(Auth::user(), $organization->id);
                $organization->synchronizing = true;
                $organization->save();
            }
        }

        return redirect()->route('home');
    }

    public function account_details($token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.hubapi.com/account-info/v3/details',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: Bearer '.$token,
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            \Log::error('HubSpot account details API error', ['error' => $err]);
            return [];
        } else {
            return json_decode($response);
        }

    }
}
