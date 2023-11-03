<?php

namespace Database\Seeders;

use App\Models\DataType;
use Illuminate\Database\Seeder;

class DataTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DataType::query()->insert(
            [
                [
                    'name' => 'نص', 'name_e' => 'TEXT',
                    'placeholder' => 'اسم النص بالعربي',
                    'placeholder_e' => 'arabic text name',
                    'length' => '20'
                ],
                ['name' => 'نص طويل', 'name_e' => 'LONGTEXT',
                    'placeholder' => 'نص طويل',
                    'placeholder_e' => 'long text',
                    'length' => '200'
                ],
                ['name' => 'من', 'name_e' => 'BOOLEAN',
                    'placeholder' => 'حاله البيانات المدخله نشطه او غير نشطه',
                    'placeholder_e' => 'The status of the entered data is active or inactive',
                    'length' => '10'
                ],
                ['name' => 'رقم صحيح', 'name_e' => 'INTEGER',
                    'placeholder' => 'بيانات المدخله لا تقبل غير ارقام صحيحه',
                    'placeholder_e' => 'The entered data does not accept other than valid numbers',
                    'length' => '10'
                ],
                ['name' => 'رقم عشر صغير', 'name_e' => 'FLOAT',
                    'placeholder' => 'بيانات المدخله لا تقبل غير ارقام عشريه (8.2)',
                    'placeholder_e' => 'The input data does not accept other than decimal numbers (8.2)',
                    'length' => '10'
        ],
                ['name' => 'رقم عشري كبير', 'name_e' => 'DOUBLE',
                    'placeholder' => 'بيانات المدخله لا تقبل غير ارقام عشريه (8.2)',
                    'placeholder_e' => 'The input data does not accept other than decimal numbers (8.2)',
                    'length' => '10'
                ],
                ['name' => 'تاريخ', 'name_e' => 'DATE',
                    'placeholder' => '',
                    'placeholder_e' => '',
                    'length' => ''
                ],
                ['name' => 'وقت', 'name_e' => 'TIME',
                    'placeholder' => '',
                    'placeholder_e' => '',
                    'length' => '10'
                ],
                ['name' => 'وقت و تاريخ', 'name_e' => 'TIMESTAMP',
                    'placeholder' => '',
                    'placeholder_e' => '',
                    'length' => '10'
                ],
                ['name' => 'فائمة', 'name_e' => 'ENUM (droplist)',
                    'placeholder' => 'لابد من الاختيار من القائمه',
                    'placeholder_e' => 'You must choose from the list',
                    'length' => ''
                ],
                ['name' => 'الجدول', 'name_e' => 'Lookup (table)',
                    'placeholder' => 'لابد من الاختيار من القائمه الجداول',
                    'placeholder_e' => 'You must choose from the list of tables',
                    'length' => ''
                ],
            ]
        );
    }
}
