<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Translation;

class GeneralCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Customers
        Translation::insert([
            [
                "key" => "customer_create_form",
                "default_en" => "Add new customer",
                "default_ar" => "اضف زبون جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "customer_edit_form",
                "default_en" => "Edit customer form",
                "default_ar" => "نموذج تعديل الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_name_ar",
                "default_en" => "Customer name (arabic)",
                "default_ar" => "اسم الزبون (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_name_en",
                "default_en" => "Customer name (english)",
                "default_ar" => "اسم الزبون (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_phone",
                "default_en" => "Customer phone",
                "default_ar" => "هاتف الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_email",
                "default_en" => "Customer email",
                "default_ar" => "بريد الزبون الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_nationality",
                "default_en" => "Customer nationality",
                "default_ar" => "جنسية الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_national_id",
                "default_en" => "Customer national id",
                "default_ar" => "الرقم القومي للزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_contact_person",
                "default_en" => "Customer contact person",
                "default_ar" => "معلومات الزبون الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_contact_phones",
                "default_en" => "Customer contact phones",
                "default_ar" => "هواتف اتصال الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_whatsapp",
                "default_en" => "Customer whatsapp",
                "default_ar" => "رقم واتساب الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_code",
                "default_en" => "Customer code",
                "default_ar" => "كود الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "customer_passport_number",
                "default_en" => "Customer passport number",
                "default_ar" => "رقم جواز سفر الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
        ]);

        //Banks
        Translation::insert([
            [
                "key" => "bank_create_form",
                "default_en" => "Add new bank",
                "default_ar" => "اضف نوع بنك جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_edit_form",
                "default_en" => "Edit bank form",
                "default_ar" => " نموذج تعديل البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_name_ar",
                "default_en" => "Bank name (arabic)",
                "default_ar" => "اسم البنك (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_name_en",
                "default_en" => "Bank name (english)",
                "default_ar" => "اسم البنك (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "country",
                "default_en" => "Coutnry name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
            [
                "key" => "bank_swiftcode",
                "default_en" => "Bank swiftcode",
                "default_ar" => "سويفت كود البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
        ]);
        //Bank account
        Translation::insert([
            [
                "key" => "bank_account_create_form",
                "default_en" => "Add new bank account",
                "default_ar" => "اضف حساب بنكي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_account_edit_form",
                "default_en" => "Edit bank bank form",
                "default_ar" => " نموذج تعديل الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank",
                "default_en" => "Bank name",
                "default_ar" => "اسم البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_account_number",
                "default_en" => "Bank account number",
                "default_ar" => "رقم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_account_phone",
                "default_en" => "Bank account phone",
                "default_ar" => "هاتف الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_account_address",
                "default_en" => "Bank account address",
                "default_ar" => "عنوان الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_account_email",
                "default_en" => "Bank account email",
                "default_ar" => "البريد الالكتروني للحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],

            [
                "key" => "employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_account_rpcode",
                "default_en" => "Bank account rpcode",
                "default_ar" => "ار بي كود الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
        ]);

        //country
        Translation::insert([
            [
                "key" => "country_create_form",
                "default_en" => "Add new country",
                "default_ar" => "اضف دولة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_edit_form",
                "default_en" => "Edit country form",
                "default_ar" => "نموذج تعديل الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_name_ar",
                "default_en" => "Country name (arabic)",
                "default_ar" => "اسم الدولة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_name_en",
                "default_en" => "Country name (english)",
                "default_ar" => "اسم الدولة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_long_name_ar",
                "default_en" => "Country long name (arabic)",
                "default_ar" => "اسم الدولة طويل (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_long_name_en",
                "default_en" => "Country long name (english)",
                "default_ar" => "اسم الدولة طويل (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_phone_key",
                "default_en" => "Country phone key",
                "default_ar" => "كود هاتف الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_short_code",
                "default_en" => "Country short code",
                "default_ar" => "الكود المختصر للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_default",
                "default_en" => "Country default",
                "default_ar" => "الحالة الافتراضية للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_status",
                "default_en" => "Country status",
                "default_ar" => "حالة الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country_national_id",
                "default_en" => "Country national id",
                "default_ar" => "الرقم القومي للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
        ]);
        //governorate
        Translation::insert([
            [
                "key" => "governorate_create_form",
                "default_en" => "Add new governorate",
                "default_ar" => "اضف محافظة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate_edit_form",
                "default_en" => "Edit governorate form",
                "default_ar" => "نموذج تعديل المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate_name_ar",
                "default_en" => "Governorate name (arabic)",
                "default_ar" => "اسم المحافظة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate_name_en",
                "default_en" => "Governorate name (english)",
                "default_ar" => "اسم المحافظة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate_phone_key",
                "default_en" => "Governorate phone key",
                "default_ar" => "كود هاتف المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate_default",
                "default_en" => "Governorate default",
                "default_ar" => "الحالة الافتراضية للمحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate_status",
                "default_en" => "Governorate status",
                "default_ar" => "حالة المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
        ]);
        //city
        Translation::insert([
            [
                "key" => "city_create_form",
                "default_en" => "Add new city",
                "default_ar" => "اضف مدينة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "city_edit_form",
                "default_en" => "Edit city form",
                "default_ar" => "نموذج تعديل المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "city_name_ar",
                "default_en" => "City name (arabic)",
                "default_ar" => "اسم المدينة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "city_name_en",
                "default_en" => "City name (english)",
                "default_ar" => "اسم المدينة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "city_status",
                "default_en" => "City status",
                "default_ar" => "حالة المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
        ]);
        //avenue
        Translation::insert([
            [
                "key" => "avenue_create_form",
                "default_en" => "Add new avenue",
                "default_ar" => "اضف منطقة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "avenue_edit_form",
                "default_en" => "Edit avenue form",
                "default_ar" => "نموذج تعديل المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "avenue_name_ar",
                "default_en" => "Avenue name (arabic)",
                "default_ar" => "اسم المنطقة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "avenue_name_en",
                "default_en" => "Avenue name (english)",
                "default_ar" => "اسم المنطقة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "avenue_status",
                "default_en" => "Avenue status",
                "default_ar" => "حالة المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "bank_account",
                "default_en" => "Bank account name",
                "default_ar" => "اسم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"

            ],
        ]);
    }
}
