<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    /**
     * Health check endpoint for monitoring
     * 
     * @return JsonResponse
     */
    public function check(): JsonResponse
    {
        $checks = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'checks' => []
        ];

        // Database connectivity check
        try {
            DB::connection()->getPdo();
            $checks['checks']['database'] = [
                'status' => 'ok',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            $checks['status'] = 'error';
            $checks['checks']['database'] = [
                'status' => 'error',
                'message' => 'Database connection failed'
            ];
        }

        // Cache connectivity check
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 10);
            $value = Cache::get($testKey);
            Cache::forget($testKey);
            
            if ($value === 'test') {
                $checks['checks']['cache'] = [
                    'status' => 'ok',
                    'message' => 'Cache is working'
                ];
            } else {
                $checks['status'] = 'error';
                $checks['checks']['cache'] = [
                    'status' => 'error',
                    'message' => 'Cache read/write failed'
                ];
            }
        } catch (\Exception $e) {
            $checks['status'] = 'error';
            $checks['checks']['cache'] = [
                'status' => 'error',
                'message' => 'Cache connection failed'
            ];
        }

        // Storage writability check
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::disk('local')->put($testFile, 'test');
            $exists = Storage::disk('local')->exists($testFile);
            Storage::disk('local')->delete($testFile);
            
            if ($exists) {
                $checks['checks']['storage'] = [
                    'status' => 'ok',
                    'message' => 'Storage is writable'
                ];
            } else {
                $checks['status'] = 'error';
                $checks['checks']['storage'] = [
                    'status' => 'error',
                    'message' => 'Storage write failed'
                ];
            }
        } catch (\Exception $e) {
            $checks['status'] = 'error';
            $checks['checks']['storage'] = [
                'status' => 'error',
                'message' => 'Storage check failed'
            ];
        }

        $statusCode = $checks['status'] === 'ok' ? 200 : 503;

        return response()->json($checks, $statusCode);
    }
}
