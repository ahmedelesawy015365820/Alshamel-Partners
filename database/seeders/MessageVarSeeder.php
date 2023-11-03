<?php

namespace Database\Seeders;

use App\Models\MessageVar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageVarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_messages_var')->delete();

        $attributes = [
           [
               'id' => 1,
               'name' => 'اسم الموظف',
               'var' => '{name}',
           ],
           [
               'id' => 2,
               'name' => 'اسم اليوم',
               'var' => '{day}',
           ],
           [
               'id' => 3,
               'name' => 'التاريخ',
               'var' => '{date}',
           ],
           [
               'id' => 4,
               'name' => 'شفت١',
               'var' => '{shift1}',
           ],
           [
               'id' => 5,
               'name' => 'شفت٢',
               'var' => '{shift2}',
           ],
           [
               'id' => 6,
               'name' => 'عدد دقائق',
               'var' => '{num_minutes}',
           ],


        ];

        foreach ($attributes as $attribute) {
            MessageVar::create($attribute);
        }
    }
}
