<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Notifications\SimpleUserNotification;
use Auth;
use Illuminate\Http\Request;
use App\Helpers\SimpleNotificationMessage;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsController extends Controller
{
    public function index(){
        return view('notifications.index');
    }

    public function show($notification){
        $notification = DatabaseNotification::find($notification);
        return view('notifications.index')->with([
            'notification'=>$notification
        ]);
    }

    public function details($notification){
        $notification = DatabaseNotification::find($notification);
        $from = User::find( $notification->data['from'] );
        return view('notifications.show')->with([
            'from' => $from,
            'notification'=>$notification
        ]);
    }

    public function create(User $user){
        return view('admin.user.notify')->with([
            'user' => $user
        ]);
    }

    public function send(Request $request, User $user){
        $input = $request->all();

        $simpleNotificationMessage = new SimpleNotificationMessage();
        $simpleNotificationMessage->from_user = Auth::user()->id;
        $simpleNotificationMessage->title = $input['title'];
        $simpleNotificationMessage->message = $input['message'];

        $user->notify(new SimpleUserNotification($simpleNotificationMessage));

        die(0);
    }

    public function notifications()
    {
        die( NotificationResource::collection(Auth::user()->notifications()->paginate(10))->toJson() );
    }
}
