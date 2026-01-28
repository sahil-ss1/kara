<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $language_folder = base_path().'/lang';
        $files = File::glob("$language_folder/*.json");
        $translations=[];
        foreach ($files as $file) {
            //echo "$filename size " . filesize($filename) . "\n";
            $translations[] = File::name($file);
        }
        return view('admin.translations.index')->with([
            'translations' =>  $translations
        ]);
    }

    public function datatable(Request $request){
        try {
            $input = $request->all();
            
            // Validate language parameter
            if (!isset($input['language']) || empty($input['language'])) {
                return response()->json([
                    'error' => 'Language parameter is required'
                ], 400);
            }
            
            $language = $input['language'];
            
            // Sanitize language parameter to prevent directory traversal
            $language = basename($language);
            
            $language_folder = base_path().'/lang';
            $file_path = $language_folder.'/'.$language.'.json';
            
            // Check if file exists - return empty array if not found (allows DataTable to initialize)
            if (!file_exists($file_path)) {
                return response()->json([]);
            }
            
            // Read and decode JSON file
            $file_content = file_get_contents($file_path);
            if ($file_content === false) {
                return response()->json([
                    'error' => 'Failed to read translation file'
                ], 500);
            }
            
            $file = json_decode($file_content, true);
            
            // Check if JSON decode was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'error' => 'Invalid JSON in translation file',
                    'json_error' => json_last_error_msg()
                ], 500);
            }
            
            // Handle null or non-array data
            if (!is_array($file)) {
                $file = [];
            }
            
            $return = [];
            foreach ($file as $key=>$value) {
                $return[] = array(
                    'key'=>$key,
                    'string'=>$value
                );
            }
            
            return response()->json($return);
            
        } catch (\Exception $e) {
            \Log::error('Translation datatable error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'An error occurred while loading translations',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $input = $request->all();
    }

    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();
            
            if (!isset($input['language']) || empty($input['language'])) {
                return response()->json(['error' => 'Language parameter is required'], 400);
            }
            
            $language = basename($input['language']);
            $language_folder = base_path().'/resources/lang';
            $file_path = $language_folder.'/'.$language.'.json';
            
            if (!file_exists($file_path)) {
                return response()->json(['error' => 'Translation file not found'], 404);
            }
            
            $file = json_decode(file_get_contents($file_path), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Invalid JSON in translation file'], 500);
            }
            
            if (!isset($input['data'][$id]['string'])) {
                return response()->json(['error' => 'String value is required'], 400);
            }
            
            $file[$id] = $input['data'][$id]['string'];
            
            if (file_put_contents($file_path, json_encode($file, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                return response()->json(['error' => 'Failed to save translation file'], 500);
            }
            
            return response()->json([
                'data' => [[
                    'key'=>$id,
                    'string'=>$file[$id]
                ]]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Translation update error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while updating translation',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $input = $request->all();
            
            if (!isset($input['language']) || empty($input['language'])) {
                return response()->json(['error' => 'Language parameter is required'], 400);
            }
            
            $language = basename($input['language']);
            $language_folder = base_path().'/resources/lang';
            $file_path = $language_folder.'/'.$language.'.json';
            
            if (!file_exists($file_path)) {
                return response()->json(['error' => 'Translation file not found'], 404);
            }
            
            $file = json_decode(file_get_contents($file_path), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['error' => 'Invalid JSON in translation file'], 500);
            }
            
            unset($file[$id]);
            
            if (file_put_contents($file_path, json_encode($file, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
                return response()->json(['error' => 'Failed to save translation file'], 500);
            }
            
            return response()->json([]);
            
        } catch (\Exception $e) {
            \Log::error('Translation destroy error: ' . $e->getMessage());
            return response()->json([
                'error' => 'An error occurred while deleting translation',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
