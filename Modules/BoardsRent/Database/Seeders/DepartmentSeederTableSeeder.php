<?php

namespace Modules\BoardsRent\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DepartmentSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $departments = [
            'Sales department',
            'Reservation department',
            'Installation Department',
            'Printing department',
            'Accountant',
        ];

        foreach ($departments as $department) {
            \Modules\BoardsRent\Entities\Department::create([
                'name' => $department,
                'name_e' => $department,
            ]);
        }

        // $this->call("OthersTableSeeder");
    }
}
