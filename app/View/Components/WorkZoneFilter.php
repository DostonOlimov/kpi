<?php

namespace App\View\Components;

use App\Models\WorkZone;
use Illuminate\View\Component;

class WorkZoneFilter extends Component
{
    public $parentWorkZones;
    public $childWorkZones;
    public $selectedWorkZoneId;
    public $selectedChildWorkZoneId;
    public $actionUrl;
    public $showLabel;
    public $autoSubmit;

    /**
     * Create a new component instance.
     *
     * @param string|null $actionUrl - The form action URL
     * @param bool $showLabel - Whether to show the label
     * @param bool $autoSubmit - Whether to auto-submit on selection
     */
    public function __construct(
        $actionUrl = null,
        $showLabel = true,
        $autoSubmit = true
    ) {
        $this->actionUrl = $actionUrl ?? url()->current();
        $this->showLabel = $showLabel;
        $this->autoSubmit = $autoSubmit;
        
        // Get current request values
        $request = request();
        $defaultWorkZoneId = get_default_parent_work_zone_id();
        $this->selectedWorkZoneId = $request->input('work_zone_id', $defaultWorkZoneId); // Default to $defaultWorkZoneId
        $this->selectedChildWorkZoneId = $request->input('child_work_zone_id');
        
        // Load work zones
        $this->parentWorkZones = WorkZone::whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
            
        $this->childWorkZones = WorkZone::where('parent_id', $this->selectedWorkZoneId)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.work-zone-filter');
    }
}
