<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $data = [
      [
        'name' => 'Super Admin',
        'email' => 'superadmin@example.com',
        'password' => bcrypt('password2001'),
        'role' => 'super admin',
        'email_verified_at' => now()
      ],
      [
        'name' => 'Production Manager',
        'email' => 'productionmanager@example.com',
        'password' => bcrypt('password2001'),
        'role' => 'production manager',
        'email_verified_at' => now()
      ],
      [
        'name' => 'Operator',
        'email' => 'operator@example.com',
        'password' => bcrypt('password2001'),
        'role' => 'operator',
        'email_verified_at' => now()
      ]
    ];

    foreach ($data as $user) {
      $user = User::create($user);
      $user->assignRole($user->role);
    }
  }
}
