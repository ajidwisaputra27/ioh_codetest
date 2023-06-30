<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    User::create([
      'name'     => 'Aji Dwi Saputra',
      'email'    => 'ajidwisaputra067@gmail.com',
      'password' => '12345678',
    ]);
  }
}
