<?php

namespace App\Infrastructures\Services\v1;

use App\Infrastructures\Repositories\PermissionRepository;

class PermissionService
{
  protected $permissionRepository;

  public function __construct()
  {
    $this->permissionRepository = new PermissionRepository();
  }

  public function getAll()
  {
    return $this->permissionRepository->all();
  }

  public function getById($id)
  {
    return $this->permissionRepository->findById($id);
  }

  public function getGroupedPermissions()
  {
    return $this->permissionRepository->getGroupedPermissions();
  }

  public function deleteById($id)
  {
    return $this->permissionRepository->deleteById($id);
  }
}
