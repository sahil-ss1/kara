<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Services\AIService;
use App\Services\DealBriefingService;
use Auth;
use Illuminate\Http\Request;

class DealBriefingController extends Controller
{
    /**
     * Generate AI-powered deal briefing
     * 
     * @param Request $request
     * @param int $dealId
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request, int $dealId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Please login first',
            ], 401);
        }

        if (!$user->hubspot_refreshToken) {
            return response()->json([
                'error' => 'HubSpot not connected',
                'message' => 'Please connect HubSpot first via /hubspot/login',
            ], 400);
        }

        try {
            $deal = Deal::findOrFail($dealId);

            // Gather deal data
            $briefingService = app(DealBriefingService::class);
            $dealData = $briefingService->gatherDealData($deal, $user);

            // Generate briefing
            $aiService = app(AIService::class);
            $briefing = $aiService->generateDealBriefing($dealData);

            return response()->json([
                'success' => true,
                'deal_id' => $deal->id,
                'deal_name' => $deal->name,
                'briefing' => $briefing,
                'data_summary' => [
                    'activities_count' => count($dealData['recent_activities']),
                    'engagements_count' => count($dealData['engagements']['calls']) + 
                                         count($dealData['engagements']['emails']) + 
                                         count($dealData['engagements']['meetings']),
                    'warnings_count' => count($dealData['warnings']),
                ],
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Deal not found',
                'message' => "Deal with ID {$dealId} does not exist",
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Deal briefing generation error', [
                'deal_id' => $dealId,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ]);

            return response()->json([
                'error' => 'Failed to generate briefing',
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while generating the briefing. Please try again later.',
            ], 500);
        }
    }
}
