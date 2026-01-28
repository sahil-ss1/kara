<?php

namespace App\Services;

use App\Models\User;

class Google
{
    protected $client;

    function __construct()
    {
        $client = new \Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setScopes(config('services.google.scopes'));
        $client->setApprovalPrompt(config('services.google.approval_prompt'));
        $client->setAccessType(config('services.google.access_type'));
        $client->setIncludeGrantedScopes(config('services.google.include_granted_scopes'));
        $this->client = $client;
    }

    public function connectUsing($token, $refresh_token)
    {
        // Handle token format
        if (is_string($token)) {
            $tokenArray = json_decode($token, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $token = $tokenArray;
            }
        }
        
        $this->client->setAccessToken($token);
        
        if ($this->client->isAccessTokenExpired() && $refresh_token) {
            // Handle refresh token format
            if (is_string($refresh_token)) {
                $refreshTokenArray = json_decode($refresh_token, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $refresh_token = $refreshTokenArray;
                }
            }
            
            $token = $this->client->refreshToken($refresh_token);
            $this->client->setAccessToken($token);
        }

        return $this;
    }
    
    /**
     * Get the Google Client instance
     * 
     * @return \Google_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function connectUser(User $user){
        try {
            $token = $user->google_token;
            
            // Handle token format - could be JSON string or array
            if (is_string($token)) {
                $tokenArray = json_decode($token, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $token = $tokenArray;
                }
            }
            
            $this->client->setAccessToken($token);
            
            // Check if token is expired and refresh if needed
            if ($this->client->isAccessTokenExpired() && $user->google_refresh_token) {
                $refresh_token = $user->google_refresh_token;
                
                // Handle refresh token format
                if (is_string($refresh_token)) {
                    $refreshTokenArray = json_decode($refresh_token, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $refresh_token = $refreshTokenArray;
                    }
                }
                
                $token = $this->client->refreshToken($refresh_token);
                $this->client->setAccessToken($token);
                
                // Update stored token
                $user->update([
                    'google_token' => $token
                ]);
            }
        } catch (\Google_Service_Exception $e) {
            // Handle Google API errors
            if ($e->getCode() == 401) {
                // Token invalid - clear tokens so user can reconnect
                $user->update([
                    'google_token' => null,
                    'google_refresh_token' => null,
                ]);
                throw new \Exception('Google Calendar authentication expired. Please reconnect via /google/login');
            }
            throw $e;
        } catch (\Exception $e) {
            // Log other errors but don't fail silently
            \Log::error('Google service connection error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
        
        return $this;
    }

    /*
    public function connectWithSynchronizable($synchronizable)
    {
        $token = $this->getTokenFromSynchronizable($synchronizable);

        return $this->connectUsing($token);
    }

    protected function getTokenFromSynchronizable($synchronizable)
    {
        switch (true) {
            case $synchronizable instanceof GoogleAccount:
                return $synchronizable->token;

            case $synchronizable instanceof Calendar:
                return $synchronizable->googleAccount->token;

            default:
                throw new \Exception("Invalid Synchronizable");
        }
    }
    */

    public function revokeToken($token = null)
    {
        $token = $token ?? $this->client->getAccessToken();

        return $this->client->revokeToken($token);
    }

    public function service($service)
    {
        $classname = "Google_Service_$service";

        return new $classname($this->client);
    }

    public function __call($method, $args)
    {
        if (! method_exists($this->client, $method)) {
            throw new \Exception("Call to undefined method '{$method}'");
        }

        return call_user_func_array([$this->client, $method], $args);
    }
}
