<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatuseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Admin report
        Status::insert([
            [
                "name" => "قيد الانتظار",
                "name_e" => "pending",
                "module_type" => "bordRent",
                "color" => "bg-warning text-white"
            ],
            [
                "name" => "في تَقَدم",
                "name_e" => "in_progress",
                "module_type" => "bordRent",
                "color" => "bg-info text-white"
            ],
            [
                "name" => "مكتمل",
                "name_e" => "completed",
                "module_type" => "bordRent",
                "color" => "bg-success text-white"
            ],
            [
                "name" => "مرفوض",
                "name_e" => "rejected",
                "module_type" => "bordRent",
                "color" => "bg-dark text-white"
            ],
            [
                "name" => "انتظار الموافقة",
                "name_e" => "waiting for approval",
                "module_type" => "bordRent",
                "color" => "bg-secondary text-white"
            ],
            [
                "name" => "إلغاء",
                "name_e" => "canceled",
                "module_type" => "bordRent",
                "color" => "bg-danger text-white"
            ],
            [
                "name" => "متاحة",
                "name_e" => "Available",
                "module_type" => "panel",
                "color" => "bg-success text-white"
            ],
            [
                "name" => "مؤجرة",
                "name_e" => "Sold",
                "module_type" => "panel",
                "color" => "bg-danger text-white"
            ],
            [
                "name" => "محجوزة",
                "name_e" => "Reserved",
                "module_type" => "panel",
                "color" => "bg-warning text-white"
            ],
            [
                "name" => "فى مجموعة",
                "name_e" => "In Strip",
                "module_type" => "panel",
                "color" => "bg-primary text-white"
            ],
            [
                "name" => "فى مجموعة",
                "name_e" => "In Strip",
                "module_type" => "panel",
                "color" => "bg-primary text-white"
            ],

            [
                "name" => "فارغة",
                "name_e" => "Available",
                "module_type" => "booking",
                "color" => "bg-success text-white"
            ],
            [
                'name' => 'تحت الصيانه',
                'name_e' => 'under maintenance',
                "module_type" => "booking",
                "color" => "bg-warning text-white"
            ],
            [
                "name" => "محجوزة",
                "name_e" => "Reserved",
                "module_type" => "booking",
                "color" => "bg-danger text-white"
            ],
            [
                "name" => "مؤكد الحجز",
                "name_e" => "Reservation confirmed",
                "module_type" => "booking",
                "color" => "bg-primary text-white"
            ],
            [
                "name" => "مشغولة",
                "name_e" => "Occupied",
                "module_type" => "booking",
                "color" => "bg-dark text-white"
            ],
        ]);
    }
}
