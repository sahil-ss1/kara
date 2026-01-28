<?php

namespace App\Livewire\Grids;

use Livewire\Component;

class ActivityGrid extends Component
{
    public $deal;

    public function render()
    {
        return view('livewire.grids.activity-grid');
    }
}
