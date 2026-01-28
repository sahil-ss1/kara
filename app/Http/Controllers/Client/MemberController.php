<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Imports\HubspotOwners;
use App\Models\Member;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.member.index');
    }

    public function datatable(){
        $organization = Auth::user()->organization();
        if (!$organization) {
            return DataTables::of(collect([]))->make();
        }
        $members = Member::select('members.*')->where('organization_id', $organization->id); //use select to allow datatables to make queries
        if ( isset($_POST['active']) ) $members = $members->where('active', $_POST['active']);
        if ( isset($_POST['teams']) ) $members = $members->join('member_team', 'members.id','=', 'member_team.member_id')->whereIn('team_id',$_POST['teams'] );
        return DataTables::of($members)
                         ->addIndexColumn() //DT_RowID
                         ->setRowId('id')
                            //->rawColumns(['active'])
                         ->make();
    }

    public function get_members(){
        $ret=[];
        $organization = Auth::user()->organization();
        if (!$organization) {
            die(json_encode([]));
        }
        $members = Member::select('members.*')->where('organization_id', $organization->id)->where('active',1);
        $teams = $_POST['teams'] ?? [];
        $members = $members->join('member_team', 'members.id','=', 'member_team.member_id')->whereIn('team_id',$teams);
        $members = $members->get();
        foreach ($members as $member){
            $ret[$member->id] = $member->lastName.' '.$member->firstName;
        }

        die(json_encode($ret));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Member $member)
    {
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][$member->id];

        $member->update( $input );
        $data = array( 'data' => array( $member ) );

        die(json_encode($data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        //
    }

    public function sync_hubspot_owners(){

        $user = Auth::user();
            
            // Check if user has HubSpot refresh token
            if (!$user->hubspot_refreshToken) {
                return response()->json([
                    'success' => false,
                    'error' => 'HubSpot not connected',
                    'message' => 'Please connect your HubSpot account first. Go to Settings > HubSpot Sync to authenticate.'
                ], 400);
            }
            
            $organization = $user->organization();
            if (!$organization) {
                return response()->json([
                    'success' => false,
                    'error' => 'No organization found',
                    'message' => 'Please ensure you are assigned to an organization.'
                ], 400);
            }

        HubspotOwners::sync_with_hubspot(null, Auth::user(),$organization->id);
    }
}
