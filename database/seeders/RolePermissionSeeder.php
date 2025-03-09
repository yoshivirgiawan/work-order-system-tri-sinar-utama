<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $superAdmin = Role::create(['name' => 'super admin']);
    $manager = Role::create(['name' => 'production manager']);
    $operator = Role::create(['name' => 'operator']);

    Permission::create(['group' => 'role', 'name' => 'create role']);
    Permission::create(['group' => 'role', 'name' => 'read role']);
    Permission::create(['group' => 'role', 'name' => 'update role']);
    Permission::create(['group' => 'role', 'name' => 'delete role']);

    Permission::create(['group' => 'user', 'name' => 'create user']);
    Permission::create(['group' => 'user', 'name' => 'read user']);
    Permission::create(['group' => 'user', 'name' => 'update user']);
    Permission::create(['group' => 'user', 'name' => 'delete user']);

    Permission::create(['group' => 'work order', 'name' => 'read work order']);
    Permission::create(['group' => 'work order', 'name' => 'create work order']);
    Permission::create(['group' => 'work order', 'name' => 'assign operator']);
    Permission::create(['group' => 'work order', 'name' => 'update work order']);
    Permission::create(['group' => 'work order', 'name' => 'update own work order']);
    Permission::create(['group' => 'work order', 'name' => 'delete work orders']);

    $superAdmin->givePermissionTo(Permission::all());
    $manager->givePermissionTo(['create work order', 'assign operator', 'update work order', 'read work order']);
    $operator->givePermissionTo(['update own work order', 'read work order']);
  }
}
