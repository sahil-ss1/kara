<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Team;
use Auth;
use Illuminate\Http\Request;
//use Yajra\DataTables\DataTables;
use DataTables;


class OneToOneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            $teams = [];
        } else {
            $teams = Team::where('organization_id', $organization->id)->pluck('name','id');
        }
        return view('client.onotoone.index')->with([
            'teams'=> $teams
        ]);;
    }

    public function teamMembersDatatable($team){
        // Handle case when team ID is 0 or null (no team selected)
        if (!$team || $team == 0) {
            return DataTables::of(collect([]))
                             ->addIndexColumn()
                             ->setRowId('id')
                             ->make();
        }
        
        $team = Team::find($team);
        if (!$team){
            return DataTables::of(collect([]))
                             ->addIndexColumn()
                             ->setRowId('id')
                             ->make();
        }
        
        $members = $team->activeMembers()->whereNotIn('email', [Auth::user()->email])->select('members.*'); //To fix bug with yajra Datatables with pivot
        return DataTables::eloquent($members)
                         ->addIndexColumn() //DT_RowID
                         ->setRowId('id')
                         ->addColumn('meet', function(Member $row){
                            $meet = $row->openMeet()->first();
                            if ($meet) return $meet->id;
                            return null;
                         })
                        ->addColumn('google_event_id', function(Member $row){
                            $meet = $row->openMeet()->first();
                            if ($meet) return $meet->google_event_id;
                            return null;
                        })
                        ->make();
    }
}
