<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name_e' => 'alshamel alaraby',
            'name' => 'الشامل العربي',
            'email' => 'super@alshamelalaraby.com',
            'is_active' => 'active',
            'password' => '12345678',
            'employee_id' => null,
            'type' => 'super_admin'
        ]);
    }
}
