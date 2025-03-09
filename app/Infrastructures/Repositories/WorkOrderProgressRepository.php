<?php

namespace App\Infrastructures\Repositories;

use App\Models\WorkOrderProgress;
use App\Infrastructures\Core\Repository;

class WorkOrderProgressRepository extends Repository
{
  protected function model(): string
  {
    return WorkOrderProgress::class;
  }
}
