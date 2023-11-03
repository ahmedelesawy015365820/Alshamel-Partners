<?php

namespace Database\Seeders;

use App\Models\DocumentStatuse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentStatuseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        DocumentStatuse::insert([
            [
                "name" => "قيد الانتظار",
                "name_e" => "pending",
                "is_default" => 1,
                "is_active" => 1,
            ],
            [
                "name" => "موافقة",
                "name_e" => "approved",
                "is_default" => 0,
                "is_active" => 1,
            ],
            [
                "name" => "مراجغة",
                "name_e" => "recheck",
                "is_default" => 0,
                "is_active" => 1,
            ],
            [
                "name" => "مرفوضة",
                "name_e" => "refused",
                "is_default" => 0,
                "is_active"=>1,
            ],
            [
                "name" => "معتمد تلقائيًا",
                "name_e" => "Auto Approved",
                "is_default" => 0,
                "is_active" => 1,
            ]

        ]);
    }
}
