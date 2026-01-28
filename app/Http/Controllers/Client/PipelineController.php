<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Imports\HubspotPipelines;
use App\Models\Pipeline;
use Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class PipelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('client.pipeline.index');
    }

    public function datatable(){
        //ray()->showQueries();
        $organization = Auth::user()->organization();
        if (!$organization) {
            return DataTables::of(collect([]))->make();
        }
        $pipelines = Pipeline::select()->where('organization_id', $organization->id); //use select to allow datatables to make queries
        return DataTables::of($pipelines)
                         ->addIndexColumn() //DT_RowID
                         ->setRowId('id')
                        //->rawColumns(['active'])
                         ->make();
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
     * @param  \App\Models\Pipeline  $pipeline
     * @return \Illuminate\Http\Response
     */
    public function show(Pipeline $pipeline)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pipeline  $pipeline
     * @return \Illuminate\Http\Response
     */
    public function edit(Pipeline $pipeline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pipeline  $pipeline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pipeline $pipeline)
    {
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][$pipeline->id];

        $pipeline->update( $input );
        $data = array( 'data' => array( $pipeline ) );

        die(json_encode($data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pipeline  $pipeline
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pipeline $pipeline)
    {
        //
    }

    public function sync_hubspot_pipelines(){
        
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

        HubspotPipelines::sync_with_hubspot(null, Auth::user(),$organization->id);
    }
}
