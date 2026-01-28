<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Imports\GoogleCalendars;
use Auth;
use Illuminate\Http\Request;

class OneOnOneMeetingController extends Controller
{
    /**
     * Fetch upcoming 1-on-1 meetings for the authenticated user
     * Returns structured data for meeting prep screen
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Please login first',
            ], 401);
        }

        if (!$user->google_token) {
            return response()->json([
                'error' => 'Google Calendar not connected',
                'message' => 'Please connect Google Calendar first via /google/login',
            ], 400);
        }

        try {
            $daysAhead = (int) $request->get('days', 7);
            $calendarId = $request->get('calendar') ?? $user->google_calendar_id ?? 'primary';

            $meetings = GoogleCalendars::get_one_on_one_meetings($user, $daysAhead, $calendarId);

            return response()->json([
                'success' => true,
                'data' => $meetings,
                'count' => count($meetings),
                'days_ahead' => $daysAhead,
                'calendar_id' => $calendarId,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch meetings',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

