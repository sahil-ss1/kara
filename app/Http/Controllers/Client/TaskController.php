<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Imports\HubspotTasks;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Member;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            return redirect()->back()->withErrors(['error' => 'No organization found']);
        }
        $deals = Deal::select('deals.*')
                       ->join('pipelines', 'deals.pipeline_id', '=', 'pipelines.id')
                       ->where('pipelines.organization_id', $organization->id)
                       ->where('pipelines.active', 1)
                       ->pluck('name', 'id');
        $members = Member::where('organization_id', $organization->id)
                         ->where('active',1)
                         ->get()
                         ->pluck('full_name', 'id');

        if( $request->has('owner') ) {
            $owner = $request->query('owner');
        }
        if( $request->has('deal') ) {
            $deal = $request->query('deal');
        }

        return view('client.task.create')->with([
            'deal'=>$deal ?? '',
            'deals' => $deals,
            'members' => $members,
            'owner' => $owner ?? ''
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'deal_id' => 'required',
            'hubspot_task_subject' => 'required',
            'hubspot_timestamp' => 'required'
        ]);

        $input = $request->all();
        /*
        $properties = [
            'hs_timestamp'     => Carbon::now()->addMonth()->format('Y-m-d'),//'2023-05-30T03 =>30 =>17.883Z',
            'hs_task_body'     => 'Send Proposal',
            'hubspot_owner_id' => '64492917',
            'hs_task_subject'  => 'Follow-up for Brian Buyer',
            'hs_task_status'   => 'NOT_STARTED', //COMPLETED or NOT_STARTED.
            'hs_task_priority' => 'HIGH', //HIGH, MEDIUM, or LOW.
            'hs_task_type'     => 'TODO', //CALL, EMAIL, or TODO.
        ];*/

        $properties = [];
        $properties['hs_task_body'] = $input['hubspot_task_body'];
        $properties['hs_task_subject'] = $input['hubspot_task_subject'];
        $date = Carbon::parse($input['hubspot_timestamp']);
        $properties['hs_timestamp'] = $date->format('Y-m-d');

        if (isset($input['member_id'])) {
            $member = Member::find($input['member_id']);
            $properties['hubspot_owner_id'] = $member->hubspot_id;
        }
        $properties['hs_task_type'] = $input['hubspot_task_type'];
        $properties['hs_task_priority'] = $input['hubspot_task_priority'];
        $properties['hs_task_status'] = 'NOT_STARTED';

        $organization = Auth::user()->organization();
        if (!$organization) {
            return response()->json(['error' => 'No organization found'], 400);
        }
        $deal = Deal::find($input['deal_id']);
        $hubspot_deal_id = $deal->hubspot_id;
        $task = HubspotTasks::createTask(null, Auth::user(), $organization->id, $hubspot_deal_id, $properties);

        die(0);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Activity  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $task)
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            return redirect()->back()->withErrors(['error' => 'No organization found']);
        }
        $deals = Deal::select('deals.*')
                     ->join('pipelines', 'deals.pipeline_id', '=', 'pipelines.id')
                     ->where('pipelines.organization_id', $organization->id)
                     ->where('pipelines.active', 1)
                     ->pluck('name', 'id');
        $members = Member::where('organization_id', $organization->id)
                         ->where('active',1)
                         ->get()
                         ->pluck('full_name', 'id');

        return view('client.task.edit')->with([
            'task'=>$task,
            'deals' => $deals,
            'members' => $members,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Activity   $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $task)
    {
        $validated = $request->validate([
            'deal_id' => 'required',
            'hubspot_task_subject' => 'required',
            'hubspot_timestamp' => 'required'
        ]);
        $input = $request->all();

        $properties = [];
        $properties['hs_task_body'] = $input['hubspot_task_body'];
        $properties['hs_task_subject'] = $input['hubspot_task_subject'];
        $date = Carbon::parse($input['hubspot_timestamp']);
        $properties['hs_timestamp'] = $date->format('Y-m-d');

        if (isset($input['member_id'])) {
            $member = Member::find($input['member_id']);
            $properties['hubspot_owner_id'] = $member->hubspot_id;
        }
        $properties['hs_task_type'] = $input['hubspot_task_type'];
        $properties['hs_task_priority'] = $input['hubspot_task_priority'];
        $properties['hs_task_status'] = $input['hubspot_status'];

        if (HubspotTasks::updateTask(null, Auth::user(), $task->hubspot_id, $properties)){
            $task->update($input);
        }

        die(0);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $task)
    {
        //$task->delete();
        HubspotTasks::removeTask(null, Auth::user(), $task->hubspot_id);
        die(0);
    }

}
