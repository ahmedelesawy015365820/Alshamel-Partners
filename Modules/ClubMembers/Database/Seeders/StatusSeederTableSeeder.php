<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class StatusSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $data = [
            [
                'name' => "طلب عضوية",
                'name_e' => "Membership request",
            ],
            [
                'name' => "مسدد الأشتراك",
                'name_e' => "Paid subscription",
            ],
            [
                'name' => "علبة اشتراكات",
                'name_e' => "Subscription box",
            ],
            [
                'name' => "لائحة الاعضاء",
                'name_e' => "Members list",
            ],
            [
                'name' => "شطب بناء علي طلبه",
                'name_e' => "Deleted at his request",
            ],
            [
                'name' => "شطب لعدم السداد",
                'name_e' => "Deleted for non-payment",
            ],
            [
                'name' => "مسدد ليس له حق الحضور",
                'name_e' => "Paid does not have the right to attend",
            ],
            [
                'name' => "شطب للوفاة",
                'name_e' => "Deleted for death",
            ],
            [
                'name' => "تم تحديث البيانات",
                'name_e' => "Data has been updated",
            ],
            [
                'name' => "شطب بموجب القرار الوزاري",
                'name_e' => "Deleted by ministerial decision",
            ],
            [
                'name' => "مسدد اول مارس",
                'name_e' => "Paid on March 1",
            ],
            [
                'name' => "شطب بموجب الهيئه",
                'name_e' => "Deleted by the authority",
            ]
        ];

        foreach ($data as $item) {
            \Modules\ClubMembers\Entities\Status::create($item);
        }
    }
}
