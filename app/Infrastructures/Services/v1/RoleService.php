<?php

namespace App\Infrastructures\Services\v1;

use Illuminate\Support\Str;
use App\Infrastructures\Repositories\RoleRepository;

class RoleService
{
  protected $roleRepository;

  public function __construct()
  {
    $this->roleRepository = new RoleRepository();
  }

  public function getAll()
  {
    return $this->roleRepository->all();
  }

  public function create($data)
  {
    $role = $this->roleRepository->create([
      'name' => Str::lower($data['name']),
    ]);

    foreach ($data['permissions'] as $permission) {
      $role->givePermissionTo($permission);
    }

    return $role;
  }

  public function getById($id)
  {
    return $this->roleRepository->findById($id);
  }

  public function updateById($id, $data)
  {
    $role = $this->roleRepository->updateById($id, [
      'name' => Str::lower($data['name']),
    ]);

    $role->syncPermissions($data['permissions']);

    return $role;
  }

  public function deleteById($id)
  {
    return $this->roleRepository->deleteById($id);
  }
}
