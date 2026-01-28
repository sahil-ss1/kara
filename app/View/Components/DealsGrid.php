<?php

namespace App\View\Components;

use Auth;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DealsGrid extends Component
{
    public $table_id;
    /**
     * Create a new component instance.
     */
    public function __construct($tableid)
    {
        $this->table_id = $tableid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.deals-grid')->with([
            'currency' => currency()->find(Auth::user()->currency())
        ]);
    }
}
