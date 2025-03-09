<?php

namespace App\Infrastructures\Repositories;

use App\Infrastructures\Core\Repository;
use App\Models\User;

class UserRepository extends Repository
{
  protected function model(): string
  {
    return User::class;
  }

  public function getUserByEmail($email)
  {
    return User::where('email', '=', $email)->firstOrFail();
  }

  public function getOperators()
  {
    return User::where('role', '=', 'operator')->get();
  }
}
