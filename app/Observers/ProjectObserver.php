<?php

namespace App\Observers;

use App\Events\DashboardUpdatedEvent;
use App\Models\Project;

class ProjectObserver
{
    public function updated(Project $project)
    {
        event(new DashboardUpdatedEvent());
    }
}
