<?php

namespace App\Infrastructures\Repositories;

use Carbon\Carbon;
use App\Models\WorkOrder;
use App\Infrastructures\Core\Repository;

class WorkOrderRepository extends Repository
{
  protected function model(): string
  {
    return WorkOrder::class;
  }

  public function getLastOrder()
  {
    return WorkOrder::whereDate('created_at', Carbon::today())->count();
  }
}
