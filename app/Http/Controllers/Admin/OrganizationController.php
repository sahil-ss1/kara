<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Auth;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\DataTables;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.organization.index');
    }

    public function datatable(){
        //ray()->showQueries();
        $organizations = Organization::select()->with('users'); //use select to allow datatables to make queries
        return DataTables::of($organizations)
                         ->addIndexColumn() //DT_RowID
                         ->setRowId('id')
                         ->addColumn('users_count',function($row){
                            return $row->users()->count();
                         })
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
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][0];

        $validator = Validator::make($input,[
            'name' => 'required|max:255|unique:organizations',
        ]);

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            $errors = [];
            foreach ($messages as $field=>$message){
                $errors[] = array( 'name' => $field, 'status' => $message[0] );
            }
            $data = array('fieldErrors'=> $errors);
        }else{

            $organization = Organization::create([
                'name' => $input['name'],
                'currency'=> 'EUR'
            ]);

            $data = array('data'=> array($organization));
        }

        die(json_encode($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        $this->authorize('update', $organization);

        return view('organization.edit')->with([
            'organization' => $organization,
            'currencies' => currency()->getActiveCurrencies()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization)
    {
        $validator = $request->validate([
            'name' => 'required|max:255',
        ]);

        $input = $request->all();
        $organization->update( $input );

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        die(json_encode(array()));
    }
}
