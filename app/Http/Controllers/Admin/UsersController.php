<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GoogleCalendars;
use App\Models\Role;
use App\Models\User;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Validator;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all()->pluck('id','name');
        return view('admin.user.index')->with([
            'roles'=> $roles
        ]);
    }

    public function datatable(){
        //ray()->showQueries();
        $users = User::select()->with(['role','organizations']); //use select to allow datatables to make queries
        return DataTables::of($users)
                ->addIndexColumn() //DT_RowID
                ->setRowId('id')
                ->addColumn('avatar',function($row){
                    return $row->getAvatar();
                })
                ->addColumn('role_name',function($row){
                    return $row->role->name;
                })
                ->addColumn('organization_name',function($row){
                    return $row->organizations()->pluck('name')->implode(',');
                })
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
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][0];

        $validator = Validator::make($input,[ //$validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'same:confirm-password',
            'role_id' => 'required'
       ]);

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            $errors = [];
            foreach ($messages as $field=>$message){
                $errors[] = array( 'name' => $field, 'status' => $message[0] );
            }
            $data = array('fieldErrors'=> $errors);
        }else{

            $user = User::create([
                'name' => $input['name'],
                'email' =>  $input['email'],
                'password' => !empty($input['password'])?Hash::make($input['password']):Hash::make('123456789'),
                'active' => $input['active'],
                'role_id' => $input['role_id'],
            ]);

            $data = array('data'=> array($user));
        }


        die(json_encode($data));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('update', $user);

        try {
            $calendars = GoogleCalendars::get_calendars();
        }catch (\Exception $e) {
            $calendars = [];
        }

        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        return view('user.show')->with([
            'user'=> $user,
            'calendars' => $calendars,
            'tzlist' => $tzlist
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();
        if ( array_key_exists('data', $input) ) $input = $input['data'][$user->id];

        //if ( empty($input['password']) ) unset( $input['current-password'] );
        $validator = Validator::make($input,[ //$validated = $request->validate([
            'name' => 'required|max:255',
            //'email' => 'required|email|unique:users',
            //'current-password' => 'current_password',
            'password' => 'same:confirm-password'
        ]);
        if ( empty($input['password']) ) unset( $input['password'] ); else $input['password'] =  Hash::make($input['password']);

        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            $errors = [];
            foreach ($messages as $field=>$message){
                $errors[] = array( 'name' => $field, 'status' => $message[0] );
            }
            $data = array('fieldErrors'=> $errors);
        }else {
            $user->update( $input );
            $data = array( 'data' => array( $user ) );
        }

        die(json_encode($data));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
       $user->delete();

       die(json_encode(array()));
    }

    public function impersonate($id)
    {
        $user = User::find($id);

        // Guard against administrator impersonate
        //if(! $user->isAdministrator())
        //{
        if (Auth::user()->id !== $user->id) {
            Auth::user()->setImpersonating($user->id);
            flash($user->name.' Impersonation Started', 'success');
        }
        //}
        //else
        //{
        //    flash()->error('Impersonate disabled for this user.');
        //}

        return redirect()->back();
    }

    public function stopImpersonate()
    {
        Auth::user()->stopImpersonating();

        flash('Welcome back!', 'success');

        return redirect()->back();
    }
}
