<?php

namespace App\Infrastructures\Services\v1;

use Illuminate\Support\Facades\Hash;
use App\Infrastructures\Repositories\UserRepository;

class UserService
{
  protected $userRepository;

  public function __construct()
  {
    $this->userRepository = new UserRepository();
  }

  public function getAll()
  {
    return $this->userRepository->all();
  }

  public function getById($id)
  {
    return $this->userRepository->findById($id);
  }

  public function getOperators()
  {
    return $this->userRepository->getOperators();
  }

  public function create(array $dataRequest)
  {
    $dataRequest['password'] = Hash::make($dataRequest['password']);
    $user = $this->userRepository->create($dataRequest);

    $user->assignRole($dataRequest['role']);

    return $user;
  }

  public function updateById($id, array $dataRequest)
  {
    if ($dataRequest['password'] != null) {
      $dataRequest['password'] = Hash::make($dataRequest['password']);
    } else {
      unset($dataRequest['password']);
    }

    $user = $this->userRepository->findById($id);
    $user->assignRole($dataRequest['role']);

    return $this->userRepository->updateById($id, $dataRequest);
  }

  public function deleteById($id)
  {
    return $this->userRepository->deleteById($id);
  }
}
