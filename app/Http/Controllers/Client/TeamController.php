<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function manage_teams()
    {
        return view('client.team.manage');
    }

    public function get_teams(){
        $organization = Auth::user()->organization();
        if (!$organization) {
            die(json_encode([]));
        }
        $teams = Team::where('organization_id', $organization->id)->pluck('name','id');
        die(json_encode($teams));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('client.team.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);

        $input = $request->all();

            $user = Auth::user();
            $organization = $user->organization();
            
            // If organization is null, try to get first organization from user's organizations
            if (!$organization) {
                $organization = $user->organizations()->first();
                
                // If still null, return detailed error
                if (!$organization) {
                    $hubspot_portalId = session('hubspot_portalId');
                    $organizationsCount = $user->organizations()->count();
                    
                    return response()->json([
                        'error' => 'No organization found',
                        'message' => 'Please ensure you are assigned to an organization.',
                        'debug' => [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'hubspot_portalId_in_session' => $hubspot_portalId,
                            'organizations_count' => $organizationsCount,
                            'organizations' => $user->organizations()->pluck('id', 'name')->toArray(),
                        ]
                    ], 400);
                }
                
                // Cache the organization in session for future requests
                Session::put('organization', $organization);
            }
            
            $team = Team::create([
            'name' => $input['name'],
                'organization_id' => $organization->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Team created successfully',
                'team' => [
                    'id' => $team->id,
                    'name' => $team->name,
                ]
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function edit(Team $team)
    {
        return view('client.team.edit')->with([
            'team'=>$team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);

        $input = $request->all();

        $team->update($input);

        return response()->json([
            'success' => true,
            'message' => 'Team updated successfully',
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
            ]
        ], 200);
    }

    public function add_members(Request $request, Team $team)
    {
        try {
        $input = $request->all();

            if (isset($input['members'])) {
                // Handle both array and comma-separated string
                if (is_array($input['members'])) {
                    $members = $input['members'];
                } else {
            $members = explode(',', $input['members']);
                }
                
                // Convert all values to integers and filter out empty/invalid values
                $members = array_filter(array_map(function($id) {
                    return (int) trim($id);
                }, $members), function($id) {
                    return $id > 0;
                });
                
                if (!empty($members)) {
            $team->members()->withTimestamps()->syncWithoutDetaching($members);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Members added to team successfully',
                        'added_count' => count($members)
                    ], 200);
                } else {
                    return response()->json([
                        'error' => 'No members provided',
                        'message' => 'Please select at least one member to add.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'error' => 'No members provided',
                    'message' => 'Please select at least one member to add.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Team $team)
    {
        try {
            $teamName = $team->name;
        $team->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Team "' . $teamName . '" deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete_member(Request $request, Team $team)
    {
        try {
        $input = $request->all();

            if (isset($input['member'])) {
                $memberId = $input['member'];
                $team->members()->detach($memberId);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Member removed from team successfully'
                ], 200);
            } else {
                return response()->json([
                    'error' => 'No member specified',
                    'message' => 'Please specify which member to remove.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
    public function goals(Request $request, Team $team){
        $goals = $team->goals()->get()->toJson(JSON_PRETTY_PRINT);
        die( $goals );
    }
    */
}
