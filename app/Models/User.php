<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravolt\Avatar\Avatar;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'avatar',
        'google_id',
        'role_id',
        'hubspot_id',
        'hubspot_token',
        'hubspot_refreshToken',
        //'organization_id',
        'google_token',
        'google_refresh_token',
        'google_name',
        'google_calendar_id',
        'timezone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
        'hubspot_id',
        'hubspot_token',
        'hubspot_refreshToken'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'google_token' => 'json',
        'google_refresh_token' => 'json',
    ];

    public function getAvatar(): string {
        if (!empty($this->avatar) && Storage::disk('public')->exists('avatars/'.$this->avatar))
            return Storage::disk('public')->url('avatars/'.$this->avatar);
        else
            return asset('images/avatar.jpg');
    }

    public function createAvatar(){
        $filename = uniqid() . '-' . now()->timestamp . '.png';
        $avatar = new Avatar(config('laravolt.avatar') );
        $avatar->create($this->name)->save(Storage::disk('public')->path('avatars'). '/'. $filename);
        $this->avatar = $filename;
        $this->save();
    }

    protected static function booted(){
        /*
        self::creating(function($user){
            $var = explode('@', $user->email);
            $domain_name =  array_pop($var);
            $organization = Organization::where('name', $domain_name )->first();
            if ($organization) {
              if(!$user->role_id) $user->role_id=2;
            }else{
                $organization = Organization::create([
                    'name'=>$domain_name,
                    'currency'=> 'EUR'
                ]);
                if(!$user->role_id) $user->role_id=3;
            }
            $user->organization_id = $organization->id;
        });*/

        static::created(function($user){
            $user->createAvatar();
        });

        static::deleting(function(User $user) {
            Storage::disk('public')->delete('avatars/'. $user->avatar);
        });
    }

    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating() : bool
    {
        return Session::has('impersonate');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function isAdmin() : bool
    {
        return $this->role->id == 1;
    }

    public function organizations()
    {
        return $this->belongsToMany('App\Models\Organization');
    }

    public function organization()
    {
        // Return cached organization from session if available
        if (Session::has('organization')) {
            return Session::get('organization');
        }
        
        // Try to find organization by hubspot_portalId from session (if user logged in via HubSpot)
        $hubspot_portalId = session('hubspot_portalId');
        $organization = null;
        
        if ($hubspot_portalId) {
            $organization = $this->organizations()->where('hubspot_portalId', $hubspot_portalId)->first();
        }
        
        // If not found or hubspot_portalId not in session, fall back to first organization
        if (!$organization) {
            $organization = $this->organizations()->first();
        }
        
        // Cache in session for future requests
        if ($organization) {
            Session::put('organization', $organization);
            // Also set hubspot_portalId in session if it wasn't set but organization has it
            if (!$hubspot_portalId && $organization->hubspot_portalId) {
                Session::put('hubspot_portalId', $organization->hubspot_portalId);
            }
        }
        
        return $organization;
    }

    public function currency(){
        $organization = $this->organization();
        if( !Session::has('user_currency') && $organization )
            Session::put('user_currency', $organization->currency);
        return Session::get('user_currency', 'USD'); // Default to USD if no organization
        //return $this->organization()->first()->currency;
    }

    public function member(){
         $organization = $this->organization();
         if (!$organization) {
             // If no organization found, try to find member by email in any organization
             // This can happen if user hasn't selected an organization yet
             $member = Member::where('email', $this->email)->first();
             if ($member) {
                 // If member found, set the organization in session for future requests
                 $memberOrganization = $member->organization;
                 if ($memberOrganization) {
                     Session::put('organization', $memberOrganization);
                     if ($memberOrganization->hubspot_portalId) {
                         Session::put('hubspot_portalId', $memberOrganization->hubspot_portalId);
                     }
                 }
                 // Return query that will return this member
                 return Member::where('id', $member->id);
             }
             // Return empty query if no organization and no member found
             return Member::whereRaw('1 = 0');
         }
         return Member::where('email', $this->email)->where('organization_id', $organization->id);
    }


}
