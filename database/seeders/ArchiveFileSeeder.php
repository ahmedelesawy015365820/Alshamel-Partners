<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ArchiveFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
            \Modules\Archiving\Entities\ArchiveFile::create([
                'data_type_id' => 1,
                'data_type_value' => json_encode([
                    'name' => 'name',
                    'name_e' => 'name_e',
                ]),
            ]);
        }
    }
}
