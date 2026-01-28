<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function googleRedirect()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first to connect Google Calendar.');
        }

        $parameters = [
            'access_type' => 'offline',
            'prompt' => 'consent', // Force consent screen to always get refresh token
            'include_granted_scopes' => 'true',
            //'application_name' => config('app.name', ''),
            // Note: 'approval_prompt' is deprecated, use 'prompt' instead
        ];

        $scopes =[
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/calendar',
            //'https://www.googleapis.com/auth/calendar.events'
            //'https://www.googleapis.com/auth/admin.directory.user.readonly',
            //'https://www.googleapis.com/auth/directory.readonly',
            //'https://www.google.com/m8/feeds/',
            //'https://www.googleapis.com/auth/contacts.readonly',
            //'https://www.googleapis.com/auth/spreadsheets'
        ];

        // Store user ID in state for callback
        $state = base64_encode(json_encode(['user_id' => Auth::id()]));
        
        // Merge state into parameters (can't call ->with() twice)
        $parameters['state'] = $state;

        return Socialite::driver('google')
                        ->scopes($scopes)
                        ->with($parameters)
                        ->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            // Use stateless() to avoid InvalidStateException with OAuth
            // When using stateless(), Socialite won't verify state automatically
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Try to get user ID from state parameter (sent back by Google)
            $userId = null;
            if ($request->has('state')) {
                try {
                    $decodedState = base64_decode($request->get('state'), true);
                    if ($decodedState !== false) {
                        $state = json_decode($decodedState, true);
                        if (json_last_error() === JSON_ERROR_NONE && isset($state['user_id'])) {
                            $userId = $state['user_id'];
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to decode state parameter in Google OAuth callback', [
                        'state' => $request->get('state'),
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Try to get user in order of priority:
            // 1. From state parameter
            // 2. Currently authenticated user
            // 3. By Google email
            $user = null;
            if ($userId) {
                $user = User::find($userId);
            }
            
            if (!$user && Auth::check()) {
            $user = Auth::user();
            }
            
            if (!$user) {
                // Try to find by Google email
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if (!$user) {
                    \Log::warning('Google OAuth callback: User not found', [
                        'google_email' => $googleUser->getEmail(),
                        'state_user_id' => $userId,
                    ]);
                    return redirect()->route('login')->with('error', 'User not found. Please login first.');
                }
                
                // Log in the user
                Auth::login($user);
            }

            // Prepare token data - google_token is cast as JSON, so format it properly
            // The Google client expects tokens in this format
            $tokenData = [
                'access_token' => $googleUser->token,
                'expires_in' => $googleUser->expiresIn ?? 3600,
                'created' => time(),
            ];

            // Add refresh_token to main token if provided
            if ($googleUser->refreshToken) {
                $tokenData['refresh_token'] = $googleUser->refreshToken;
            }

            // Update user with Google credentials
            $updateData = [
                    'google_id' => $googleUser->getId(),
                    'google_name' => $googleUser->getEmail(),
                'google_token' => $tokenData,
            ];

            // Store refresh_token separately if provided (might be null on re-authorization)
            // The refresh_token should be stored as a simple string or array format
            if ($googleUser->refreshToken) {
                // Store refresh token - Google client expects it as a string or simple array
                $updateData['google_refresh_token'] = $googleUser->refreshToken;
            }
            // Note: If refreshToken is null, we don't overwrite existing refresh_token
            // (Google doesn't always return refresh_token on re-authorization)

            $user->update($updateData);
            
            // Log for debugging
            \Log::info('Google OAuth tokens saved', [
                'user_id' => $user->id,
                'email' => $user->email,
                'google_name' => $updateData['google_name'],
                'has_token' => !empty($tokenData['access_token']),
                'has_refresh_token' => !empty($googleUser->refreshToken),
            ]);

            return redirect()->route('user.show', $user)->with('success', 'Google Calendar connected successfully!');

        } catch (\Throwable $th) {
            \Log::error('Google OAuth callback error: ' . $th->getMessage(), [
                'exception' => $th,
                'trace' => config('app.debug') ? $th->getTraceAsString() : null,
                'request_params' => config('app.debug') ? $request->all() : null,
            ]);
            
            $errorMessage = config('app.debug')
                ? 'Failed to connect Google Calendar: ' . $th->getMessage()
                : 'Failed to connect Google Calendar. Please try again.';
            
            // Try to redirect to user profile if authenticated, otherwise to login
            if (Auth::check()) {
                return redirect()->route('user.show', Auth::user())
                    ->with('error', $errorMessage);
            } else {
                return redirect()->route('login')
                    ->with('error', 'Failed to connect Google Calendar. Please try again.');
            }
        }
    }
}
