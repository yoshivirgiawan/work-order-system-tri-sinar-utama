<?php

namespace App\Infrastructures\Repositories;

use Spatie\Permission\Models\Role;
use App\Infrastructures\Core\Repository;

class RoleRepository extends Repository
{
  protected function model(): string
  {
    return Role::class;
  }
}
