<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        $data = $this->data;
        $from_user_id = $data['from'];
        $from_user = User::find($from_user_id);
        return [
            'id' => $this->id,
            //'type' => $this->type,
            'created_at' => $this->created_at->diffForHumans(),
            'read_at' => $this->read_at,
            'from' => $from_user ? $from_user->name : 'Unknown',
            'from_avatar' => $from_user ? $from_user->getAvatar() : asset('images/avatar.jpg'),
            'title' => $data['title'],
            'message' => $data['message'],
        ];
    }
}
