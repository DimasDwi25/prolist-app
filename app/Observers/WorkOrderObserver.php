<?php

namespace App\Observers;

use App\Events\DashboardUpdatedEvent;
use App\Models\WorkOrder;

class WorkOrderObserver
{
    public function created(WorkOrder $workOrder)
    {
        event(new DashboardUpdatedEvent());
    }

    public function updated(WorkOrder $workOrder)
    {
        event(new DashboardUpdatedEvent());
    }
}
