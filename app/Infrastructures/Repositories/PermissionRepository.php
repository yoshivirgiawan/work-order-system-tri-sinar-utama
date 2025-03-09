<?php

namespace App\Infrastructures\Repositories;

use App\Infrastructures\Core\Repository;
use Spatie\Permission\Models\Permission;

class PermissionRepository extends Repository
{
  protected function model(): string
  {
    return Permission::class;
  }

  public function getGroupedPermissions()
  {
    return Permission::get()->groupBy('group');
  }
}
