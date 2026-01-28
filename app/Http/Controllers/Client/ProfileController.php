<?php

namespace App\Http\Controllers\Client;

use App\Helpers\Periods;
use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Member;
use App\Models\Team;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $member_id
     * @return \Illuminate\Http\Response
     */
    public function show($member_id)
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            abort(404);
        }
        $member = Member::where('id', $member_id)->where('organization_id', $organization->id)->first();
        if (!$member) abort(404);

        $teams = $member->teams()->pluck( 'teams.id' )->toArray();
        $members = Member::where('organization_id', $organization->id)->where('active', 1)->get();
        $periods = Periods::$type;
        $meeting = $member->openMeet()->first();

        return view('client.profile.index')->with([
            'member'=> $member,
            'teams' => $teams,
            'meeting' => $meeting,
            'periods' => $periods,
            'members' => $members
        ]);
    }
}
