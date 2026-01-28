<?php

namespace App\Livewire;

use Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ShowNotifications extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.show-notifications',[
            'notifications' => Auth::user()->notifications()->paginate(10)
        ]);
    }
}
