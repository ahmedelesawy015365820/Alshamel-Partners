<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //Admin report
        Translation::insert([
            [
                "key" => "admin_report_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_start_date",
                "default_en" => "Start date",
                "default_ar" => "تاريخ البداية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_end_date",
                "default_en" => "End date",
                "default_ar" => "تاريخ النهاية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_serial_number",
                "default_en" => "Serial number",
                "default_ar" => "رقم السيريال",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_prefix",
                "default_en" => "Prefix",
                "default_ar" => "البريفيكس",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "customer",
                "default_en" => "Customer name",
                "default_ar" => "اسم العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "saleman",
                "default_en" => "Saleman name",
                "default_ar" => "اسم رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_phone",
                "default_en" => "Customer phone",
                "default_ar" => "رقم هاتف العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_contact_phone",
                "default_en" => "Customer contact phone",
                "default_ar" => "هاتف العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "branch",
                "default_en" => "Branch name",
                "default_ar" => "اسم الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "document",
                "default_en" => "Document name",
                "default_ar" => "اسم الوثيقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unit",
                "default_en" => "Unit name",
                "default_ar" => "اسم الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_unit_area",
                "default_en" => "Unit area",
                "default_ar" => "مساحة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_path",
                "default_en" => "Bathroom",
                "default_ar" => "الحمام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_rooms",
                "default_en" => "Rooms",
                "default_ar" => "الغرف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_unit_value",
                "default_en" => "Unit value",
                "default_ar" => "قيمة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "building",
                "default_en" => "Building name",
                "default_ar" => "اسم المبنى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_building_lng",
                "default_en" => "Building lng",
                "default_ar" => "لنج المبني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_building_lat",
                "default_en" => "Building lat",
                "default_ar" => "لات المبنى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "owner",
                "default_en" => "Owner name",
                "default_ar" => "اسم المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "wallet",
                "default_en" => "Wallet name",
                "default_ar" => "اسم المحفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unit_status",
                "default_en" => "Unit status name",
                "default_ar" => "اسم حالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_owner_phone",
                "default_en" => "Owner phone",
                "default_ar" => "هاتف المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "admin_report_unit_ty",
                "default_en" => "Unity type",
                "default_ar" => "نوع الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Branch
        Translation::insert([
            [
                "key" => "branch_create_form",
                "default_en" => "Add new branch",
                "default_ar" => "اضف فرع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_edit_form",
                "default_en" => "Edit branch form",
                "default_ar" => "تعديل نموذج الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_name_ar",
                "default_en" => "Branch name (arabic)",
                "default_ar" => "اسم الفرع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_name_en",
                "default_en" => "Branch name (english)",
                "default_ar" => "اسم الفرع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_status",
                "default_en" => "Branch status",
                "default_ar" => "حالة الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_parent",
                "default_en" => "Parent",
                "default_ar" => "الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_email",
                "default_en" => "Email",
                "default_ar" => "الاميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_phone",
                "default_en" => "Phone",
                "default_ar" => "رقم الموبيل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_second_phone",
                "default_en" => "Second Phone",
                "default_ar" => "رقم الموبيل التاني ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_address",
                "default_en" => "Address",
                "default_ar" => "عنوان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_fax",
                "default_en" => "Fax",
                "default_ar" => "فكس",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "branch_p_o_pox",
                "default_en" => "p.o.box",
                "default_ar" => "ص.ب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
        ]);
        //Customer Category
        Translation::insert([
            [
                "key" => "customer_category_create_form",
                "default_en" => "Add new category",
                "default_ar" => "اضف فئة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer category"
            ],
            [
                "key" => "customer_category_edit_form",
                "default_en" => "Edit category",
                "default_ar" => "تعديل الفئة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer category"
            ],
            [
                "key" => "customer_category_name_ar",
                "default_en" => "Category name (arabic)",
                "default_ar" => "اسم الفئة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer category"
            ],
            [
                "key" => "customer_category_name_en",
                "default_en" => "Category name (english)",
                "default_ar" => "اسم الفئة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer category"
            ],
            [
                "key" => "customer_category_parent",
                "default_en" => "parent",
                "default_ar" => "الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer category"
            ],
        ]);
        //Customer Source
        Translation::insert([
            [
                "key" => "customer_source_create_form",
                "default_en" => "Add new source",
                "default_ar" => "اضف مصدر جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "customer_source_edit_form",
                "default_en" => "Edit source",
                "default_ar" => "تعديل المصدر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "customer_source_name_ar",
                "default_en" => "Source name (arabic)",
                "default_ar" => "اسم المصدر (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "customer_source_name_en",
                "default_en" => "Source name (english)",
                "default_ar" => "اسم المصدر (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
        ]);
        //Sales list
        Translation::insert([
            [
                "key" => "sales_list_invoice_id",
                "default_en" => "Invoice ID",
                "default_ar" => "رقم الفاتورة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_type",
                "default_en" => "Type",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_sold_by",
                "default_en" => "Sold by",
                "default_ar" => "تم البيع بواسطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_sold_to",
                "default_en" => "Sold to",
                "default_ar" => "تم البيع ل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_status",
                "default_en" => "Status",
                "default_ar" => "الحالة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_item_purchased",
                "default_en" => "Item purchased",
                "default_ar" => "الصنف المشترى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_tax",
                "default_en" => "Tax",
                "default_ar" => "الضريبة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_discount",
                "default_en" => "Discount",
                "default_ar" => "الخصم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_total",
                "default_en" => "Total",
                "default_ar" => "الاجمالي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_list_due",
                "default_en" => "Due",
                "default_ar" => "المستحقات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
        ]);
        //POS Reports
        Translation::insert([
            [
                "key" => "product",
                "default_en" => "Product Name",
                "default_ar" => "اسم المنتج",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "pos_report_month",
                "default_en" => "Month",
                "default_ar" => "شهر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "pos_report_year",
                "default_en" => "Year",
                "default_ar" => "سنة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],

            [
                "key" => "order_type",
                "default_en" => "Order Type",
                "default_ar" => "نوع الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "supplier_purchase",
                "default_en" => "Supplier Purchase",
                "default_ar" => "المشتريات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "variant",
                "default_en" => "Variant Name",
                "default_ar" => "اسم المتغير",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "adjustment_item",
                "default_en" => "Adjustment Item",
                "default_ar" => "عنصر التسوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "adjustment_type",
                "default_en" => "Adjustment Type",
                "default_ar" => "نوع التسوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sales_type",
                "default_en" => "Sales type",
                "default_ar" => "انواع المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "purchase_type",
                "default_en" => "Purchase Type",
                "default_ar" => "نوع الشراء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "supplier",
                "default_en" => "Supplier",
                "default_ar" => "المورد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "purchased_by",
                "default_en" => "Purchased by",
                "default_ar" => "تم الشراء بواسطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "items",
                "default_en" => "Items",
                "default_ar" => "الاصناف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sub_total",
                "default_en" => "Sub total",
                "default_ar" => "الاجمالي الجزئي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "brand",
                "default_en" => "Brand name",
                "default_ar" => "اسم العلامة التجارية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "item_sold",
                "default_en" => "Item sold",
                "default_ar" => "الصنف المباع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "purchase_type",
                "default_en" => "Purchase Type",
                "default_ar" => "نوع المشتريات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "supplier",
                "default_en" => "Supplier Name",
                "default_ar" => "اسم المورد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "purchased_by",
                "default_en" => "Purchased By",
                "default_ar" => "تم الشراء بواسطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "id",
                "default_en" => "id",
                "default_ar" => "الاي دي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "title",
                "default_en" => "Title",
                "default_ar" => "العنوان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "opened_by",
                "default_en" => "Opened by",
                "default_ar" => "مفتوح بواسطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "closed_by",
                "default_en" => "Closed by",
                "default_ar" => "مغلق بواسطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "status",
                "default_en" => "Status",
                "default_ar" => "الحالة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "note",
                "default_en" => "Note",
                "default_ar" => "ملحوظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "opening_amount",
                "default_en" => "Opening amount",
                "default_ar" => "الكمية الافتتاحية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "cash_sales",
                "default_en" => "Cash Sales",
                "default_ar" => "المبيعات النقدية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "cash_receives",
                "default_en" => "Cash Receives",
                "default_ar" => "المستلمات النقدية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "closing_amount",
                "default_en" => "Closing amount",
                "default_ar" => "الكمية المغلقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "difference",
                "default_en" => "Difference",
                "default_ar" => "الفرق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "sku",
                "default_en" => "SKU",
                "default_ar" => "SKU",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "item",
                "default_en" => "Item Name",
                "default_ar" => "اسم الصنف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "variant",
                "default_en" => "Variant",
                "default_ar" => "المتغير",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "category",
                "default_en" => "Category",
                "default_ar" => "الفئة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "group",
                "default_en" => "Group",
                "default_ar" => "الجروب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "brand",
                "default_en" => "Brand",
                "default_ar" => "العلامة التجارية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "purchase_price",
                "default_en" => "Purchase price",
                "default_ar" => "سعر الشراء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "selling_price",
                "default_en" => "Selling Price",
                "default_ar" => "سعر البيع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "inventory",
                "default_en" => "Inventory",
                "default_ar" => "المخزن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "barcode",
                "default_en" => "Barcode",
                "default_ar" => "الباركود",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "method",
                "default_en" => "Method",
                "default_ar" => "طريقة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "paid_by",
                "default_en" => "Paid by",
                "default_ar" => "تم الدفع بواسطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "cash_register",
                "default_en" => "Cash Register",
                "default_ar" => "تسجيل نقدي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "amount",
                "default_en" => "Amount",
                "default_ar" => "الكمية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "year",
                "default_en" => "Year",
                "default_ar" => "السنة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "grand_total",
                "default_en" => "Grand Total",
                "default_ar" => "المجموع الكلي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "profit_amount",
                "default_en" => "Profit Amount",
                "default_ar" => "مبلغ الربح",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "customer",
                "default_en" => "Customer",
                "default_ar" => "العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "total_sales",
                "default_en" => "Total Sales",
                "default_ar" => "اجمالي المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "total_return",
                "default_en" => "Total Return",
                "default_ar" => "اجمالي المرتجعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "total_payment",
                "default_en" => "Total Payment",
                "default_ar" => "اجمالي الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => " supplier",
                "default_en" => "Supplier",
                "default_ar" => "المورد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => " supplier_purchase",
                "default_en" => "Supplier Purchase",
                "default_ar" => "مشتريات المورد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => " shipping_area",
                "default_en" => "Shipping area",
                "default_ar" => "منطقة الشحن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => " address",
                "default_en" => "Address",
                "default_ar" => "العنوان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => " shipping_cost",
                "default_en" => "Shipping Cost",
                "default_ar" => "تكلفة الشحن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => " shipping_status",
                "default_en" => "Status",
                "default_ar" => "الحالة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
        ]);
        //Stock Adjustment type
        Translation::insert([
            [
                "key" => "stock_adjustment_type_create_form",
                "default_en" => "Add new stock adjustment type",
                "default_ar" => "اضف نوع تسوية مخزن جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "stock_adjustment_type_edit_form",
                "default_en" => "Edit stock adjustment type",
                "default_ar" => "تعديل نوع تسوية المخزن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "stock_adjustment_type_title_ar",
                "default_en" => "Stock adjustment type name (arabic)",
                "default_ar" => "اسم نوع تسوية المخزن (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "stock_adjustment_type_title_en",
                "default_en" => "Stock adjustment type name (english)",
                "default_ar" => "اسم نوع تسوية المخزن (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
        ]);
        //Serial
        Translation::insert([
            [
                "key" => "serial_create_form",
                "default_en" => "Add new serial",
                "default_ar" => "اضف سيريال جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_edit_form",
                "default_en" => "Edit serial form",
                "default_ar" => "تعديل السيريال",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_name_ar",
                "default_en" => "Serial name (arabic)",
                "default_ar" => "اسم السيريال (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_name_en",
                "default_en" => "Serial name (english)",
                "default_ar" => "اسم السريال (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_branch",
                "default_en" => "Branch name",
                "default_ar" => "اسم الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_document",
                "default_en" => "Document name",
                "default_ar" => "اسم المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_prefix",
                "default_en" => "Prefix",
                "default_ar" => "البريفيكس",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_suffix",
                "default_en" => "Suffix",
                "default_ar" => "السفيكس",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_restart_period",
                "default_en" => "Restart period",
                "default_ar" => "اعادة التشغيل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_start_no",
                "default_en" => "Start no",
                "default_ar" => "رقم البداية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],
            [
                "key" => "serial_gender",
                "default_en" => "gender",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "serial"
            ],

        ]);
        //Unit status
        Translation::insert([
            [
                "key" => "unitstatus_create_form",
                "default_en" => "Add new unit status",
                "default_ar" => "اضف حالة وحدة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "realEstate unit status"
            ],
            [
                "key" => "unitstatus_edit_form",
                "default_en" => "Edit unit status form",
                "default_ar" => "نموذج تعديل حالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "realEstate unit status"
            ],
            [
                "key" => "unitstatus_name_ar",
                "default_en" => "Unit status name (arabic)",
                "default_ar" => "اسم حالة الوحدة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "realEstate unit status"
            ],
            [
                "key" => "unitstatus_name_en",
                "default_en" => "Unit status name (english)",
                "default_ar" => "اسم حالة الوحدة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "realEstate unit status"

            ],
            [
                "key" => "unitstatus_status",
                "default_en" => "Unit status",
                "default_ar" => "حالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "realEstate unit status"

            ],
            [
                "key" => "unitstatus_default",
                "default_en" => "Unit status default",
                "default_ar" => "الحالة الافتراضية لحالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "realEstate unit status"

            ],
        ]);
        //Store
        Translation::insert([
            [
                "key" => "store_create_form",
                "default_en" => "Add new store",
                "default_ar" => "اضف مخزن جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "store",
            ],
            [
                "key" => "store_edit_form",
                "default_en" => "Edit store form",
                "default_ar" => "تعديل نموذج المخزن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "store",
            ],
            [
                "key" => "store_name_ar",
                "default_en" => "Store name (arabic)",
                "default_ar" => "اسم المخزن (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "store",
            ],
            [
                "key" => "store_name_en",
                "default_en" => "Store name (english)",
                "default_ar" => "اسم المخزن (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "store",
            ],
            [
                "key" => "store_status",
                "default_en" => "Store status",
                "default_ar" => "حالة المخزن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "store",

            ],
            [
                "key" => "branch",
                "default_en" => "Branch name",
                "default_ar" => "اسم الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "store",
            ],
        ]);
        //Screen property
        Translation::insert([
            [
                "key" => "screen_property_create_form",
                "default_en" => "Add new screen property form",
                "default_ar" => "اضف خاصية وشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "screen properties",
            ],
            [
                "key" => "screen_property_edit_form",
                "default_en" => "Edit screen property form",
                "default_ar" => "نموذج تعديل الخاصية والشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "screen properties",

            ],
            [
                "key" => "screen",
                "default_en" => "Screen Name",
                "default_ar" => "اسم الشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "screen properties",

            ],
            [
                "key" => "property",
                "default_en" => "Property name",
                "default_ar" => "اسم الخاصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "screen properties",

            ],
        ]);
        //Tree property
        Translation::insert([
            [
                "key" => "property_create_form",
                "default_en" => "Add new property form",
                "default_ar" => "اضف خاصية جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "tree property"
            ],
            [
                "key" => "property_edit_form",
                "default_en" => "Edit property form",
                "default_ar" => "نموذج تعديل الخاصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "tree property"
            ],
            [
                "key" => "property_name_ar",
                "default_en" => "Property name (arabic)",
                "default_ar" => "اسم الخاصية (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "tree property"

            ],
            [
                "key" => "property_name_en",
                "default_en" => "Property name (english)",
                "default_ar" => "اسم الخاصية (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "tree property"
            ],
            [
                "key" => "required",
                "default_en" => "required",
                "default_ar" => "مطلوب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "tree property"
            ],
            [
                "key" => "parent",
                "default_en" => "Parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "tree property"
            ],
        ]);
        //Sales men types
        Translation::insert([
            [
                "key" => "sale_man_type_create_form",
                "default_en" => "Add new sale man type",
                "default_ar" => "اضف نوع رجل مبيعات جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men Type"
            ],
            [
                "key" => "sale_man_type_edit_form",
                "default_en" => "Edit sale man type form",
                "default_ar" => "نموذج تعديل نوع رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men Type"
            ],
            [
                "key" => "sale_man_type_name_ar",
                "default_en" => "Sale man type name (arabic)",
                "default_ar" => "اسم نوع رجل المبيعات (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men Type"
            ],
            [
                "key" => "sale_man_type_name_en",
                "default_en" => "Sale man type name (english)",
                "default_ar" => "اسم نوع رجل المبيعات (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men Type"
            ],
            [
                "key" => "is_employee",
                "default_en" => "Is employee",
                "default_ar" => "هل موظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men Type"
            ],
        ]);
        //Sales man
        Translation::insert([
            [
                "key" => "sale_man_create_form",
                "default_en" => "Add new sale man",
                "default_ar" => "اضف رجل مبيعات جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_edit_form",
                "default_en" => "Edit sale man form",
                "default_ar" => "نموذج تعديل رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_name_ar",
                "default_en" => "Sale man name (arabic)",
                "default_ar" => "اسم رجل المبيعات (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_name_en",
                "default_en" => "Sale man name (english)",
                "default_ar" => "اسم رجل المبيعات (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_type",
                "default_en" => "Sale man type",
                "default_ar" => "نوع رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
        ]);
        //External sale man
        Translation::insert([
            [
                "key" => "external_sale_man_create_form",
                "default_en" => "Add new external sale man",
                "default_ar" => "اضف رجل مبيعات خارجي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "external_sale_man_edit_form",
                "default_en" => "Edit external sale man form",
                "default_ar" => "نموذج تعديل رجل مبيعات خارجي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "external_sale_man_phone",
                "default_en" => "External Sale man phone",
                "default_ar" => "هاتف رجل المبيعات الخارجي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "external_sale_man_email",
                "default_en" => "External Sale man email",
                "default_ar" => "البريد الالكتروني لرجل المبيعات الخارجي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "external_saleman_country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "external_sale_man_national_id",
                "default_en" => "External Sale man national id",
                "default_ar" => "الرقم القومي لرجل المبيعات الخارجي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "external_sale_man_address",
                "default_en" => "External Sale man address",
                "default_ar" => "عنوان رجل المبيعات الخارجي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "external_sale_man_code",
                "default_en" => "External Sale man code",
                "default_ar" => "كود رجل المبيعات الخارجي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "external_sale_man_status",
                "default_en" => "External Sale man status",
                "default_ar" => "حالة رجل المبيعات الخارجي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
        ]);
        //Payment type
        Translation::insert([
            [
                "key" => "payment_type_create_form",
                "default_en" => "Add new payment type",
                "default_ar" => "اضف نوع دفع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment types"
            ],
            [
                "key" => "payment_type_edit_form",
                "default_en" => "Edit payment type form",
                "default_ar" => " نموذج تعديل نوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment types"
            ],
            [
                "key" => "payment_type_name_ar",
                "default_en" => "Payment type name (arabic)",
                "default_ar" => "اسم نوع الدفع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment types"

            ],
            [
                "key" => "payment_type_name_en",
                "default_en" => "Payment type name (english)",
                "default_ar" => "اسم نوع الدفع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment types"

            ],
            [
                "key" => "payment_type_default",
                "default_en" => "Payment type default",
                "default_ar" => "الحالة الافتراضية لنوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment types"

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
                "screen" => "banks"
            ],
            [
                "key" => "bank_edit_form",
                "default_en" => "Edit bank form",
                "default_ar" => " نموذج تعديل البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "banks"
            ],
            [
                "key" => "bank_name_ar",
                "default_en" => "Bank name (arabic)",
                "default_ar" => "اسم البنك (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "banks"
            ],
            [
                "key" => "bank_name_en",
                "default_en" => "Bank name (english)",
                "default_ar" => "اسم البنك (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "banks"

            ],
            [
                "key" => "country",
                "default_en" => "Coutnry name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "banks"

            ],
            [
                "key" => "bank_swiftcode",
                "default_en" => "Bank swiftcode",
                "default_ar" => "سويفت كود البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "banks"
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
                "screen" => "bank accounts"
            ],
            [
                "key" => "bank_account_edit_form",
                "default_en" => "Edit bank bank form",
                "default_ar" => " نموذج تعديل الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],
            [
                "key" => "bank",
                "default_en" => "Bank name",
                "default_ar" => "اسم البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],
            [
                "key" => "bank_account_number",
                "default_en" => "Bank account number",
                "default_ar" => "رقم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],
            [
                "key" => "bank_account_phone",
                "default_en" => "Bank account phone",
                "default_ar" => "هاتف الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],
            [
                "key" => "bank_account_address",
                "default_en" => "Bank account address",
                "default_ar" => "عنوان الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],
            [
                "key" => "bank_account_email",
                "default_en" => "Bank account email",
                "default_ar" => "البريد الالكتروني للحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],

            [
                "key" => "employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],
            [
                "key" => "bank_account_rpcode",
                "default_en" => "Bank account rpcode",
                "default_ar" => "ار بي كود الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank accounts"
            ],
        ]);

        //Internal sale man
        Translation::insert([
            [
                "key" => "internal_sale_man_create_form",
                "default_en" => "Add new internal sale man",
                "default_ar" => "اضف رجل مبيعات داخلي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "internal salesmen"
            ],
            [
                "key" => "internal_sale_man_edit_form",
                "default_en" => "Edit internal sale man form",
                "default_ar" => "نموذج تعديل رجل مبيعات داخلي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "internal salesmen"
            ],
            [
                "key" => "employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "internal salesmen"
            ],
            [
                "key" => "internal_sale_man_status",
                "default_en" => "Internal sale man status",
                "default_ar" => "حالة رجل المبيعات الداخلي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "internal salesmen"
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
                "screen" => "country"
            ],
            [
                "key" => "country_edit_form",
                "default_en" => "Edit country form",
                "default_ar" => "نموذج تعديل الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_name_ar",
                "default_en" => "Country name (arabic)",
                "default_ar" => "اسم الدولة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_name_en",
                "default_en" => "Country name (english)",
                "default_ar" => "اسم الدولة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_long_name_ar",
                "default_en" => "Country long name (arabic)",
                "default_ar" => "اسم الدولة طويل (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_long_name_en",
                "default_en" => "Country long name (english)",
                "default_ar" => "اسم الدولة طويل (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_phone_key",
                "default_en" => "Country phone key",
                "default_ar" => "كود هاتف الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_short_code",
                "default_en" => "Country web code",
                "default_ar" => "الكود الويب المختصر للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_default",
                "default_en" => "Country default",
                "default_ar" => "الحالة الافتراضية للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_status",
                "default_en" => "Country status",
                "default_ar" => "حالة الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
            ],
            [
                "key" => "country_national_id",
                "default_en" => "Country national id",
                "default_ar" => "الرقم القومي للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "country"
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
                "screen" => "governorate"
            ],
            [
                "key" => "governorate_edit_form",
                "default_en" => "Edit governorate form",
                "default_ar" => "نموذج تعديل المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "governorate"
            ],
            [
                "key" => "governorate_name_ar",
                "default_en" => "Governorate name (arabic)",
                "default_ar" => "اسم المحافظة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "governorate"
            ],
            [
                "key" => "governorate_name_en",
                "default_en" => "Governorate name (english)",
                "default_ar" => "اسم المحافظة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "governorate"
            ],
            [
                "key" => "governorate_phone_key",
                "default_en" => "Governorate phone key",
                "default_ar" => "كود هاتف المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "governorate"
            ],
            [
                "key" => "governorate_default",
                "default_en" => "Governorate default",
                "default_ar" => "الحالة الافتراضية للمحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "governorate"
            ],
            [
                "key" => "governorate_status",
                "default_en" => "Governorate status",
                "default_ar" => "حالة المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "governorate"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "governorate"
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
                "screen" => "city"
            ],
            [
                "key" => "city_edit_form",
                "default_en" => "Edit city form",
                "default_ar" => "نموذج تعديل المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "city"
            ],
            [
                "key" => "city_name_ar",
                "default_en" => "City name (arabic)",
                "default_ar" => "اسم المدينة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "city"
            ],
            [
                "key" => "city_name_en",
                "default_en" => "City name (english)",
                "default_ar" => "اسم المدينة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "city"
            ],
            [
                "key" => "city_status",
                "default_en" => "City status",
                "default_ar" => "حالة المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "city"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "city"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "city"
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
                "screen" => "avenue"
            ],
            [
                "key" => "avenue_edit_form",
                "default_en" => "Edit avenue form",
                "default_ar" => "نموذج تعديل المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "avenue_name_ar",
                "default_en" => "Avenue name (arabic)",
                "default_ar" => "اسم المنطقة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "avenue_name_en",
                "default_en" => "Avenue name (english)",
                "default_ar" => "اسم المنطقة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "avenue_status",
                "default_en" => "Avenue status",
                "default_ar" => "حالة المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
        ]);
        //Role type
        Translation::insert([
            [
                "key" => "role_type_create_form",
                "default_en" => "Add new role type",
                "default_ar" => "اضف نوع دور جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role Type"
            ],
            [
                "key" => "role_type_edit_form",
                "default_en" => "Edit role type form",
                "default_ar" => "نموذج تعديل نوع الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role Type"
            ],
            [
                "key" => "role_type_name_ar",
                "default_en" => "Role type name (arabic)",
                "default_ar" => "اسم نوع الدور (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role Type"
            ],
            [
                "key" => "role_type_name_en",
                "default_en" => "Role type name (english)",
                "default_ar" => "اسم نوع الدور (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role Type"
            ],
        ]);
        //Role
        Translation::insert([
            [
                "key" => "role_create_form",
                "default_en" => "Add new role",
                "default_ar" => "اضف دور جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "roles"
            ],
            [
                "key" => "role_edit_form",
                "default_en" => "Edit role form",
                "default_ar" => "نموذج تعديل الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "roles"
            ],
            [
                "key" => "role_name",
                "default_en" => "name (arabic)",
                "default_ar" => "اسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "roles"
            ],
            [
                "key" => "role_name_e",
                "default_en" => "name (english)",
                "default_ar" => "اسم (الانجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "roles"
            ],
        ]);
        //Role workflow
        Translation::insert([
            [
                "key" => "role_workflow_create_form",
                "default_en" => "Add new role workflow",
                "default_ar" => "اضف دور سير عمل جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow"
            ],
            [
                "key" => "role_workflow_edit_form",
                "default_en" => "Edit role workflow form",
                "default_ar" => "نموذج تعديل دور سير العمل ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow"
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow"
            ],
            [
                "key" => "workflow",
                "default_en" => "Workflow name",
                "default_ar" => "اسم سير العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow"
            ],
        ]);
        //Role workflow button
        Translation::insert([
            [
                "key" => "role_workflow_button_create_form",
                "default_en" => "Add new role workflow button",
                "default_ar" => "اضف دور سير عمل زر جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow button"
            ],
            [
                "key" => "role_workflow_button_edit_form",
                "default_en" => "Edit role workflow button form",
                "default_ar" => "نموذج تعديل دور سير العمل والزرار ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow button"
            ],
            [
                "key" => "button",
                "default_en" => "Button name",
                "default_ar" => "اسم الزرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow button"
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow button"
            ],
            [
                "key" => "workflow",
                "default_en" => "Workflow name",
                "default_ar" => "اسم سير العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role workflow button"
            ],
        ]);
        //Role hotfield screen
        Translation::insert([
            [
                "key" => "role_hotfield_screen_create_form",
                "default_en" => "Add new role hotfield screen",
                "default_ar" => "اضف الدور مع الحقل والشاشة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role hotfield screen",
            ],
            [
                "key" => "role_hotfield_screen_edit_form",
                "default_en" => "Edit role hotfield screen form",
                "default_ar" => "نموذج تعديل الدور مع الحقل والشاشة ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role hotfield screen",
            ],
            [
                "key" => "hotfield",
                "default_en" => "Hotfield name",
                "default_ar" => "اسم الحقل الساخن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role hotfield screen",
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role hotfield screen"
            ],
            [
                "key" => "workflow",
                "default_en" => "Workflow name",
                "default_ar" => "اسم سير العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role hotfield screen"
            ],
        ]);
        //Workflow hotfield
        Translation::insert([
            [
                "key" => "workflow_hotfield_create_form",
                "default_en" => "Add new workflow hotfield",
                "default_ar" => "اضف سير العمل مع الحقل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Workflow hotfields"
            ],
            [
                "key" => "workflow_hotfield_edit_form",
                "default_en" => "Edit workflow hotfield form",
                "default_ar" => "نموذج تعديل سير العمل والحقل ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Workflow hotfields"
            ],
            [
                "key" => "hotfield",
                "default_en" => "Hotfield name",
                "default_ar" => "اسم الحقل الساخن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Workflow hotfields",
            ],
            [
                "key" => "workflow",
                "default_en" => "Workflow name",
                "default_ar" => "اسم سير العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Workflow hotfields"
            ],
        ]);
        //Users
        Translation::insert([
            [
                "key" => "user_create_form",
                "default_en" => "Add new user",
                "default_ar" => "اضف مستخدم جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "user_edit_form",
                "default_en" => "Edit user form",
                "default_ar" => "تعديل نموذج المستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "user_name_ar",
                "default_en" => "User name (arabic)",
                "default_ar" => "اسم المستخدم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "user_name_en",
                "default_en" => "User name (english)",
                "default_ar" => "اسم المستخدم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "user_status",
                "default_en" => "User status",
                "default_ar" => "حالة المستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "user_email",
                "default_en" => "User email",
                "default_ar" => "البريد الالكتروني للمستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "employee_user",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "role_user",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "user_password",
                "default_en" => "User Password",
                "default_ar" => "كلمة مرور المستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
            [
                "key" => "role",
                "default_en" => "Role",
                "default_ar" => "الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "users"
            ],
        ]);
        //Currency
        Translation::insert([
            [
                "key" => "currency_create_form",
                "default_en" => "Add new currency",
                "default_ar" => "اضف عملة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"
            ],
            [
                "key" => "currency_edit_form",
                "default_en" => "Edit currency form",
                "default_ar" => "تعديل نموذج العملة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_name_ar",
                "default_en" => "Currency name (arabic)",
                "default_ar" => "اسم العملة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_name_en",
                "default_en" => "Currency name (english)",
                "default_ar" => "اسم العملة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_code_ar",
                "default_en" => "Currency code (arabic)",
                "default_ar" => "كود العملة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_code_en",
                "default_en" => "Currency code (english)",
                "default_ar" => "كود العملة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_fraction_ar",
                "default_en" => "Currency fraction (arabic)",
                "default_ar" => "كسر العملة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_fraction_en",
                "default_en" => "Currency fraction (english)",
                "default_ar" => "كسر العملة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_symbol_ar",
                "default_en" => "Currency symbol (arabic)",
                "default_ar" => "رمز العملة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"
            ],
            [
                "key" => "currency_symbol_en",
                "default_en" => "Currency symbol (english)",
                "default_ar" => "رمز العملة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_status",
                "default_en" => "Currency status",
                "default_ar" => "حالة العملة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_default",
                "default_en" => "Currency default",
                "default_ar" => "الحالة الافتراضية للعملة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
            [
                "key" => "currency_fraction_number",
                "default_en" => "Currency fraction number",
                "default_ar" => "رقم كسر العملة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "currencies"

            ],
        ]);
        //Employee
        Translation::insert([
            [
                "key" => "employee_create_form",
                "default_en" => "Add new employee",
                "default_ar" => "اضف موظف جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_edit_form",
                "default_en" => "Edit employee form",
                "default_ar" => "تعديل نموذج الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "employee_department",
                "default_en" => "Department",
                "default_ar" => "القسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "employee_name_ar",
                "default_en" => " name (arabic)",
                "default_ar" => "اسم  (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "employee_is_sms",
                "default_en" => "Sms is the same WhatsApp number ? ",
                "default_ar" => "هل رقم الواتس هو نفسه رقم sms ؟",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "employee_name_en",
                "default_en" => " name (english)",
                "default_ar" => "اسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"

            ],
            [
                "key" => "employee_manager",
                "default_en" => "Name of the direct manager",
                "default_ar" => "اسم المدير المباشر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_managers",
                "default_en" => "other managers",
                "default_ar" => " المديرين الاخرين ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_att_code",
                "default_en" => "Fingerprint code",
                "default_ar" => "كود البصمه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_sms",
                "default_en" => "SMS Message",
                "default_ar" => "رسائل sms",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_is_salesman",
                "default_en" => "is salesman",
                "default_ar" => "رجل مبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "manage_others",
                "default_en" => "Manage others",
                "default_ar" => "مدير للاخرين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_for_all_customer",
                "default_en" => "For all customer",
                "default_ar" => "لجميع العملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_customer_handel",
                "default_en" => "Customer handle",
                "default_ar" => "التعامل مع العملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_plan",
                "default_en" => "Plan name",
                "default_ar" => "اسم الخطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_sale_man_type",
                "default_en" => "Sale man type",
                "default_ar" => "نوع رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_mobile",
                "default_en" => "Mobile",
                "default_ar" => "رقم التليفون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_whatsapp",
                "default_en" => "Whatsapp",
                "default_ar" => "واتساب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_email",
                "default_en" => "Email",
                "default_ar" => "الاميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "sale_man_type_create_form",
                "default_en" => "Add new sale man type",
                "default_ar" => "اضف نوع رجل مبيعات جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "sale_man_type_edit_form",
                "default_en" => "Edit sale man type form",
                "default_ar" => "نموذج تعديل نوع رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "sale_man_type_name_ar",
                "default_en" => "Sale man type name (arabic)",
                "default_ar" => "اسم نوع رجل المبيعات (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "sale_man_type_name_en",
                "default_en" => "Sale man type name (english)",
                "default_ar" => "اسم نوع رجل المبيعات (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "is_employee",
                "default_en" => "Is employee",
                "default_ar" => "هل موظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_job",
                "default_en" => "Job name",
                "default_ar" => "اسم الوظيفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "employee_branch",
                "default_en" => "Branch name",
                "default_ar" => "اسم الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
        ]);
        //Financial Year
        Translation::insert([
            [
                "key" => "financial_year_create_form",
                "default_en" => "Add new financial year",
                "default_ar" => "اضف سنة مالية جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"
            ],
            [
                "key" => "financial_year_edit_form",
                "default_en" => "Edit financial year form",
                "default_ar" => "تعديل نموذج السنة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"

            ],
            [
                "key" => "financial_year_name_ar",
                "default_en" => "Financial year name (arabic)",
                "default_ar" => "اسم السنة المالية (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"

            ],
            [
                "key" => "financial_year_name_en",
                "default_en" => "Financial year name (english)",
                "default_ar" => "اسم السنة المالية (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"

            ],
            [
                "key" => "financial_year_start_date",
                "default_en" => "Financial year start date",
                "default_ar" => "بداية تاريخ السنة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"

            ],
            [
                "key" => "financial_year_end_date",
                "default_en" => "Financial year end date",
                "default_ar" => "نهاية تاريخ السنة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"

            ],
            [
                "key" => "financial_year_default",
                "default_en" => "Is active",
                "default_ar" => "تفعيل ؟",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"

            ],
            [
                "key" => "financial_year_year",
                "default_en" => "For Year",
                "default_ar" => "عن سنه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "financial Year"

            ],
        ]);
        //Units
        Translation::insert([
            [
                "key" => "unit_create_form",
                "default_en" => "Add new unit",
                "default_ar" => "اضف وحدة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Units"
            ],
            [
                "key" => "unit_edit_form",
                "default_en" => "Edit unit form",
                "default_ar" => "نموذج تعديل الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Units"

            ],
            [
                "key" => "unit_name_ar",
                "default_en" => "Unit name (arabic)",
                "default_ar" => "اسم الوحدة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Units"

            ],
            [
                "key" => "unit_name_en",
                "default_en" => "Unit name (english)",
                "default_ar" => "اسم الوحدة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Units"

            ],
            [
                "key" => "unit_status",
                "default_en" => "Unit status",
                "default_ar" => "حالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Units"

            ],
        ]);
        //Color
        Translation::insert([
            [
                "key" => "color_create_form",
                "default_en" => "Add new color",
                "default_ar" => "اضف لون جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "color"
            ],
            [
                "key" => "color_edit_form",
                "default_en" => "Edit color form",
                "default_ar" => "نموذج تعديل اللون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "color"

            ],
            [
                "key" => "color_name_ar",
                "default_en" => "Color name (arabic)",
                "default_ar" => "اسم اللون (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "color"

            ],
            [
                "key" => "color_name_en",
                "default_en" => "Color name (english)",
                "default_ar" => "اسم اللون (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "color"

            ],
            [
                "key" => "color_status",
                "default_en" => "Color status",
                "default_ar" => "حالة اللون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "color"

            ],
        ]);
        //Contract unit
        Translation::insert([
            [
                "key" => "contractunit_create_form",
                "default_en" => "Add new contract unit form",
                "default_ar" => "اضف وحدة عقد جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract unit"
            ],
            [
                "key" => "contractunit_edit_form",
                "default_en" => "Edit contract unit form",
                "default_ar" => "نموذج تعديل وحدة عقد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract unit"

            ],
        ]);
        //Contract
        Translation::insert([
            [
                "key" => "contract_create_form",
                "default_en" => "Add new contract",
                "default_ar" => "اضف عقد جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract"
            ],
            [
                "key" => "contract_edit_form",
                "default_en" => "Edit contract form",
                "default_ar" => "تعديل نموذج العقد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract"

            ],
            [
                "key" => "customer",
                "default_en" => "Customer name",
                "default_ar" => "اسم العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract"
            ],
            [
                "key" => "contract_date",
                "default_en" => "Contract date",
                "default_ar" => "تاريخ العقد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract"
            ],
            [
                "key" => "sale_man",
                "default_en" => "Sale man name",
                "default_ar" => "اسم رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract"

            ],
            [
                "key" => "reservation_date",
                "default_en" => "Reservation date",
                "default_ar" => "تاريخ الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "contract"

            ],
            [
                "key" => "reservation_installment_plan",
                "default_en" => "Installment plan",
                "default_ar" => "خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "reservation_start_date",
                "default_en" => "Start date",
                "default_ar" => "تاريخ البداية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "reservation_end_date",
                "default_en" => "End date",
                "default_ar" => "تاريخ النهاية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unit_value",
                "default_en" => "Unit amount",
                "default_ar" => "مبلغ الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "reservation_value",
                "default_en" => "Reservation amount",
                "default_ar" => "مبلغ الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "reservation_serial",
                "default_en" => "Serial",
                "default_ar" => "Serial",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Real estate unit types
        Translation::insert([
            [
                "key" => "unit_type_create_form",
                "default_en" => "Add new unit type",
                "default_ar" => "اضف نوع وحدة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unit_type_edit_form",
                "default_en" => "Edit unit type",
                "default_ar" => "تعديل نوع الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unit_type_name_ar",
                "default_en" => "Unit type name (arabic)",
                "default_ar" => "اسم نوع الوحدة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unit_type_name_en",
                "default_en" => "Unit type name (english)",
                "default_ar" => "اسم نوع الوحدة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Real estate finishing
        Translation::insert([
            [
                "key" => "finishing_create_form",
                "default_en" => "Add finishing",
                "default_ar" => "اضف تشطيب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "finishing_edit_form",
                "default_en" => "Edit finishing",
                "default_ar" => "تعديل التشطيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "finishing_name_ar",
                "default_en" => "Finishing name (arabic)",
                "default_ar" => "اسم التشطيب (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "finishing_name_en",
                "default_en" => "Finishing name (english)",
                "default_ar" => "اسم التشطيب (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Real estate view
        Translation::insert([
            [
                "key" => "view_create_form",
                "default_en" => "Add view",
                "default_ar" => "اضف منظر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "view_edit_form",
                "default_en" => "Edit view",
                "default_ar" => "تعديل منظر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "view_name_ar",
                "default_en" => "View name (arabic)",
                "default_ar" => "اسم المنظر (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "view_name_en",
                "default_en" => "View name (english)",
                "default_ar" => "اسم المنظر (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],


        ]);
        //real estate units
        Translation::insert([
            [
                "key" => "realEstate_unit_create_form",
                "default_en" => "Add New Unit",
                "default_ar" => "اضف وحده جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "realEstate_unit_edit_form",
                "default_en" => "Edit color form",
                "default_ar" => "نموذج تعديل الوحده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_name_ar",
                "default_en" => "Unit Name (arabic)",
                "default_ar" => "اسم وحده (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_name_en",
                "default_en" => "Unit Name (english)",
                "default_ar" => "اسم وحده (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_description_ar",
                "default_en" => "Unit Description (arabic)",
                "default_ar" => "وصف  الوحده (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_description_en",
                "default_en" => "Unit Description (english)",
                "default_ar" => "وصف الوحده (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_code",
                "default_en" => "Unit Code",
                "default_ar" => "كود الوحده (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_unit_ty",
                "default_en" => "Unit Ty ",
                "default_ar" => "نوع الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_status_date",
                "default_en" => "Status Date ",
                "default_ar" => "وقت الحاله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_unit_area",
                "default_en" => "Unit Area ",
                "default_ar" => "مساحه الوحده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_building",
                "default_en" => "Unit building ",
                "default_ar" => "وحده المبنا (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_owner",
                "default_en" => "Unit Owner ",
                "default_ar" => "مالك المبني (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_currency",
                "default_en" => "Unit Currency ",
                "default_ar" => "عمله الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_wallet",
                "default_en" => "Unit wallet ",
                "default_ar" => "محفظه الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_wallet",
                "default_en" => "Unit wallet ",
                "default_ar" => "محفظه الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_status",
                "default_en" => "Unit Status ",
                "default_ar" => "حاله الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_price",
                "default_en" => "price ",
                "default_ar" => "سعر (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_rooms",
                "default_en" => "Rooms ",
                "default_ar" => "الغرف (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_path",
                "default_en" => "Path ",
                "default_ar" => "حمام (عقارات) ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_floor",
                "default_en" => "Floor ",
                "default_ar" => "الارضيه (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_view",
                "default_en" => "View ",
                "default_ar" => "المنظر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_unit_net_area",
                "default_en" => "Unit Net Area",
                "default_ar" => "Unit Net Area",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "realEstate_unit_finishing",
                "default_en" => "Finishing ",
                "default_ar" => "التشطيب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "realEstate_unit_properties",
                "default_en" => "Properties ",
                "default_ar" => "الخصائص",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_rank",
                "default_en" => "Rank ",
                "default_ar" => "مرتبه (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_module",
                "default_en" => "Module ",
                "default_ar" => "موديول (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ]
        ]);
        //Owners
        Translation::insert([
            [
                "key" => "owner_create_form",
                "default_en" => "Add new owner",
                "default_ar" => "اضف مالك جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"
            ],
            [
                "key" => "owner_edit_form",
                "default_en" => "Edit owner form",
                "default_ar" => "نموذج تعديل المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_name_ar",
                "default_en" => "Owner name (arabic)",
                "default_ar" => "اسم المالك (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_name_en",
                "default_en" => "Owner name (english)",
                "default_ar" => "اسم المالك (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_phone",
                "default_en" => "Owner phone",
                "default_ar" => "هاتف المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_email",
                "default_en" => "Owner email",
                "default_ar" => "بريد الكترون المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_nationality",
                "default_en" => "Owner nationality",
                "default_ar" => "جنسية المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_national_id",
                "default_en" => "Owner national id",
                "default_ar" => "الرقم القومي للمالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_contact_person",
                "default_en" => "Owner contact person",
                "default_ar" => "معلومات المالك الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_contact_phones",
                "default_en" => "Owner contact phones",
                "default_ar" => "هواتف اتصال المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "bank_account",
                "default_en" => "Bank account name",
                "default_ar" => "اسم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_whatsapp",
                "default_en" => "Owner whatsapp",
                "default_ar" => "رقم واتساب المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
            [
                "key" => "owner_code",
                "default_en" => "Owner code",
                "default_ar" => "كود المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "owners"

            ],
        ]);
        //Buildings
        Translation::insert([
            [
                "key" => "building_create_form",
                "default_en" => "Add new building",
                "default_ar" => "اضف بناء جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"
            ],
            [
                "key" => "building_edit_form",
                "default_en" => "Edit building form",
                "default_ar" => "نموذج تعديل البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_street",
                "default_en" => "Street name",
                "default_ar" => "اسم الشارع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_name_ar",
                "default_en" => "Building name (arabic)",
                "default_ar" => "اسم البناء (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_name_en",
                "default_en" => "Building name (english)",
                "default_ar" => "اسم البناء (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_description_ar",
                "default_en" => "Building description (arabic)",
                "default_ar" => "وصف البناء (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_description_en",
                "default_en" => "Building description (english)",
                "default_ar" => "وصف البناء (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_area",
                "default_en" => "Building area (m)",
                "default_ar" => "مساحة البناء (متر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_land_area",
                "default_en" => "Building land area (m)",
                "default_ar" => "مساحةارض البناء (متر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_construction_year",
                "default_en" => "Building construction year",
                "default_ar" => "سنة تاسيس البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "module",
                "default_en" => "Module name",
                "default_ar" => "اسم الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"
            ],

            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],

            [
                "key" => "avenue",
                "default_en" => "Avenue name",
                "default_ar" => "اسم المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_longitude",
                "default_en" => "Building longitude",
                "default_ar" => "خطوط طول المبنى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
            [
                "key" => "building_latitude",
                "default_en" => "Building latitude",
                "default_ar" => "خطوط عرض المبنى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "building"

            ],
        ]);
        //Customers
        Translation::insert([
            [
                "key" => "customer_create_form",
                "default_en" => "Add new customer",
                "default_ar" => "اضف زبون جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"
            ],
            [
                "key" => "customer_edit_form",
                "default_en" => "Edit customer form",
                "default_ar" => "نموذج تعديل العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_name_ar",
                "default_en" => "Customer name (arabic)",
                "default_ar" => "اسم العميل (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_name_en",
                "default_en" => "Customer name (english)",
                "default_ar" => "اسم العميل (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_phone",
                "default_en" => "Customer phone",
                "default_ar" => "هاتف العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_email",
                "default_en" => "Customer email",
                "default_ar" => "بريد العميل الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_nationality",
                "default_en" => "Customer nationality",
                "default_ar" => "جنسية العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_national_id",
                "default_en" => "Customer national id",
                "default_ar" => "الرقم القومي للزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_contact_person",
                "default_en" => "Customer contact person",
                "default_ar" => "معلومات العميل الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_contact_phones",
                "default_en" => "Customer contact phones",
                "default_ar" => "هواتف اتصال العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_whatsapp",
                "default_en" => "Customer whatsapp",
                "default_ar" => "رقم واتساب العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_code",
                "default_en" => "Customer code",
                "default_ar" => "كود العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],
            [
                "key" => "customer_passport_number",
                "default_en" => "Customer passport number",
                "default_ar" => "رقم جواز سفر العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer"

            ],

        ]);
        //reservation
        Translation::insert([
            [
                "key" => "reservation_create_form",
                "default_en" => "Add new reservation",
                "default_ar" => "اضف حجز جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "reservation_edit_form",
                "default_en" => "Edit reservation form",
                "default_ar" => "تعديل نموذج الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "reservation_date",
                "default_en" => "Reservation date",
                "default_ar" => "تاريخ الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer",
                "default_en" => "Customer name",
                "default_ar" => "اسم العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "branch",
                "default_en" => "Branch name",
                "default_ar" => "اسم الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "serial",
                "default_en" => "Serial name",
                "default_ar" => "اسم التسلسل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],

            [
                "key" => "sale_man",
                "default_en" => "Sale man name",
                "default_ar" => "اسم رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "payment_plan",
                "default_en" => "Payment Plan",
                "default_ar" => "خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //Building wallet
        Translation::insert([
            [
                "key" => "building_wallet_create_form",
                "default_en" => "Add new building wallet",
                "default_ar" => "اضافة محفظة بناء جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
            ],
            [
                "key" => "building_wallet_edit_form",
                "default_en" => "Edit building wallet form",
                "default_ar" => "نموذج تعديل محفظة البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
            ],
            [
                "key" => "building",
                "default_en" => "Building name",
                "default_ar" => "اسم البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
            ],
            [
                "key" => "wallet",
                "default_en" => "Wallet name",
                "default_ar" => "اسم المحفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
            ],
            [
                "key" => "building_wallet_bu_ty",
                "default_en" => "Buty",
                "default_ar" => "الزبد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
            ]
        ]);
        //Wallet
        Translation::insert([
            [
                "key" => "wallet_create_form",
                "default_en" => "Add new wallet",
                "default_ar" => "اضافة محفظة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallets"
            ],
            [
                "key" => "wallet_edit_form",
                "default_en" => "Edit wallet form",
                "default_ar" => "نموذج تعديل محفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallets"

            ],
            [
                "key" => "wallet_name_ar",
                "default_en" => "Wallet name (arabic)",
                "default_ar" => "اسم المحفظة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallets"
            ],
            [
                "key" => "wallet_name_en",
                "default_en" => "Wallet name (english)",
                "default_ar" => "اسم المحفظة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallets"

            ],
        ]);
        //Wallet owner
        Translation::insert([
            [
                "key" => "wallet_owner_create_form",
                "default_en" => "Add new wallet owner",
                "default_ar" => "اضافة محفظة مالك جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallet owner"
            ],
            [
                "key" => "wallet_owner_edit_form",
                "default_en" => "Edit wallet owner form",
                "default_ar" => "نموذج تعديل محفظة المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallet owner"
            ],
            [
                "key" => "wallet",
                "default_en" => "Wallet name",
                "default_ar" => "اسم المحفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallet owner"
            ],
            [
                "key" => "owner",
                "default_en" => "Owner name",
                "default_ar" => "اسم المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallet owner"
            ],
            [
                "key" => "wallet_owner_percentage",
                "default_en" => "Wallet owner percentage",
                "default_ar" => "النسبة المئوية لمالك المحفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "wallet owner"
            ],
        ]);
        //sub contact group
        Translation::insert([
            [
                "key" => "subcontact_group_create_form",
                "default_en" => "Add new sub contact group",
                "default_ar" => "اضف جروب تواصل فرعي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sub contact group"
            ],
            [
                "key" => "subcontact_group_edit_form",
                "default_en" => "Edit sub contact group form",
                "default_ar" => "نموذج تعديل جروب التواصل الفرعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sub contact group"
            ],
            [
                "key" => "subcontact_group_name_ar",
                "default_en" => "sub contact group name (arabic)",
                "default_ar" => "اسم جروب التواصل الفرعي (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sub contact group"
            ],
            [
                "key" => "subcontact_group_name_en",
                "default_en" => "sub contact group name (english)",
                "default_ar" => "اسم جروب التواصل الفرعي (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sub contact group"
            ],
            [
                "key" => "main_contact_group",
                "default_en" => "Main contact group name ",
                "default_ar" => "اسم جروب التواصل الاساسي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sub contact group"
            ],
            [
                "key" => "gl_coa_acc",
                "default_en" => "GL Coa account",
                "default_ar" => "اسم الحساب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sub contact group"
            ],
        ]);
        //Payment plan installment
        Translation::insert([
            [
                "key" => "payment_plan_installment_create_form",
                "default_en" => "Add new payment plan installment",
                "default_ar" => "إضافة خطة دفع جديدة بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "payment_plan_installment_edit_form",
                "default_en" => "Edit payment plan installment form",
                "default_ar" => "نموذج تعديل التقسيط لخطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_payment_plan",
                "default_en" => " payment plan name",
                "default_ar" => "اسم خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_payment_plan_v_date",
                "default_en" => "payment plan v-date",
                "default_ar" => "التاريخ الخاس لخطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_payment_plan_note_a",
                "default_en" => "note (arabic)",
                "default_ar" => "ملاحظه (بالعربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_payment_plan_note_e",
                "default_en" => " note (english)",
                "default_ar" => "ملاحظه (بالانجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_payment_plan_due_date",
                "default_en" => "payment plan due date",
                "default_ar" => "تاريخ استحقاق خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_payment_plan_total_amount",
                "default_en" => "payment plan total amount",
                "default_ar" => "المبلغ الاجمالي لخطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_payment_plan_paid_amount",
                "default_en" => "payment plan paid amount",
                "default_ar" => "المبلغ المدفوع لخطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "installment_status",
                "default_en" => "Installment status name",
                "default_ar" => "اسم حالة التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "doc_type",
                "default_en" => "Document type name",
                "default_ar" => "اسم نوع الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "ref_id",
                "default_en" => "Ref ID",
                "default_ar" => "رقم الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
            [
                "key" => "rp_code",
                "default_en" => "rp code",
                "default_ar" => "ترميز rp",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "payment plan installment"
            ],
        ]);
        //Installment Status
        Translation::insert([
            [
                "key" => "installment_status_create_form",
                "default_en" => "Add new Installment Status",
                "default_ar" => "اضافة حاله تقسيط جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_status_edit_form",
                "default_en" => "Edit  Installment Status",
                "default_ar" => "نموذج تعديل حاله التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_status_name",
                "default_en" => "Installment Status name (arabic)",
                "default_ar" => "اسم حاله التقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_status_name_e",
                "default_en" => "installment_status name (english)",
                "default_ar" => "اسم حاله التقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_status_default",
                "default_en" => "Installment Status Default",
                "default_ar" => "الحالة الافتراضية لحاله التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
        ]);
        //Installment Payment Type
        Translation::insert([
            [
                "key" => "installment_payment_type_create_form",
                "default_en" => "Add new Installment Payment Type",
                "default_ar" => "اضافة نوع دفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_payment_type_edit_form",
                "default_en" => "Edit  Installment Payment Type",
                "default_ar" => "نموذج تعديل نوع الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_payment_type_name",
                "default_en" => "Installment Payment Type name (arabic)",
                "default_ar" => "اسم نوع دفع بالتقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_payment_type_name_e",
                "default_en" => "Installment Payment Type name (english)",
                "default_ar" => "اسم نوع دفع بالتقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_payment_auto_freq",
                "default_en" => "Installment Status Auto Freq",
                "default_ar" => "Installment Status Auto Freq",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ],
            [
                "key" => "installment_payment_is_partially",
                "default_en" => "Installment Payment Partially",
                "default_ar" => "Installment Payment Partially",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "installment status"
            ]
        ]);
        //Main Contact Groups
        Translation::insert([
            [
                "key" => "MainContactGroupsCreate",
                "default_en" => "Add new Main Contact Group",
                "default_ar" => "اضافة مجموعة الاتصال الرئيسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Main Contact Groups"
            ],
            [
                "key" => "MainContactGroupsEdit",
                "default_en" => "Edit Main Contact Group",
                "default_ar" => "نموذج تعديل مجموعة الاتصال الرئيسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Main Contact Groups"
            ],
            [
                "key" => "Group_name",
                "default_en" => "name Group (arabic)",
                "default_ar" => "اسم المجموعة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Main Contact Groups"
            ],
            [
                "key" => "Group_name_e",
                "default_en" => "name Group (english)",
                "default_ar" => "اسم المجموعة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Main Contact Groups"
            ],
        ]);
        //Installment Payment Plan Detail
        Translation::insert([
            [
                "key" => "InstallmentPaymentPlanDetailCreate",
                "default_en" => "Add new Installment Payment Plan Detail",
                "default_ar" => "اضافة تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "InstallmentPaymentPlanDetailEdit",
                "default_en" => "Edit Installment Payment Plan Detail",
                "default_ar" => "نموذج تعديل تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "installment_payment_type_id",
                "default_en" => "installment payment type",
                "default_ar" => "نوع الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "ln_no",
                "default_en" => "Line No",
                "default_ar" => "رقم الخط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "is_fixed",
                "default_en" => "is fixed",
                "default_ar" => "تم إصلاحه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "installment_payment_type_per",
                "default_en" => "installment payment type per",
                "default_ar" => "لكل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "installment_payment_type_amount",
                "default_en" => "installment payment type amount",
                "default_ar" => "المبلغ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "installment_payment_type_freq",
                "default_en" => "installment payment type freq",
                "default_ar" => "عدد الاقساط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "interest_per",
                "default_en" => "interest per",
                "default_ar" => "نسبة الفايدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
            [
                "key" => "interest_value",
                "default_en" => "interest value",
                "default_ar" => "قيمة الفايدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Installment Payment Plan Detail"
            ],
        ]);
        //Archive document
        Translation::insert([
            [
                "key" => "arch_document_create_form",
                "default_en" => "Add new archiving document",
                "default_ar" => "اضف وثيقة ارشفة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch documents"
            ],
            [
                "key" => "arch_document_edit_form",
                "default_en" => "Edit archiving document form",
                "default_ar" => "نموذج تعديلة وثيقة ارشفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch documents"
            ],
            [
                "key" => "arch_document_type",
                "default_en" => "Archiving document type name",
                "default_ar" => "اسم نوع وثيقة الارشفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch documents"
            ],
            [
                "key" => "arch_document_status",
                "default_en" => "Archiving document status name",
                "default_ar" => "اسم حالة وثيقة الارشفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch documents"
            ],
            [
                "key" => "arch_document_description",
                "default_en" => "Archiving document description",
                "default_ar" => "وصف وثيقة الارشفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch documents"
            ],
            [
                "key" => "arch_document_url_reference",
                "default_en" => "Archiving document url reference",
                "default_ar" => "الرابط المرجعي لوثيقة الارشفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch documents"
            ],
        ]);
        //Arch doc status
        Translation::insert([
            [
                "key" => "arch_document_status_create_form",
                "default_en" => "Add new archiving document status",
                "default_ar" => "اضف حالة وثيقة ارشفة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc status"
            ],
            [
                "key" => "arch_document_status_edit_form",
                "default_en" => "Edit archiving document status form",
                "default_ar" => "نموذج تعديل حالة وثيقة ارشفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc status"
            ],
            [
                "key" => "arch_document_status_name_ar",
                "default_en" => "Archiving document status name (arabic)",
                "default_ar" => "اسم حالة وثيقة ارشفة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc status"
            ],
            [
                "key" => "arch_document_status_name_en",
                "default_en" => "Archiving document status name (english)",
                "default_ar" => "اسم حالة وثيقة ارشفة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc status"
            ],
        ]);
        //Arch doc type
        Translation::insert([
            [
                "key" => "arch_document_type_create_form",
                "default_en" => "Add new archiving document type",
                "default_ar" => "اضف نوع وثيقة ارشفة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "arch_document_type_edit_form",
                "default_en" => "Edit archiving document type form",
                "default_ar" => "نموذج تعديل نوع وثيقة ارشفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "arch_document_type_name_ar",
                "default_en" => "Archiving document type name (arabic)",
                "default_ar" => "اسم نوع وثيقة ارشفة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "arch_document_type_name_en",
                "default_en" => "Archiving document type name (english)",
                "default_ar" => "اسم نوع وثيقة ارشفة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "parent",
                "default_en" => "parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "is_valid",
                "default_en" => "Is valid",
                "default_ar" => "هل صالح",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
        ]);
        //document field
        Translation::insert([
            [
                "key" => "documentFieldName",
                "default_en" => "Document Field Name (arabic)",
                "default_ar" => "اسم حقل المستند (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "documentFieldNameE",
                "default_en" => "Document Field Name (english)",
                "default_ar" => "اسم حقل المستند (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "document-field-type",
                "default_en" => "Document Field type",
                "default_ar" => "نوع حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "document-field-lookup",
                "default_en" => "Document Field Lookup Table",
                "default_ar" => "بحث  حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "document-field-lookup_column",
                "default_en" => "Document Field Lookup Column",
                "default_ar" => "اسم العمود في  حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "document-field-reference",
                "default_en" => "Document Field Reference",
                "default_ar" => "مرجع حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "documentFieldCreate",
                "default_en" => "Add new Document Field",
                "default_ar" => "اضافة مستند جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "property",
                "default_en" => "Property name",
                "default_ar" => "اسم الخاصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
            [
                "key" => "documentFieldEdit",
                "default_en" => "Edit Document Field",
                "default_ar" => "نموذج تعديل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document field"
            ],
        ]);
        //Arch Department Table
        Translation::insert([
            [
                "key" => "ArchiveDepartmentCreate",
                "default_en" => "Add new Archive Department",
                "default_ar" => "اضافة قسم الأرشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch departments"
            ],
            [
                "key" => "ArchiveDepartmentEdit",
                "default_en" => "Edit Archive Department",
                "default_ar" => "نموذج تعديل قسم الأرشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch departments"
            ],
            [
                "key" => "archive_department_id",
                "default_en" => "Archive Department",
                "default_ar" => "قسم الأرشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch departments"
            ],
            [
                "key" => "archive_name",
                "default_en" => "name (arabic)",
                "default_ar" => "اسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch departments"
            ],
            [
                "key" => "archive_name_e",
                "default_en" => "name (english)",
                "default_ar" => "اسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch departments"
            ],
            [
                "key" => "archive_status",
                "default_en" => "department status",
                "default_ar" => "حالة القسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch departments"
            ]
        ]);
        //Arch doc type field Table
        Translation::insert([
            [
                "key" => "arch_doc_type_field_create_form",
                "default_en" => "Add new archiving document type field",
                "default_ar" => "إضافة حقل نوع مستند أرشفة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc type fields"
            ],
            [
                "key" => "arch_doc_type_field_edit_form",
                "default_en" => "Edit archiving document type field",
                "default_ar" => "تعديل حقل نوع مستند أرشفة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc type fields"
            ],
            [
                "key" => "arch_doc_type",
                "default_en" => "Archiving document type name",
                "default_ar" => "اسم نوع الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc type fields"
            ],
            [
                "key" => "arch_doc_field",
                "default_en" => "Archiving document field name",
                "default_ar" => "اسم حقل الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc type fields"
            ],

            [
                "key" => "arch_doc_field_order",
                "default_en" => "Archiving document Sort field",
                "default_ar" => "ترتيب حقل الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc type fields"
            ],

            [
                "key" => "arch_doc_field_character",
                "default_en" => "Archiving document field character",
                "default_ar" => "حرف حقل الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc type fields"
            ],
            [
                "key" => "is_required",
                "default_en" => "Is required",
                "default_ar" => "مطلوب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "arch doc type fields"
            ],
        ]);
        //Archiving
        Translation::insert([
            [
                "key" => "archive_create_form",
                "default_en" => "Add new archiving",
                "default_ar" => "اضافة ارشيف جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving screen"
            ],
            [
                "key" => "archive_edit_form",
                "default_en" => "Edit archive",
                "default_ar" => "تعديل الارشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving screen"
            ],
            [
                "key" => "arch_doc_type",
                "default_en" => "Archiving document type name",
                "default_ar" => "اسم نوع ملف الارشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving screen"
            ],
            [
                "key" => "arch_doc_status",
                "default_en" => "Archiving document status name",
                "default_ar" => "اسم حالة ملف الارشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving screen"
            ],
            [
                "key" => "archive_file_number",
                "default_en" => "Archive file number",
                "default_ar" => "رقم ملف الارشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving screen"
            ],
            [
                "key" => "archive_description",
                "default_en" => "Archive description",
                "default_ar" => "وصف الارشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving screen"
            ],
            [
                "key" => "archive_timestamp",
                "default_en" => "Archive timestamp",
                "default_ar" => "تاريخ الارشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving screen"
            ],
        ]);
        //Document Department Table
        Translation::insert([
            [
                "key" => "document_department_create_form",
                "default_en" => "Add new document department",
                "default_ar" => "إضافة قسم للمستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "document_department_edit_form",
                "default_en" => "Edit document department",
                "default_ar" => "تعديل قسم المستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "department",
                "default_en" => "department",
                "default_ar" => "القسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],
            [
                "key" => "document",
                "default_en" => "document",
                "default_ar" => "المستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "gen arch doc types"
            ],

        ]);

        //Screen property
        Translation::insert([
            [
                "key" => "screen_property_create_form",
                "default_en" => "Add new screen property form",
                "default_ar" => "اضف خاصية وشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties",
            ],
            [
                "key" => "screen_property_edit_form",
                "default_en" => "Edit screen property form",
                "default_ar" => "نموذج تعديل الخاصية والشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties",

            ],
            // [
            //     "key" => "screen",
            //     "default_en" => "Screen Name",
            //     "default_ar" => "اسم الشاشة",
            //     "new_ar" => "",
            //     "new_en" => "",
            //     "company_id" => 0,
            //     "screen" => "properties",

            // ],
            [
                "key" => "property",
                "default_en" => "Property name",
                "default_ar" => "اسم الخاصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties",

            ],
        ]);
        //Tree property
        Translation::insert([
            [
                "key" => "property_create_form",
                "default_en" => "Add new property form",
                "default_ar" => "اضف خاصية جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties"
            ],
            [
                "key" => "property_edit_form",
                "default_en" => "Edit property form",
                "default_ar" => "نموذج تعديل الخاصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties"
            ],
            [
                "key" => "property_name_ar",
                "default_en" => "Property name (arabic)",
                "default_ar" => "اسم الخاصية (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties"

            ],
            [
                "key" => "property_name_en",
                "default_en" => "Property name (english)",
                "default_ar" => "اسم الخاصية (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties"
            ],
            [
                "key" => "required",
                "default_en" => "required",
                "default_ar" => "مطلوب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties"
            ],
            [
                "key" => "parent",
                "default_en" => "Parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "properties"
            ],
        ]);

        //Sales men types
        Translation::insert([
            // [
            //     "key" => "sale_man_type_create_form",
            //     "default_en" => "Add new sale man type",
            //     "default_ar" => "اضف نوع رجل مبيعات جديد",
            //     "new_ar" => "",
            //     "new_en" => "",
            //     "company_id" => 0,
            //     "screen" => "sales men"
            // ],
            // [
            //     "key" => "sale_man_type_edit_form",
            //     "default_en" => "Edit sale man type form",
            //     "default_ar" => "نموذج تعديل نوع رجل المبيعات",
            //     "new_ar" => "",
            //     "new_en" => "",
            //     "company_id" => 0,
            //     "screen" => "sales men"
            // ],
            // [
            //     "key" => "sale_man_type_name_ar",
            //     "default_en" => "Sale man type name (arabic)",
            //     "default_ar" => "اسم نوع رجل المبيعات (عربي)",
            //     "new_ar" => "",
            //     "new_en" => "",
            //     "company_id" => 0,
            //     "screen" => "sales men"
            // ],
            // [
            //     "key" => "sale_man_type_name_en",
            //     "default_en" => "Sale man type name (english)",
            //     "default_ar" => "اسم نوع رجل المبيعات (انجليزي)",
            //     "new_ar" => "",
            //     "new_en" => "",
            //     "company_id" => 0,
            //     "screen" => "sales men"
            // ],
            // [
            //     "key" => "is_employee",
            //     "default_en" => "Is employee",
            //     "default_ar" => "هل موظف",
            //     "new_ar" => "",
            //     "new_en" => "",
            //     "company_id" => 0,
            //     "screen" => "sales men"
            // ],
        ]);
        //Sales man
        Translation::insert([
            [
                "key" => "sale_man_create_form",
                "default_en" => "Add new sale man",
                "default_ar" => "اضف رجل مبيعات جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "manager",
                "default_en" => "Manager name",
                "default_ar" => "اسم المدير",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "is_salesman",
                "default_en" => "is salesman",
                "default_ar" => "رجل مبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "for_all_customer",
                "default_en" => "For all customer",
                "default_ar" => "لجميع العملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "customer_handel",
                "default_en" => "Customer handle",
                "default_ar" => "التعامل مع العملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"

            ],
            [
                "key" => "plan",
                "default_en" => "Plan name",
                "default_ar" => "اسم الخطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_edit_form",
                "default_en" => "Edit sale man form",
                "default_ar" => "نموذج تعديل رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_name_ar",
                "default_en" => "Sale man name (arabic)",
                "default_ar" => "اسم رجل المبيعات (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_name_en",
                "default_en" => "Sale man name (english)",
                "default_ar" => "اسم رجل المبيعات (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "sale_man_type",
                "default_en" => "Sale man type",
                "default_ar" => "نوع رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
        ]);
        //External sale man
        // Translation::insert([
        //     [
        //         "key" => "external_sale_man_create_form",
        //         "default_en" => "Add new external sale man",
        //         "default_ar" => "اضف رجل مبيعات خارجي جديد",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"
        //     ],
        //     [
        //         "key" => "external_sale_man_edit_form",
        //         "default_en" => "Edit external sale man form",
        //         "default_ar" => "نموذج تعديل رجل مبيعات خارجي",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"
        //     ],
        //     [
        //         "key" => "external_sale_man_phone",
        //         "default_en" => "External Sale man phone",
        //         "default_ar" => "هاتف رجل المبيعات الخارجي",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"
        //     ],
        //     [
        //         "key" => "external_sale_man_email",
        //         "default_en" => "External Sale man email",
        //         "default_ar" => "البريد الالكتروني لرجل المبيعات الخارجي",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"

        //     ],
        //     [
        //         "key" => "country",
        //         "default_en" => "Country name",
        //         "default_ar" => "اسم الدولة",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"
        //     ],
        //     [
        //         "key" => "external_sale_man_national_id",
        //         "default_en" => "External Sale man national id",
        //         "default_ar" => "الرقم القومي لرجل المبيعات الخارجي",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"

        //     ],
        //     [
        //         "key" => "external_sale_man_address",
        //         "default_en" => "External Sale man address",
        //         "default_ar" => "عنوان رجل المبيعات الخارجي",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"

        //     ],
        //     [
        //         "key" => "external_sale_man_code",
        //         "default_en" => "External Sale man code",
        //         "default_ar" => "كود رجل المبيعات الخارجي",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"

        //     ],
        //     [
        //         "key" => "external_sale_man_status",
        //         "default_en" => "External Sale man status",
        //         "default_ar" => "حالة رجل المبيعات الخارجي",
        //         "new_ar" => "",
        //         "new_en" => "",
        //         "company_id" => 0,
        //         "screen" => "sales men"

        //     ],
        // ]);
        //Internal sale man
        Translation::insert([
            [
                "key" => "internal_sale_man_create_form",
                "default_en" => "Add new internal sale man",
                "default_ar" => "اضف رجل مبيعات داخلي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "internal_sale_man_edit_form",
                "default_en" => "Edit internal sale man form",
                "default_ar" => "نموذج تعديل رجل مبيعات داخلي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
            [
                "key" => "internal_sale_man_status",
                "default_en" => "Internal sale man status",
                "default_ar" => "حالة رجل المبيعات الداخلي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sales men"
            ],
        ]);

        //Payment type
        Translation::insert([
            [
                "key" => "payment_type_create_form",
                "default_en" => "Add new payment type",
                "default_ar" => "اضف نوع دفع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "payment_type_edit_form",
                "default_en" => "Edit payment type form",
                "default_ar" => " نموذج تعديل نوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "payment_type_name_ar",
                "default_en" => "Payment type name (arabic)",
                "default_ar" => "اسم نوع الدفع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"

            ],
            [
                "key" => "payment_type_name_en",
                "default_en" => "Payment type name (english)",
                "default_ar" => "اسم نوع الدفع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"

            ],
            [
                "key" => "payment_type_default",
                "default_en" => "Payment type default",
                "default_ar" => "الحالة الافتراضية لنوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"

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
                "screen" => "bank"
            ],
            [
                "key" => "bank_edit_form",
                "default_en" => "Edit bank form",
                "default_ar" => " نموذج تعديل البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank_name_ar",
                "default_en" => "Bank name (arabic)",
                "default_ar" => "اسم البنك (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank_name_en",
                "default_en" => "Bank name (english)",
                "default_ar" => "اسم البنك (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"

            ],
            [
                "key" => "country",
                "default_en" => "Coutnry name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"

            ],
            [
                "key" => "bank_swiftcode",
                "default_en" => "Bank swiftcode",
                "default_ar" => "سويفت كود البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
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
                "screen" => "bank"
            ],
            [
                "key" => "bank_account_edit_form",
                "default_en" => "Edit bank bank form",
                "default_ar" => " نموذج تعديل الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank",
                "default_en" => "Bank name",
                "default_ar" => "اسم البنك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank_account_number",
                "default_en" => "Bank account number",
                "default_ar" => "رقم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank_account_phone",
                "default_en" => "Bank account phone",
                "default_ar" => "هاتف الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank_account_address",
                "default_en" => "Bank account address",
                "default_ar" => "عنوان الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank_account_email",
                "default_en" => "Bank account email",
                "default_ar" => "البريد الالكتروني للحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],

            [
                "key" => "bank_account_employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
            ],
            [
                "key" => "bank_account_rpcode",
                "default_en" => "Bank account rpcode",
                "default_ar" => "ار بي كود الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "bank"
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
                "screen" => "area"
            ],
            [
                "key" => "country_edit_form",
                "default_en" => "Edit country form",
                "default_ar" => "نموذج تعديل الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_name_ar",
                "default_en" => "Country name (arabic)",
                "default_ar" => "اسم الدولة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_name_en",
                "default_en" => "Country name (english)",
                "default_ar" => "اسم الدولة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_long_name_ar",
                "default_en" => "Country long name (arabic)",
                "default_ar" => "اسم الدولة طويل (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_long_name_en",
                "default_en" => "Country long name (english)",
                "default_ar" => "اسم الدولة طويل (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_phone_key",
                "default_en" => "Country phone key",
                "default_ar" => "كود هاتف الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_short_code",
                "default_en" => "Country web short code",
                "default_ar" => "كود الويب المختصر للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_default",
                "default_en" => "Country default",
                "default_ar" => "الحالة الافتراضية للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_status",
                "default_en" => "Country status",
                "default_ar" => "حالة الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country_national_id",
                "default_en" => "Country national id length",
                "default_ar" => "طول الرقم القومي للدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
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
                "screen" => "area"
            ],
            [
                "key" => "governorate_edit_form",
                "default_en" => "Edit governorate form",
                "default_ar" => "نموذج تعديل المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "governorate_name_ar",
                "default_en" => "Governorate name (arabic)",
                "default_ar" => "اسم المحافظة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "governorate_name_en",
                "default_en" => "Governorate name (english)",
                "default_ar" => "اسم المحافظة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "governorate_phone_key",
                "default_en" => "Governorate phone key",
                "default_ar" => "كود هاتف المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "governorate_default",
                "default_en" => "Governorate default",
                "default_ar" => "الحالة الافتراضية للمحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "governorate_status",
                "default_en" => "Governorate status",
                "default_ar" => "حالة المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
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
                "screen" => "area"
            ],
            [
                "key" => "city_edit_form",
                "default_en" => "Edit city form",
                "default_ar" => "نموذج تعديل المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "city_name_ar",
                "default_en" => "City name (arabic)",
                "default_ar" => "اسم المدينة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "city_name_en",
                "default_en" => "City name (english)",
                "default_ar" => "اسم المدينة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "city_status",
                "default_en" => "City status",
                "default_ar" => "حالة المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
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
                "screen" => "area"
            ],
            [
                "key" => "avenue_edit_form",
                "default_en" => "Edit avenue form",
                "default_ar" => "نموذج تعديل المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "avenue_name_ar",
                "default_en" => "Avenue name (arabic)",
                "default_ar" => "اسم المنطقة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "avenue_name_en",
                "default_en" => "Avenue name (english)",
                "default_ar" => "اسم المنطقة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "avenue_status",
                "default_en" => "Avenue status",
                "default_ar" => "حالة المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "area"
            ],
        ]);

        //Role type
        Translation::insert([
            [
                "key" => "role_type_create_form",
                "default_en" => "Add new role type",
                "default_ar" => "اضف نوع دور جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_type_edit_form",
                "default_en" => "Edit role type form",
                "default_ar" => "نموذج تعديل نوع الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_type_name_ar",
                "default_en" => "Role type name (arabic)",
                "default_ar" => "اسم نوع الدور (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_type_name_en",
                "default_en" => "Role type name (english)",
                "default_ar" => "اسم نوع الدور (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
        ]);
        //Role
        Translation::insert([
            [
                "key" => "role_create_form",
                "default_en" => "Add new role",
                "default_ar" => "اضف دور جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_edit_form",
                "default_en" => "Edit role form",
                "default_ar" => "نموذج تعديل الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_name_ar",
                "default_en" => "Role name (arabic)",
                "default_ar" => "اسم الدور (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_name_en",
                "default_en" => "Role name (english)",
                "default_ar" => "اسم الدور (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
        ]);
        //Role workflow
        Translation::insert([
            [
                "key" => "role_workflow_create_form",
                "default_en" => "Add new role workflow",
                "default_ar" => "اضف دور سير عمل جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_workflow_edit_form",
                "default_en" => "Edit role workflow form",
                "default_ar" => "نموذج تعديل دور سير العمل ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "workflow",
                "default_en" => "Workflow name",
                "default_ar" => "اسم سير العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
        ]);
        //Role workflow button
        Translation::insert([
            [
                "key" => "role_workflow_button_create_form",
                "default_en" => "Add new role workflow button",
                "default_ar" => "اضف دور سير عمل زر جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_workflow_button_edit_form",
                "default_en" => "Edit role workflow button form",
                "default_ar" => "نموذج تعديل دور سير العمل والزرار ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "button",
                "default_en" => "Button name",
                "default_ar" => "اسم الزرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "workflow",
                "default_en" => "Workflow name",
                "default_ar" => "اسم سير العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
        ]);
        //Role hotfield screen
        Translation::insert([
            [
                "key" => "role_hotfield_screen_create_form",
                "default_en" => "Add new role hotfield screen",
                "default_ar" => "اضف الدور مع الحقل والشاشة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role",
            ],
            [
                "key" => "role_hotfield_screen_edit_form",
                "default_en" => "Edit role form",
                "default_ar" => "نموذج تعديل الدور مع الحقل والشاشة ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role",
            ],
            [
                "key" => "hotfield",
                "default_en" => "Hotfield name",
                "default_ar" => "اسم الحقل الساخن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role",
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "workflow",
                "default_en" => "Workflow name",
                "default_ar" => "اسم سير العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
        ]);
        //Role user
        Translation::insert([
            [
                "key" => "role_user_create_form",
                "default_en" => "Add new role user",
                "default_ar" => "اضف دور للمستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role_user_edit_form",
                "default_en" => "Edit role user",
                "default_ar" => "تعديل دور المستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
            [
                "key" => "user",
                "default_en" => "User name",
                "default_ar" => "اسم المستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role"
            ],
        ]);

        //Contract unit
        Translation::insert([
            [
                "key" => "contractunit_create_form",
                "default_en" => "Add new contract unit form",
                "default_ar" => "اضف وحدة عقد جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "contractunit_edit_form",
                "default_en" => "Edit contract unit form",
                "default_ar" => "نموذج تعديل وحدة عقد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //Contract
        Translation::insert([
            [
                "key" => "contract_create_form",
                "default_en" => "Add new contract",
                "default_ar" => "اضف عقد جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "contract_edit_form",
                "default_en" => "Edit contract form",
                "default_ar" => "تعديل نموذج العقد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer",
                "default_en" => "Customer name",
                "default_ar" => "اسم العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "contract_date",
                "default_en" => "Contract date",
                "default_ar" => "تاريخ العقد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "sale_man",
                "default_en" => "Sale man name",
                "default_ar" => "اسم رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building",
                "default_en" => "Building name",
                "default_ar" => "اسم المبنى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "unit",
                "default_en" => "Unit name",
                "default_ar" => "اسم الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "reservation_date",
                "default_en" => "Reservation date",
                "default_ar" => "تاريخ الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //Invoice
        Translation::insert([
            [
                "key" => "invoice_create_form",
                "default_en" => "Add new invoice",
                "default_ar" => "اضف فاتورة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "invoice_edit_form",
                "default_en" => "Edit invoice form",
                "default_ar" => "تعديل الفاتورة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "serial",
                "default_en" => "Serial",
                "default_ar" => "التسلسل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //real estate units
        Translation::insert([
            [
                "key" => "realEstate_unit_create_form",
                "default_en" => "Add New Unit",
                "default_ar" => "اضف وحده جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "realEstate_unit_edit_form",
                "default_en" => "Edit Unit form",
                "default_ar" => "نموذج تعديل الوحده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_name_ar",
                "default_en" => "Unit Name (arabic)",
                "default_ar" => "اسم وحده (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_name_en",
                "default_en" => "Unit Name (english)",
                "default_ar" => "اسم وحده (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_description_ar",
                "default_en" => "Unit Description (arabic)",
                "default_ar" => "وصف  الوحده (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_description_en",
                "default_en" => "Unit Description (english)",
                "default_ar" => "وصف الوحده (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_code",
                "default_en" => "Unit Code",
                "default_ar" => "كود الوحده (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_unit_ty",
                "default_en" => "Unit Ty ",
                "default_ar" => "نوع الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_status_date",
                "default_en" => "Status Date ",
                "default_ar" => "وقت الحاله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_unit_area",
                "default_en" => "Unit Area ",
                "default_ar" => "مساحه الوحده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_building",
                "default_en" => "Unit building ",
                "default_ar" => "وحده المبنى (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_owner",
                "default_en" => "Unit Owner ",
                "default_ar" => "مالك المبني (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_currency",
                "default_en" => "Unit Currency ",
                "default_ar" => "عمله الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_wallet",
                "default_en" => "Unit wallet ",
                "default_ar" => "محفظه الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_wallet",
                "default_en" => "Unit wallet ",
                "default_ar" => "محفظه الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_status",
                "default_en" => "Unit Status ",
                "default_ar" => "حاله الوحده (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_price",
                "default_en" => "price ",
                "default_ar" => "سعر (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_commission_ty",
                "default_en" => "Commission ty ",
                "default_ar" => "Commission Ty ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_commission_value",
                "default_en" => "Commission Value ",
                "default_ar" => "قيمة العموله (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_commission_ty",
                "default_en" => "Commission ty ",
                "default_ar" => "Commission Ty",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_rooms",
                "default_en" => "Rooms ",
                "default_ar" => "الغرف (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_path",
                "default_en" => "Path ",
                "default_ar" => "حمام (عقارات) ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_floor",
                "default_en" => "Floor ",
                "default_ar" => "الارضيه (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_view",
                "default_en" => "View ",
                "default_ar" => "المنظر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_rank",
                "default_en" => "Rank ",
                "default_ar" => "مرتبه (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "realEstate_unit_module",
                "default_en" => "Module ",
                "default_ar" => "موديول (عقارات)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ]
        ]);
        //Owners
        Translation::insert([
            [
                "key" => "owner_create_form",
                "default_en" => "Add new owner",
                "default_ar" => "اضف مالك جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "owner_edit_form",
                "default_en" => "Edit owner form",
                "default_ar" => "نموذج تعديل المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_name_ar",
                "default_en" => "Owner name (arabic)",
                "default_ar" => "اسم المالك (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_name_en",
                "default_en" => "Owner name (english)",
                "default_ar" => "اسم المالك (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_phone",
                "default_en" => "Owner phone",
                "default_ar" => "هاتف المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_email",
                "default_en" => "Owner email",
                "default_ar" => "بريد الكترون المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_nationality",
                "default_en" => "Owner nationality",
                "default_ar" => "جنسية المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_national_id",
                "default_en" => "Owner national id",
                "default_ar" => "الرقم القومي للمالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_contact_person",
                "default_en" => "Other contact person",
                "default_ar" => "جهة اتصال أخرى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_contact_phones",
                "default_en" => "Other contract phone",
                "default_ar" => "هاتف اتصال اخر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "bank_account",
                "default_en" => "Bank account name",
                "default_ar" => "اسم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_whatsapp",
                "default_en" => "Owner whatsapp",
                "default_ar" => "رقم واتساب المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "owner_code",
                "default_en" => "Owner code",
                "default_ar" => "كود المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //Buildings
        Translation::insert([
            [
                "key" => "building_create_form",
                "default_en" => "Add new building",
                "default_ar" => "اضف بناء جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "building_edit_form",
                "default_en" => "Edit building form",
                "default_ar" => "نموذج تعديل البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_name_ar",
                "default_en" => "Building name (arabic)",
                "default_ar" => "اسم البناء (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_name_en",
                "default_en" => "Building name (english)",
                "default_ar" => "اسم البناء (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_description_ar",
                "default_en" => "Building description (arabic)",
                "default_ar" => "وصف البناء (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_description_en",
                "default_en" => "Building description (english)",
                "default_ar" => "وصف البناء (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_area",
                "default_en" => "Building area (m)",
                "default_ar" => "مساحة البناء (متر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_land_area",
                "default_en" => "Building land area (m)",
                "default_ar" => "مساحةارض البناء (متر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_construction_year",
                "default_en" => "Building construction year",
                "default_ar" => "سنة تاسيس البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "module",
                "default_en" => "Module name",
                "default_ar" => "اسم الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],

            [
                "key" => "governorate",
                "default_en" => "Governorate name",
                "default_ar" => "اسم المحافظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],

            [
                "key" => "avenue",
                "default_en" => "Avenue name",
                "default_ar" => "اسم المنطقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_longitude",
                "default_en" => "Building longitude",
                "default_ar" => "خطوط طول المبنى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "building_latitude",
                "default_en" => "Building latitude",
                "default_ar" => "خطوط عرض المبنى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //Customers
        Translation::insert([
            [
                "key" => "customer_create_form",
                "default_en" => "Add new customer",
                "default_ar" => "اضف زبون جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "customer_edit_form",
                "default_en" => "Edit customer form",
                "default_ar" => "نموذج تعديل العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_name_ar",
                "default_en" => "Customer name (arabic)",
                "default_ar" => "اسم العميل (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_name_en",
                "default_en" => "Customer name (english)",
                "default_ar" => "اسم العميل (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_phone",
                "default_en" => "Customer phone",
                "default_ar" => "هاتف العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_email",
                "default_en" => "Customer email",
                "default_ar" => "بريد العميل الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_nationality",
                "default_en" => "Customer nationality",
                "default_ar" => "جنسية العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_national_id",
                "default_en" => "Customer national id",
                "default_ar" => "الرقم القومي للزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_contact_person",
                "default_en" => "Customer contact person",
                "default_ar" => "معلومات العميل الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_contact_phones",
                "default_en" => "Customer contact phones",
                "default_ar" => "هواتف اتصال العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_whatsapp",
                "default_en" => "Customer whatsapp",
                "default_ar" => "رقم واتساب العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_code",
                "default_en" => "Customer code",
                "default_ar" => "كود العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "customer_passport_number",
                "default_en" => "Customer passport number",
                "default_ar" => "رقم جواز سفر العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //reservation
        Translation::insert([
            [
                "key" => "reservation_create_form",
                "default_en" => "Add new reservation",
                "default_ar" => "اضف حجز جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "reservation_edit_form",
                "default_en" => "Edit reservation form",
                "default_ar" => "تعديل نموذج الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "reservation_date",
                "default_en" => "Reservation date",
                "default_ar" => "تاريخ الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "customer",
                "default_en" => "Customer name",
                "default_ar" => "اسم العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "sale_man",
                "default_en" => "Sale man name",
                "default_ar" => "اسم رجل المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "payment_plan",
                "default_en" => "Payment Plan",
                "default_ar" => "خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //reservation
        Translation::insert([
            [
                "key" => "invoice_create_form",
                "default_en" => "Add new invoice",
                "default_ar" => "اضف فاتورة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "invoice_edit_form",
                "default_en" => "Edit invoice form",
                "default_ar" => "تعديل الفاتورة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "invoice_date",
                "default_en" => "Invoice date",
                "default_ar" => "تاريخ الفاتورة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "item",
                "default_en" => "Item",
                "default_ar" => "الصنف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "invoice_amount",
                "default_en" => "Amount",
                "default_ar" => "المبلغ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "invoice_quantity",
                "default_en" => "Quantity",
                "default_ar" => "الكمية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Building wallet
        Translation::insert([
            [
                "key" => "building_wallet_create_form",
                "default_en" => "Add new building wallet",
                "default_ar" => "اضافة محفظة بناء جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "building_wallet_edit_form",
                "default_en" => "Edit building wallet form",
                "default_ar" => "نموذج تعديل محفظة البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "building",
                "default_en" => "Building name",
                "default_ar" => "اسم البناء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "wallet",
                "default_en" => "Wallet name",
                "default_ar" => "اسم المحفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "building_wallet_bu_ty",
                "default_en" => "Buty",
                "default_ar" => "الزبد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ]
        ]);
        //Wallet
        Translation::insert([
            [
                "key" => "wallet_create_form",
                "default_en" => "Add new wallet",
                "default_ar" => "اضافة محفظة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "wallet_edit_form",
                "default_en" => "Edit wallet form",
                "default_ar" => "نموذج تعديل محفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "wallet_name_ar",
                "default_en" => "Wallet name (arabic)",
                "default_ar" => "اسم المحفظة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "wallet_name_en",
                "default_en" => "Wallet name (english)",
                "default_ar" => "اسم المحفظة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);
        //Wallet owner
        Translation::insert([
            [
                "key" => "wallet_owner_create_form",
                "default_en" => "Add new wallet owner",
                "default_ar" => "اضافة محفظة مالك جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "wallet_owner_edit_form",
                "default_en" => "Edit wallet owner form",
                "default_ar" => "نموذج تعديل محفظة المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "wallet",
                "default_en" => "Wallet name",
                "default_ar" => "اسم المحفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "owner",
                "default_en" => "Owner name",
                "default_ar" => "اسم المالك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "wallet_owner_percentage",
                "default_en" => "Wallet owner percentage",
                "default_ar" => "النسبة المئوية لمالك المحفظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Unit status
        Translation::insert([
            [
                "key" => "unitstatus_create_form",
                "default_en" => "Add new unit status",
                "default_ar" => "اضف حالة وحدة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unitstatus_edit_form",
                "default_en" => "Edit unit status form",
                "default_ar" => "نموذج تعديل حالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "unitstatus_name_ar",
                "default_en" => "Unit status name (arabic)",
                "default_ar" => "اسم حالة الوحدة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "unitstatus_name_en",
                "default_en" => "Unit status name (english)",
                "default_ar" => "اسم حالة الوحدة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "unitstatus_status",
                "default_en" => "Unit status",
                "default_ar" => "حالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "unitstatus_default",
                "default_en" => "Unit status default",
                "default_ar" => "الحالة الافتراضية لحالة الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
        ]);

        //document field
        Translation::insert([
            [
                "key" => "documentFieldName",
                "default_en" => "Document Field Name (arabic)",
                "default_ar" => "اسم حقل المستند (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "documentFieldNameE",
                "default_en" => "Document Field Name (english)",
                "default_ar" => "اسم حقل المستند (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "document-field-type",
                "default_en" => "Document Field type",
                "default_ar" => "نوع حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "document-field-lookup",
                "default_en" => "Document Field Lookup Table",
                "default_ar" => "بحث  حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "document-field-lookup_column",
                "default_en" => "Document Field Lookup Column",
                "default_ar" => "اسم العمود في  حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "document-field-reference",
                "default_en" => "Document Field Reference",
                "default_ar" => "مرجع حقل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "documentFieldCreate",
                "default_en" => "Add new Document Field",
                "default_ar" => "اضافة مستند جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "documentFieldEdit",
                "default_en" => "Edit Document Field",
                "default_ar" => "نموذج تعديل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
        ]);
        //Arch Department Table
        Translation::insert([
            [
                "key" => "ArchiveDepartmentCreate",
                "default_en" => "Add new Archive Department",
                "default_ar" => "اضافة قسم الأرشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "ArchiveDepartmentEdit",
                "default_en" => "Edit Archive Department",
                "default_ar" => "نموذج تعديل قسم الأرشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "archive_department_id",
                "default_en" => "Archive Department",
                "default_ar" => "قسم الأرشيف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "archive_name",
                "default_en" => "name (arabic)",
                "default_ar" => "اسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "archive_name_e",
                "default_en" => "name (english)",
                "default_ar" => "اسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "archive_status",
                "default_en" => "department status",
                "default_ar" => "حالة القسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ]
        ]);
        //Document Department Table
        Translation::insert([
            [
                "key" => "document_department_create_form",
                "default_en" => "Add new document department",
                "default_ar" => "إضافة قسم للمستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "document_department_edit_form",
                "default_en" => "Edit document department",
                "default_ar" => "تعديل قسم المستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "department",
                "default_en" => "department",
                "default_ar" => "القسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "document",
                "default_en" => "document",
                "default_ar" => "المستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],

        ]);
        //Arch doc type field Table
        Translation::insert([
            [
                "key" => "arch_doc_type_field_create_form",
                "default_en" => "Add new archiving document type field",
                "default_ar" => "إضافة حقل نوع مستند أرشفة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "arch_doc_type_field_edit_form",
                "default_en" => "Edit archiving document type field",
                "default_ar" => "تعديل حقل نوع مستند أرشفة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "arch_doc_type",
                "default_en" => "Archiving document type name",
                "default_ar" => "اسم نوع الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "arch_doc_field",
                "default_en" => "Archiving document field name",
                "default_ar" => "اسم حقل الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],

            [
                "key" => "arch_doc_field_order",
                "default_en" => "Archiving document Sort field",
                "default_ar" => "ترتيب حقل الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],

            [
                "key" => "arch_doc_field_character",
                "default_en" => "Archiving document field character",
                "default_ar" => "حرف حقل الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
            [
                "key" => "is_required",
                "default_en" => "Is required",
                "default_ar" => "مطلوب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "archiving"
            ],
        ]);

        //Payment plan installment
        Translation::insert([
            [
                "key" => "payment_plan_installment_create_form",
                "default_en" => "Add new payment plan installment",
                "default_ar" => "إضافة خطة دفع جديدة بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "payment_plan_installment_edit_form",
                "default_en" => "Edit payment plan installment form",
                "default_ar" => "نموذج تعديل التقسيط لخطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan",
                "default_en" => "installment payment plan name",
                "default_ar" => "اسم خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_detail",
                "default_en" => "Installment payment plan detail",
                "default_ar" => "تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_v_date",
                "default_en" => "payment plan v-date",
                "default_ar" => "التاريخ الخامس لتفاصيل خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

            [
                "key" => "installment_payment_plan_due_date",
                "default_en" => "payment plan due date",
                "default_ar" => "تاريخ استحقاق خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_total_amount",
                "default_en" => "total amount",
                "default_ar" => "المبلغ الاجمالي ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_paid_amount",
                "default_en" => "paid amount",
                "default_ar" => "المبلغ المدفوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status",
                "default_en" => "Installment status name",
                "default_ar" => "اسم حالة التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "doc_type",
                "default_en" => "Document type name",
                "default_ar" => "اسم نوع الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "module",
                "default_en" => "Module name",
                "default_ar" => "اسم الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "screen",
                "default_en" => "Screen name",
                "default_ar" => "اسم الشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "ref_id",
                "default_en" => "Ref ID",
                "default_ar" => "رقم الملف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "rp_code",
                "default_en" => "rp code",
                "default_ar" => "ترميز rp",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Status
        Translation::insert([
            [
                "key" => "installment_status_create_form",
                "default_en" => "Add new Installment Status",
                "default_ar" => "اضافة حاله تقسيط جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_edit_form",
                "default_en" => "Edit  Installment Status",
                "default_ar" => "نموذج تعديل حاله التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_name",
                "default_en" => "Installment Status name (arabic)",
                "default_ar" => "اسم حاله التقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_name_e",
                "default_en" => "installment_status name (english)",
                "default_ar" => "اسم حاله التقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_status_default",
                "default_en" => "Installment Status Default",
                "default_ar" => "الحالة الافتراضية لحاله التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Payment Type
        Translation::insert([
            [
                "key" => "installment_payment_type_create_form",
                "default_en" => "Add new Installment Payment Type",
                "default_ar" => "اضافة نوع دفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_edit_form",
                "default_en" => "Edit  Installment Payment Type",
                "default_ar" => "نموذج تعديل نوع الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_name",
                "default_en" => "Installment Payment Type name (arabic)",
                "default_ar" => "اسم نوع دفع بالتقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_name_e",
                "default_en" => "Installment Payment Type name (english)",
                "default_ar" => "اسم نوع دفع بالتقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_auto_freq",
                "default_en" => "Installment Status Auto Freq",
                "default_ar" => "حالة تقسيط التكرار التلقائي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_is_partially",
                "default_en" => "Installment Payment Partially",
                "default_ar" => "تقسيط السداد جزئيا",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_passed",
                "default_en" => "Is passed",
                "default_ar" => "تم تمريره",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_passed_all",
                "default_en" => "Is passed all",
                "default_ar" => "تم تمريره بالكامل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "freq_period",
                "default_en" => "Freq period",
                "default_ar" => "فترة التكرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installmentPaymentTyp_step",
                "default_en" => "Step",
                "default_ar" => "نوع التكرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installmentPaymentType_per",
                "default_en" => "Percent",
                "default_ar" => "النسبة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installmentPaymentType_freq",
                "default_en" => "Frequency",
                "default_ar" => "التكرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "interest_per",
                "default_en" => "Interest percent",
                "default_ar" => "نسبة الفائدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_passed_contract_plan",
                "default_en" => "Is passed contract plan",
                "default_ar" => "هل خطة العقد ناجحة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

            [
                "key" => "is_conditional",
                "default_en" => "Is conditional",
                "default_ar" => "شرطي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "condition",
                "default_en" => "Condition name",
                "default_ar" => "اسم الشرط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ]
        ]);
        //Main Contact Groups
        Translation::insert([
            [
                "key" => "MainContactGroupsCreate",
                "default_en" => "Add new Main Contact Group",
                "default_ar" => "اضافة مجموعة الاتصال الرئيسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "MainContactGroupsEdit",
                "default_en" => "Edit Main Contact Group",
                "default_ar" => "نموذج تعديل مجموعة الاتصال الرئيسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "Group_name",
                "default_en" => "name Group (arabic)",
                "default_ar" => "اسم المجموعة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "Group_name_e",
                "default_en" => "name Group (english)",
                "default_ar" => "اسم المجموعة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Payment Plan Detail
        Translation::insert([
            [
                "key" => "InstallmentPaymentPlanDetailCreate",
                "default_en" => "Add new Installment Payment Plan Detail",
                "default_ar" => "اضافة تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "InstallmentPaymentPlanDetailEdit",
                "default_en" => "Edit Installment Payment Plan Detail",
                "default_ar" => "نموذج تعديل تفاصيل خطة الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_id",
                "default_en" => "installment payment plan",
                "default_ar" => "خطه الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_id",
                "default_en" => "installment payment type",
                "default_ar" => "نوع الدفع بالتقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "ln_no",
                "default_en" => "Line No",
                "default_ar" => "رقم الخط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_fixed",
                "default_en" => "is fixed day",
                "default_ar" => "تم إصلاحه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_per",
                "default_en" => "payment type per",
                "default_ar" => "لكل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type_freq",
                "default_en" => "payment type freq",
                "default_ar" => "عدد الاقساط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "interest_per",
                "default_en" => "interest per",
                "default_ar" => "نسبة الفايدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "interest_value",
                "default_en" => "interest value",
                "default_ar" => "قيمة الفايدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Role user
        Translation::insert([
            [
                "key" => "role_user_create_form",
                "default_en" => "Add new role user",
                "default_ar" => "اضف دور للمستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role user"
            ],
            [
                "key" => "role_user_edit_form",
                "default_en" => "Edit role user",
                "default_ar" => "تعديل دور المستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role user"
            ],
            [
                "key" => "role",
                "default_en" => "Role name",
                "default_ar" => "اسم الدور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role user"
            ],
            [
                "key" => "user",
                "default_en" => "User name",
                "default_ar" => "اسم المستخدم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "role user"
            ],
        ]);
        //Installment payment plan
        Translation::insert([
            [
                "key" => "installment_payment_plan_create",
                "default_en" => "Add new install payment plan",
                "default_ar" => "اضف خطة دفع جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_edit_form",
                "default_en" => "Edit install payment plan",
                "default_ar" => "تعديل خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_name_ar",
                "default_en" => "Installment payment name (arabic)",
                "default_ar" => "اسم خطة الدفع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_name_en",
                "default_en" => "Install payment plan name (english)",
                "default_ar" => "اسم خطة الدفع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_default",
                "default_en" => "Is default",
                "default_ar" => "افتراضي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "is_active",
                "default_en" => "Is active",
                "default_ar" => "فعال",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_description_ar",
                "default_en" => "Installment payment description (arabic)",
                "default_ar" => "وصف خطة الدفع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_description_en",
                "default_en" => "Install payment plan description (english)",
                "default_ar" => "وصف خطة الدفع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_start_date",
                "default_en" => "Install payment plan start date",
                "default_ar" => "تاريخ بداية خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_type",
                "default_en" => "Install payment type name",
                "default_ar" => "اسم نوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "day_mounth",
                "default_en" => "Day mounth",
                "default_ar" => "يوم الشهر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_note_a",
                "default_en" => "Note a",
                "default_ar" => "ملحوظة الف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_payment_plan_note_e",
                "default_en" => "Note e",
                "default_ar" => "ملحوظة ب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //installment document plan
        Translation::insert([
            [
                "key" => "installment_document_plan_create_form",
                "default_en" => "Add document plan",
                "default_ar" => "اضف خطة وثيقة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_document_plan_edit_form",
                "default_en" => "Edit installment document plan",
                "default_ar" => "تعديل خطة وثيقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_plan",
                "default_en" => "Installment plan",
                "default_ar" => "خطة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //screen sub contact group
        Translation::insert([
            [
                "key" => "screen_subcontact_group_create_form",
                "default_en" => "Add new screen subcontact group",
                "default_ar" => "اضف جروب اتصال فرعي للشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "screen_subcontact_group_edit_form",
                "default_en" => "Edit screen subcontact group",
                "default_ar" => "تعديل جروب الاتصال الفرعي للشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "subcontact_group",
                "default_en" => "Subcontact group name",
                "default_ar" => "اسم جروب التواصل الفرعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "screen",
                "default_en" => "Screen name",
                "default_ar" => "اسم الشاشة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //Installment Status
        Translation::insert([
            [
                "key" => "installment_condition_create_form",
                "default_en" => "Add new Installment Condition",
                "default_ar" => "اضافة شرط للتقسيط جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_edit_form",
                "default_en" => "Edit  Installment Condition",
                "default_ar" => "نموذج تعديل شرط التقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_name",
                "default_en" => "Installment Condition name (arabic)",
                "default_ar" => "اسم شرط تقسيط (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_name_e",
                "default_en" => "Installment Condition name (english)",
                "default_ar" => "اسم شرط تقسيط (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_condition_default",
                "default_en" => "Installment Condition Default",
                "default_ar" => "الحالة الافتراضية لشرط تقسيط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
        ]);
        //custom table
        Translation::insert([
            [
                "key" => "custom_table_create_form",
                "default_en" => "Add New Custom Table",
                "default_ar" => "اضافة جدول مخصص جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "custom table"
            ],
            [
                "key" => "custom_table_edit_form",
                "default_en" => "Edit  Custom Table",
                "default_ar" => "نموذج تعديل جدول مخصص",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "custom table"
            ],
            [
                "key" => "custom_table_name",
                "default_en" => "Table Name",
                "default_ar" => "اسم الجدول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "custom table"
            ],
            [
                "key" => "custom_column",
                "default_en" => "column",
                "default_ar" => "عمود",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "custom table"
            ],
            [
                "key" => "custom_is_required",
                "default_en" => "Is Required",
                "default_ar" => "مطلوب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "custom table"
            ],
            [
                "key" => "custom_is_visible",
                "default_en" => "Is Visible",
                "default_ar" => "مرئي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "custom table"
            ]
        ]);
        //general Customers
        Translation::insert([
            [
                "key" => "general_customer_create_form",
                "default_en" => "Add new customer",
                "default_ar" => "اضف زبون جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_edit_form",
                "default_en" => "Edit customer form",
                "default_ar" => "نموذج تعديل العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],

            [
                "key" => "general_customer_group",
                "default_en" => "Group Name",
                "default_ar" => "اسم الجروب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_is_supplier",
                "default_en" => "Is supplier",
                "default_ar" => "مورد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_name_ar",
                "default_en" => "Customer name (arabic)",
                "default_ar" => "اسم العميل (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_name_en",
                "default_en" => "Customer name (english)",
                "default_ar" => "اسم العميل (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_phone",
                "default_en" => "Customer phone",
                "default_ar" => "هاتف العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_email",
                "default_en" => "Customer email",
                "default_ar" => "بريد العميل الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_nationality",
                "default_en" => "Customer nationality",
                "default_ar" => "جنسية العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_national_id",
                "default_en" => "Customer national id",
                "default_ar" => "الرقم القومي للزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_contact_person",
                "default_en" => "Customer contact person",
                "default_ar" => "معلومات العميل الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_contact_phones",
                "default_en" => "Customer contact phones",
                "default_ar" => "هواتف اتصال العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_whatsapp",
                "default_en" => "Customer whatsapp",
                "default_ar" => "رقم واتساب العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_code",
                "default_en" => "Customer code",
                "default_ar" => "كود العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "general_customer_passport_number",
                "default_en" => "Customer passport number",
                "default_ar" => "رقم جواز سفر العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "customer_employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "customer_salesman",
                "default_en" => "Salesman name",
                "default_ar" => "اسم البائع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_facebook",
                "default_en" => "Facebook",
                "default_ar" => "الفيسبوك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_instagram",
                "default_en" => "Instagram",
                "default_ar" => "انستجرام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_linkedin",
                "default_en" => "Linkedin",
                "default_ar" => "لنكدان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_snapchat",
                "default_en" => "Snapchat",
                "default_ar" => "سناب شات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_twitter",
                "default_en" => "Twitter",
                "default_ar" => "تويتر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_website",
                "default_en" => "Website",
                "default_ar" => "الموقع الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "general_customer_note",
                "default_en" => "Note",
                "default_ar" => "الملاحظه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "customer_main_category",
                "default_en" => "Main Category",
                "default_ar" => "الفئه الرئيسيه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "customer_sub_category",
                "default_en" => "Sub Category",
                "default_ar" => "الفئه الرئيسيه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "customer_source",
                "default_en" => "Customer Source",
                "default_ar" => "مصدر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "customer_sector",
                "default_en" => "Sector",
                "default_ar" => "قطاع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ]
        ]);
        //document
        Translation::insert([
            [
                "key" => "document_create_form",
                "default_en" => "Add new document",
                "default_ar" => "اضف مستند جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"
            ],
            [
                "key" => "document_edit_form",
                "default_en" => "Edit document form",
                "default_ar" => "نموذج تعديل المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_name_ar",
                "default_en" => "Document name (arabic)",
                "default_ar" => "اسم المستند (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_name_en",
                "default_en" => "Document name (english)",
                "default_ar" => "اسم المستند (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_status",
                "default_en" => "Document default",
                "default_ar" => "الحالة الافتراضية للمستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_required",
                "default_en" => "Document Required",
                "default_ar" => "المستند مطلوب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_need_approve",
                "default_en" => "Document Need Approve",
                "default_ar" => "المستند يحتاج الى موافقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_need_is_copy",
                "default_en" => "Is Copy",
                "default_ar" => "متكرر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_need_is_partially",
                "default_en" => "Is Partially",
                "default_ar" => "يمكن التجزئه ؟",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_need_is_break",
                "default_en" => "Is Break",
                "default_ar" => "Is Break",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_detail_type",
                "default_en" => "document detail type",
                "default_ar" => "نوع المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"
            ],
            [
                "key" => "document_document_module_type",
                "default_en" => "document module type",
                "default_ar" => "البرنامج الفرعى للمستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
        ]);
        //document status
        Translation::insert([
            [
                "key" => "document_status_create_form",
                "default_en" => "Add new document status",
                "default_ar" => "اضف حالة جديدة للمستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"
            ],
            [
                "key" => "document_status_edit_form",
                "default_en" => "Edit document status form",
                "default_ar" => "نموذج تعديل خالة المستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_status_name_ar",
                "default_en" => "Document status name (arabic)",
                "default_ar" => "اسم حالة المستند (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_status_name_en",
                "default_en" => "Document status name (english)",
                "default_ar" => "اسم حالة المستند (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_status_status",
                "default_en" => "status",
                "default_ar" => "الحالة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_status_default",
                "default_en" => "Document status default",
                "default_ar" => "الحالة الافتراضية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],

        ]);
        //document reason
        Translation::insert([
            [
                "key" => "document_reason_create_form",
                "default_en" => "Add new document reason",
                "default_ar" => "اضف قرار جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"
            ],
            [
                "key" => "document_reason_edit_form",
                "default_en" => "Edit document reason form",
                "default_ar" => "نموذج تعديل القرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_serial_number",
                "default_en" => "Document serial number",
                "default_ar" => "الرقم التسلسلي للمستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_reason_notes",
                "default_en" => "notes",
                "default_ar" => "الملاحظات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_decision_date",
                "default_en" => "Decision date",
                "default_ar" => "تاريخ القرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_approval_time",
                "default_en" => "Approval time",
                "default_ar" => "وقت الموافقة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],
            [
                "key" => "document_decision",
                "default_en" => "Decision",
                "default_ar" => "القرار",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document"

            ],

        ]);
        // installment opening balance
        Translation::insert([
            [
                "key" => "installment_opening_balance_create_form",
                "default_en" => "Add customer opening balance",
                "default_ar" => "اضافة الرصيد الافتتاحى للعملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "installment_opening_balance_edit_form",
                "default_en" => "Edit customer opening balance",
                "default_ar" => "نموذج تعديل الرصيد الافتتاحى للعملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_date",
                "default_en" => "date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_currency",
                "default_en" => "Currency",
                "default_ar" => "العملة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_rate",
                "default_en" => "Rate",
                "default_ar" => "المعدل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_debit",
                "default_en" => "Debit Foreign",
                "default_ar" => "مدين اجنبية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_credit",
                "default_en" => "Credit Foreign",
                "default_ar" => "دائن اجنبية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_local_debit",
                "default_en" => "Local Debit",
                "default_ar" => "مدين محلية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_balance_local_credit",
                "default_en" => "Local Credit",
                "default_ar" => "دائن محلية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_count_opening_balance",
                "default_en" => "Count Opening Balance",
                "default_ar" => "عدد الارصدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_total_local_debit",
                "default_en" => "Total Local Debit",
                "default_ar" => "اجمالى مدين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_total_local_credit",
                "default_en" => "Total Local Credit",
                "default_ar" => "اجمالى دائن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_net",
                "default_en" => "Net",
                "default_ar" => "الرصيد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
            [
                "key" => "installment_opening_client_type",
                "default_en" => "Client Type",
                "default_ar" => "نوع العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"

            ],
        ]);
        //Street
        Translation::insert([
            [
                "key" => "street_create_form",
                "default_en" => "Add new street",
                "default_ar" => "اضف شارع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "street"
            ],
            [
                "key" => "street_edit_form",
                "default_en" => "Edit street form",
                "default_ar" => "تعديل نموذج شارع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "street"
            ],
            [
                "key" => "street_name_ar",
                "default_en" => "street name (arabic)",
                "default_ar" => "اسم شارع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "street"
            ],
            [
                "key" => "street_name_en",
                "default_en" => "street name (english)",
                "default_ar" => "اسم شارع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "street"
            ],
            [
                "key" => "street_status",
                "default_en" => "street status",
                "default_ar" => "حاله ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "street"
            ],
            [
                "key" => "street_avenue",
                "default_en" => "avenue",
                "default_ar" => "المنطقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "street",
            ],
        ]);
        //departments
        Translation::insert([
            [
                "key" => "department_create_form",
                "default_en" => "Add new department",
                "default_ar" => "اضف اداره جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],

            [
                "key" => "department_task_name_ar",
                "default_en" => "task name (arabic)",
                "default_ar" => "اسم مهمة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_task_supervisors",
                "default_en" => "Task Supervisor",
                "default_ar" => "مشرف المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_task_name_en",
                "default_en" => "task name (english)",
                "default_ar" => "اسم مهمة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_task_description_ar",
                "default_en" => "Task description (arabic)",
                "default_ar" => "وصف المهمه (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"

            ],
            [
                "key" => "department_task_description_en",
                "default_en" => "Task description (english)",
                "default_ar" => "وصف المهمه (الانجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"

            ],
            [
                "key" => "department_edit_form",
                "default_en" => "Edit department form",
                "default_ar" => "نموذج تعديل الاداره",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"

            ],
            [
                "key" => "department_name_ar",
                "default_en" => "department name (arabic)",
                "default_ar" => "اسم القسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"

            ],
            [
                "key" => "department_name_en",
                "default_en" => "department name (english)",
                "default_ar" => "اسم قسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_attentions",
                "default_en" => "attentions",
                "default_ar" => "مراجعين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_estimated_time",
                "default_en" => "Estimated time",
                "default_ar" => "الوقت التقديري",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_estimated_day",
                "default_en" => "Estimated day",
                "default_ar" => "اليوم التقديري",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_task_edit_form",
                "default_en" => "Edit task",
                "default_ar" => "تعديل المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],
            [
                "key" => "department_task_create_form",
                "default_en" => "Add new task",
                "default_ar" => "اضف مهمة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "department"
            ],

        ]);
        //statuses
        Translation::insert([
            [
                "key" => "statuses_create_form",
                "default_en" => "Add new status",
                "default_ar" => "اضف حاله جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"
            ],
            [
                "key" => "statuses_edit_form",
                "default_en" => "Edit status form",
                "default_ar" => "نموذج تعديل الحاله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "statuses_module_type",
                "default_en" => "Module type",
                "default_ar" => "نوع الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "statuses_name_ar",
                "default_en" => "status name (arabic)",
                "default_ar" => "اسم الحاله (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "statuses_name_en",
                "default_en" => "status name (english)",
                "default_ar" => "اسم الحاله (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "statuses_icon",
                "default_en" => "icon",
                "default_ar" => "أيقونة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "statuses_color",
                "default_en" => "color",
                "default_ar" => "اللون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
        ]);
        //   start  boardRealestate    //
        //boardRealestate sell method
        Translation::insert([
            [
                "key" => "boardRent_sellMethod_create_form",
                "default_en" => "Add new sell Method",
                "default_ar" => "اضف طريقه بيع جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sell method"
            ],
            [
                "key" => "boardRent_sellMethod_edit_form",
                "default_en" => "Edit Sell Method form",
                "default_ar" => "تعديل طريقه البيع ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sell method"

            ],
            [
                "key" => "boardRent_sellMethod_name_ar",
                "default_en" => "Sell Method name (arabic)",
                "default_ar" => "اسم طريقه البيع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sell method"
            ],
            [
                "key" => "boardRent_sellMethod_name_en",
                "default_en" => "Sell Method name (english)",
                "default_ar" => "اسم طريقه اليع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sell method"
            ],
            [
                "key" => "boardRent_sellMethod_calculated_percentage",
                "default_en" => "Calculated target",
                "default_ar" => "الهدف المحسوب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sell method"
            ],
            [
                "key" => "boardRent_sellMethod_commission_ratio",
                "default_en" => "Commission ratio",
                "default_ar" => "العمولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sell method"
            ],
            [
                "key" => "is_all_value",
                "default_en" => "Full Value",
                "default_ar" => "كامل القيمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sell method"
            ],
        ]);
        //boardRealestate package
        Translation::insert([
            [
                "key" => "boardRent_package_create_form",
                "default_en" => "Add new Package",
                "default_ar" => "اضف باقه جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_package_edit_form",
                "default_en" => "Edit Package form",
                "default_ar" => "تعديل الباقه ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_package_category",
                "default_en" => "Category",
                "default_ar" => "الفئات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_package_governorate",
                "default_en" => "Governorate",
                "default_ar" => "المحافظات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_package_name_ar",
                "default_en" => "Package name (arabic)",
                "default_ar" => "اسم الباقه (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_package_name_en",
                "default_en" => "Package name (english)",
                "default_ar" => "اسم الباقه (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_package_code",
                "default_en" => "Package Code",
                "default_ar" => "كود الباقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_package_price",
                "default_en" => "Package  price",
                "default_ar" => "سعر الباقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_package_note",
                "default_en" => "Note",
                "default_ar" => "ملاحظه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
        ]);
        //boardRealestate Unit status
        Translation::insert([
            [
                "key" => "boardRent_unitStatus_create_form",
                "default_en" => "Add new Unit Status",
                "default_ar" => "اضف حاله الوحده جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_unitStatus_edit_form",
                "default_en" => "Edit Unit Status form",
                "default_ar" => "تعديل حاله الوحده ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_unitStatus_name_ar",
                "default_en" => "Unit Status name (arabic)",
                "default_ar" => "اسم حاله البيع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_unitStatus_name_en",
                "default_en" => "Unit Status name (english)",
                "default_ar" => "اسم حاله البيع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_unitStatus_status",
                "default_en" => "Status",
                "default_ar" => " حاله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
        ]);
        //boardRealestate Task
        Translation::insert([
            [
                "key" => "boardRent_task_type",
                "default_en" => "Type",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_equipment",
                "default_en" => "Equipment Type",
                "default_ar" => "نوع المعده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_location",
                "default_en" => "Location",
                "default_ar" => "الموقع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_type",
                "default_en" => "Type",
                "default_ar" => "نوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_task_requirement",
                "default_en" => "Task Requirement",
                "default_ar" => "متطلبات المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_is_closed",
                "default_en" => "Is Closed ?",
                "default_ar" => "مغلقه ؟",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_panel_admin_note",
                "default_en" => "Admin Note",
                "default_ar" => "ملاحظة المسؤول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_create_form",
                "default_en" => "Add new Task",
                "default_ar" => "اضف مهمة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_edit_form",
                "default_en" => "Edit Task form",
                "default_ar" => "تعديل المهمة ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "boardRent_task_name_ar",
                "default_en" => "task name (arabic)",
                "default_ar" => "اسم مهمة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_name_en",
                "default_en" => "task name (english)",
                "default_ar" => "اسم مهمة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "boardRent_task_description_ar",
                "default_en" => "Task description (arabic)",
                "default_ar" => "وصف المهمه (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "boardRent_task_description_en",
                "default_en" => "Task description (english)",
                "default_ar" => "وصف المهمه (الانجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"
            ],
            [
                "key" => "boardRent_task_department",
                "default_en" => "Department",
                "default_ar" => "القسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_title",
                "default_en" => "Task Title",
                "default_ar" => "عنوان المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "equipment",
                "default_en" => "Equipment",
                "default_ar" => "المعدات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_type",
                "default_en" => "Task Type",
                "default_ar" => "نوع المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_priority",
                "default_en" => "priority",
                "default_ar" => "الاولويه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_owners",
                "default_en" => "Task Owners",
                "default_ar" => "مالكو المهام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_notifications",
                "default_en" => "Task Notification",
                "default_ar" => "اشعارات المهام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_supervisors",
                "default_en" => "Task Supervisor",
                "default_ar" => "مشرف المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_status",
                "default_en" => "Task Status",
                "default_ar" => "حالة المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "execution_date",
                "default_en" => "Task Execution Date",
                "default_ar" => "تاريخ تنفيذ المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_start_time",
                "default_en" => "Task Start Time",
                "default_ar" => "وقت بداية المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "task_end_time",
                "default_en" => "Task End Time",
                "default_ar" => "وقت نهاية المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "execution_duration",
                "default_en" => "Task Execution Duration",
                "default_ar" => "المدة المتوقعة للمهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "notification_date",
                "default_en" => "Task Notification Date",
                "default_ar" => "تاريخ اشعار المهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
            [
                "key" => "execution_end_date",
                "default_en" => "Task Expected End Date",
                "default_ar" => "تاريخ الانتهاء المتوقع للمهمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "ticket manager"

            ],
        ]);
        // boardRealestate lookups
        Translation::insert([
            [
                "key" => "boardRent_lookUp_create_form",
                "default_en" => "Add new look Up form",
                "default_ar" => "اضف عمليه البحث جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_lookUp_edit_form",
                "default_en" => "Edit look up form",
                "default_ar" => "نموذج تعديل عمليه البحث",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_lookUp_name_ar",
                "default_en" => "look up name (arabic)",
                "default_ar" => "اسم عمليه البحث (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_lookUp_name_en",
                "default_en" => "LookUp name (english)",
                "default_ar" => "اسم عمليه البحث (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_lookUp_type",
                "default_en" => "Type",
                "default_ar" => "نوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_lookUp_parent",
                "default_en" => "Parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
        ]);
        // boardRealestate panels
        Translation::insert([
            [
                "key" => "boardRent_panel_create_form",
                "default_en" => "Add new Panel form",
                "default_ar" => "اضف لوحه جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_edit_form",
                "default_en" => "Edit Panel form",
                "default_ar" => "نموذج تعديل اللوحه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_name_ar",
                "default_en" => " name (arabic)",
                "default_ar" => "اسم  (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_panel_name_en",
                "default_en" => " name (english)",
                "default_ar" => "اسم  (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_description",
                "default_en" => " Description (arabic)",
                "default_ar" => "الوصف  (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_panel_description_e",
                "default_en" => " Description (english)",
                "default_ar" => "الوصف  (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_day",
                "default_en" => "Price Per Day",
                "default_ar" => "سعر اليوم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_panel_week",
                "default_en" => "Week Price",
                "default_ar" => "سعر الاسبوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_month",
                "default_en" => "Month Price",
                "default_ar" => "سعر الشهر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_quarter_year",
                "default_en" => "3 Month Price",
                "default_ar" => "سعر ربع سنوي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_half_year",
                "default_en" => "6 Month Price",
                "default_ar" => "سعر النصف سنوي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_year",
                "default_en" => "Year Price",
                "default_ar" => "السعر السنوي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_code",
                "default_en" => "Code",
                "default_ar" => "كود",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_new_code",
                "default_en" => "New Code",
                "default_ar" => "الكود الجديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_face",
                "default_en" => "Face",
                "default_ar" => "الوجه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_note",
                "default_en" => "Note",
                "default_ar" => "الملاحظه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_size",
                "default_en" => "Size",
                "default_ar" => "المقاسات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_unit_status",
                "default_en" => "Unit Status",
                "default_ar" => "حاله الوحده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_department",
                "default_en" => "Department",
                "default_ar" => "القسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_category",
                "default_en" => "Category",
                "default_ar" => "الفئات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_country",
                "default_en" => "Country",
                "default_ar" => "الدوله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_governorate",
                "default_en" => "Governorate",
                "default_ar" => "المحافظات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_city",
                "default_en" => "City",
                "default_ar" => "المدينه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_avenue",
                "default_en" => "Avenue",
                "default_ar" => "المناطق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_street",
                "default_en" => "Street",
                "default_ar" => "الشارع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_lat",
                "default_en" => "latitude",
                "default_ar" => "latitude",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_panel_lng",
                "default_en" => "longitude",
                "default_ar" => "longitude",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
        ]);
        //board order
        Translation::insert([
            [
                "key" => "order_serial",
                "default_en" => "Serial",
                "default_ar" => "Serial",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_order_create_form",
                "default_en" => "Add new Order",
                "default_ar" => "اضف الطلب جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_order_edit_form",
                "default_en" => "Edit Order form",
                "default_ar" => "تعديل الطلب ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_quotation_create_form",
                "default_en" => "Add new Quotation",
                "default_ar" => "اضف اقتباس جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_quotation_edit_form",
                "default_en" => "Edit Quotation form",
                "default_ar" => "تعديل الاقتباس ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_document_related",
                "default_en" => "Document Related",
                "default_ar" => "وثيقة ذات صلة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_price",
                "default_en" => "price",
                "default_ar" => "سعر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_sellMethod",
                "default_en" => "Sell Method",
                "default_ar" => "طريقه البيع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_is_stripe",
                "default_en" => "Stripe",
                "default_ar" => "خط",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_quantity",
                "default_en" => "Quantity",
                "default_ar" => "الكميه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_from",
                "default_en" => "Start Date",
                "default_ar" => "تاريخ البدايه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"

            ],
            [
                "key" => "boardRent_order_to",
                "default_en" => "End Date",
                "default_ar" => "تاريخ النهايه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_order_package",
                "default_en" => "Package",
                "default_ar" => "الباقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_order_category",
                "default_en" => "Category",
                "default_ar" => "الفئه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
            [
                "key" => "boardRent_order_governorate",
                "default_en" => "Governorate",
                "default_ar" => "المحافظه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "board rent"
            ],
        ]);
        //   end  boardRealestate    //
        //Category
        Translation::insert([
            [
                "key" => "category_create_form",
                "default_en" => "Add new category",
                "default_ar" => "اضف فئه جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Category"
            ],
            [
                "key" => "category_edit_form",
                "default_en" => "Edit Category form",
                "default_ar" => "تعديل نموذج الفئه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Category"

            ],
            [
                "key" => "category_name_ar",
                "default_en" => "Category name (arabic)",
                "default_ar" => "اسم الفئه (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Category"

            ],
            [
                "key" => "category_name_en",
                "default_en" => "Category name (english)",
                "default_ar" => "اسم الفئه (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Category"
            ]
        ]);
        //Sponsor
        Translation::insert([
            [
                "key" => "sponsor_create_form",
                "default_en" => "Add new sponsor",
                "default_ar" => "اضف راعي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "sponsor_edit_form",
                "default_en" => "Edit sponsor form",
                "default_ar" => "تعديل الراعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "sponsor_name_ar",
                "default_en" => "Sponsor name (arabic)",
                "default_ar" => "اسم الراعي (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "sponsor_name_en",
                "default_en" => "Sponsor name (english)",
                "default_ar" => "اسم الراعي (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "parent",
                "default_en" => "Parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "sponsor_group",
                "default_en" => "Group",
                "default_ar" => "جروب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ]
        ]);
        //Member
        Translation::insert([
            [
                "key" => "member_create_form",
                "default_en" => "Add new member",
                "default_ar" => "اضف عضو جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_edit_form",
                "default_en" => "Edit member",
                "default_ar" => "تعديل العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "member_request_create_form",
                "default_en" => "Add new member request",
                "default_ar" => "اضف  طلب لعضو جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_request_create_accept",
                "default_en" => "Acceptance of members",
                "default_ar" => "قبول الاعضاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_request_create_reject",
                "default_en" => "Reject member",
                "default_ar" => "رفض عضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_request_edit_form",
                "default_en" => "Edit member request",
                "default_ar" => "تعديل الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "member_first_name",
                "default_en" => "First name",
                "default_ar" => "الاسم الاول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_second_name",
                "default_en" => "Second name",
                "default_ar" => "الاسم الثاني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_third_name",
                "default_en" => "Third name",
                "default_ar" => "الاسم الثالث",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status",
                "default_en" => "Financial status name",
                "default_ar" => "اسم الحالة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_type_id",
                "default_en" => "Member type name",
                "default_ar" => "اسم نوع العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_type",
                "default_en" => "Type",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_last_name",
                "default_en" => "Last name",
                "default_ar" => "الاسم الرابع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_family_name",
                "default_en" => "Family name",
                "default_ar" => "اسم العائلة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "status",
                "default_en" => "Registered status name",
                "default_ar" => "اسم الحالة المسجلة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status",
                "default_en" => "Financial status name",
                "default_ar" => "اسم الحالة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_membership_number",
                "default_en" => "Membership number",
                "default_ar" => "رقم الحالة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_applying_number",
                "default_en" => "Applying number",
                "default_ar" => "رقم الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "auto_status_sun",
                "default_en" => "Auto status name",
                "default_ar" => "اسم الحالة الالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "auto_status_parent",
                "default_en" => "Auto status parent name",
                "default_ar" => "اسم الحالة الالية للاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_birth_date",
                "default_en" => "Birth date",
                "default_ar" => "تاريخ الميلاد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_national_id",
                "default_en" => "National id",
                "default_ar" => "الرقم المدني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_nationality_number",
                "default_en" => "Nationality number",
                "default_ar" => "رقم الهوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_home_phone",
                "default_en" => "Home phone",
                "default_ar" => "تليفون المنزل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_work_phone",
                "default_en" => "Work phone",
                "default_ar" => "تليفون العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],

            [
                "key" => "member_home_address",
                "default_en" => "Home address",
                "default_ar" => "عنوان المنزل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_work_address",
                "default_en" => "Work address",
                "default_ar" => "عنوان العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],

            [
                "key" => "member_membership_date",
                "default_en" => "Membership date",
                "default_ar" => "تاريخ العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_membership_number",
                "default_en" => "Membership number",
                "default_ar" => "رقم العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "apply_membership_date",
                "default_en" => "date of request",
                "default_ar" => "تاريخ الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_membership_number",
                "default_en" => "Membership number",
                "default_ar" => "رقم العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "apply_membership_number",
                "default_en" => "number of request",
                "default_ar" => "رقم الطلب ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_job",
                "default_en" => "Job",
                "default_ar" => "الوظيفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_degree",
                "default_en" => "Degree",
                "default_ar" => "الدرجة العلمية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_acceptance",
                "default_en" => "Acceptance",
                "default_ar" => "القبول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_session_date",
                "default_en" => "Session date",
                "default_ar" => "تاريخ الجلسة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_acceptance_date",
                "default_en" => "Acceptance date",
                "default_ar" => "تاريخ القبول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_acceptance_number",
                "default_en" => "Acceptance number",
                "default_ar" => "رقم القبول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_session_number",
                "default_en" => "Session number",
                "default_ar" => "رقم الجلسة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "sponsor",
                "default_en" => "Sponsor name",
                "default_ar" => "اسم الراعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "from-sponsor",
                "default_en" => "Sponsor name (from)",
                "default_ar" => "اسم الراعي (من)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "to-sponsor",
                "default_en" => "Sponsor name (to)",
                "default_ar" => "اسم الراعي (الي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_is_sponsor",
                "default_en" => "Is Sponsor",
                "default_ar" => " هل  راعي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "Subscription_receipt_number",
                "default_en" => "Subscription receipt number",
                "default_ar" => "رقم ايصال الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);
        //Pending member
        Translation::insert([
            [
                "key" => "pending_member_create_form",
                "default_en" => "Add new pending member",
                "default_ar" => "اضف عضو معلق جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "pending_member_edit_form",
                "default_en" => "Edit pending member",
                "default_ar" => "تعديل عضو معلق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "pending_member_name_ar",
                "default_en" => "Pending member name (arabic)",
                "default_ar" => "اسم العضو المعلق (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "pending_member_name_en",
                "default_en" => "Pending member name (english)",
                "default_ar" => "اسم العضو المعلق (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "pending_member_status",
                "default_en" => "Pending member status",
                "default_ar" => "حالة العضو المعلق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Financial status
        Translation::insert([
            [
                "key" => "financial_status_create_form",
                "default_en" => "Add new financial status",
                "default_ar" => "اضف حالة مالية جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "financial_status_edit_form",
                "default_en" => "Edit financial status form",
                "default_ar" => "تعديل الحالة المالية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"

            ],
            [
                "key" => "financial_status_name_ar",
                "default_en" => "Financial status name (arabic)",
                "default_ar" => "اسم الحالة المالية (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "financial_status_name_en",
                "default_en" => "Financial status name (english)",
                "default_ar" => "اسم الحالة المالية (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //Street
        Translation::insert([
            [
                "key" => "street_create_form",
                "default_en" => "Add new street",
                "default_ar" => "اضف شارع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "street_edit_form",
                "default_en" => "Edit street form",
                "default_ar" => "تعديل نموذج شارع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "street_name_ar",
                "default_en" => "street name (arabic)",
                "default_ar" => "اسم شارع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "street_name_en",
                "default_en" => "street name (english)",
                "default_ar" => "اسم شارع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "street_status",
                "default_en" => "street status",
                "default_ar" => "حاله ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"
            ],
            [
                "key" => "street_avenue",
                "default_en" => "avenue",
                "default_ar" => "المنطقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer",
            ],
        ]);

        //Item
        Translation::insert([
            [
                "key" => "item_create_form",
                "default_en" => "Add new item",
                "default_ar" => "اضف صنف جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "item_edit_form",
                "default_en" => "Edit item form",
                "default_ar" => "تعديل الصنف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "code_number",
                "default_en" => "Code number",
                "default_ar" => "رقم الكود",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "item_name_ar",
                "default_en" => "Item name (arabic)",
                "default_ar" => "اسم الفئة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "item_name_en",
                "default_en" => "Item name (english)",
                "default_ar" => "اسم الصنف (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "item_unit",
                "default_en" => "Unit name",
                "default_ar" => "اسم الوحدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "item_price",
                "default_en" => "price",
                "default_ar" => "السعر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "item_type",
                "default_en" => "type",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],

        ]);

        //Service
        Translation::insert([
            [
                "key" => "service_name_ar",
                "default_en" => "Service name (arabic)",
                "default_ar" => "اسم الفئة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "service_name_en",
                "default_en" => "Service name (english)",
                "default_ar" => "اسم الصنف (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "service_create_form",
                "default_en" => "Add new service",
                "default_ar" => "اضف خدمة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "service_edit_form",
                "default_en" => "Edit service form",
                "default_ar" => "تعديل الخدمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "service_price",
                "default_en" => "Price",
                "default_ar" => "السعر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "service_type",
                "default_en" => "The Nature Of The Item",
                "default_ar" => "طبيعة الصنف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],

        ]);
        //Service
        Translation::insert([
            [
                "key" => "company_name_ar",
                "default_en" => "Company name (arabic)",
                "default_ar" => "اسم الشركة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "company_name_en",
                "default_en" => "Compaany name (english)",
                "default_ar" => "اسم الشركة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "company_create_form",
                "default_en" => "Add new company",
                "default_ar" => "اضف شركة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "company_edit_form",
                "default_en" => "Edit company form",
                "default_ar" => "تعديل الشركة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "company_phone",
                "default_en" => "Phone",
                "default_ar" => "الهاتف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "company_address",
                "default_en" => "Address",
                "default_ar" => "العنوان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
            [
                "key" => "company_tax_record",
                "default_en" => "Tax Record",
                "default_ar" => "السجل الضريبي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "real estate"
            ],
        ]);
        //club settings
        Translation::insert([
            [
                "key" => "settings_create_form",
                "default_en" => "Add new setting",
                "default_ar" => "اضف اعدادات جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "settings_edit_form",
                "default_en" => "Edit setting form",
                "default_ar" => "تعديل الاعدادات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_gender",
                "default_en" => "gender",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_type",
                "default_en" => "Member Type",
                "default_ar" => "انواع العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member_permission",
                "default_en" => "Member Permission",
                "default_ar" => "صلاحيات العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_period",
                "default_en" => "Membership Period",
                "default_ar" => "مدة الاشتراك تجاوزة (سنه)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "financial_status",
                "default_en" => "Financial Status",
                "default_ar" => "حالة السداد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "allowed_subscription_date",
                "default_en" => "Allowed Subscription Date",
                "default_ar" => "فترة السداد لا تتجاوز تاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "allowed_vote_date",
                "default_en" => "Allowed Vote Date",
                "default_ar" => "اقصى تاريخ للاحقية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);
        //membership_renewal
        Translation::insert([
            [
                "key" => "membership_renewal_create_form",
                "default_en" => "Add new membership renewal",
                "default_ar" => "اضف تجديد عضويه جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_edit_form",
                "default_en" => "Edit membership renewal",
                "default_ar" => "تعديل تجديد عضويه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"

            ],
            [
                "key" => "membership_renewal_from",
                "default_en" => "from",
                "default_ar" => "من",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_to",
                "default_en" => "to",
                "default_ar" => "الي ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_fromDay",
                "default_en" => "from (D)",
                "default_ar" => "من (اليوم)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_toDay",
                "default_en" => "to (D)",
                "default_ar" => "الي (اليوم)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_fromMonth",
                "default_en" => "from (M)",
                "default_ar" => "من (الشهر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_toMonth",
                "default_en" => "to (M)",
                "default_ar" => "الي (الشهر)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_membership_availability",
                "default_en" => "membership availability",
                "default_ar" => "عضويه متاحه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_renewal_availability",
                "default_en" => "renewal availability",
                "default_ar" => "متوفر التجديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_renewal_membership_cost",
                "default_en" => "membership cost",
                "default_ar" => "تكلفة العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "membership_renewal_renewal_renewal_cost",
                "default_en" => "renewal cost",
                "default_ar" => "تكلفة التجديد ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ]
        ]);
        //club Membership renewal
        Translation::insert([
            [
                "key" => "subscription_create_form",
                "default_en" => "Add Membership renewal",
                "default_ar" => "اضافة تجديد عضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "subscription_edit_form",
                "default_en" => "Edit Membership renewal form",
                "default_ar" => "تعديل تجديد العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "member",
                "default_en" => "Member",
                "default_ar" => "العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "year_from",
                "default_en" => "From Year",
                "default_ar" => "تاريخ بداية الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "year_to",
                "default_en" => "To Year",
                "default_ar" => "تاريخ نهاية الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "subscription_type",
                "default_en" => "Type",
                "default_ar" => "نوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "subscription_amount",
                "default_en" => "Amount Membership renewal",
                "default_ar" => "قيمة تجديد العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
            [
                "key" => "number_of_years",
                "default_en" => "Number Of Years",
                "default_ar" => "عدد السنين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],

        ]);

        //credit note
        Translation::insert([
            [
                "key" => "credit_note_table",
                "default_en" => "Credit Note Table",
                "default_ar" => "جدول اشعار دائن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "serial_number",
                "default_en" => "Serial Number",
                "default_ar" => "الرقم التسلسلي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

            [
                "key" => "credit_note_create_form",
                "default_en" => "Add New Credit Note",
                "default_ar" => "اضافة اشعار دائن جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "credit_note_edit_form",
                "default_en" => "edit Credit Note",
                "default_ar" => "تعديل اشعار دائن",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

        ]);

        //debit note
        Translation::insert([
            [
                "key" => "debit_note_table",
                "default_en" => "Debit Note Table",
                "default_ar" => "جدول اشعار مدين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "debit_note_create_form",
                "default_en" => "Add New Debit Note",
                "default_ar" => "اضافة اشعار مدين جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],
            [
                "key" => "debit_note_edit_form",
                "default_en" => "edit Debit Note",
                "default_ar" => "تعديل اشعار مدين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "receivable payable"
            ],

        ]);
        //Salesman plans
        Translation::insert([
            [
                "key" => "salesmanplan_create_form",
                "default_en" => "Add new plan",
                "default_ar" => "اضف خطة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "salesmanplan_edit_form",
                "default_en" => "Edit plan",
                "default_ar" => "تعديل الخطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "salesmanplan_name_ar",
                "default_en" => "Plan name (arabic)",
                "default_ar" => "اسم الخطة (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "salesmanplan_name_en",
                "default_en" => "Plan name (english)",
                "default_ar" => "اسم الخطة (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "period",
                "default_en" => "Period name",
                "default_ar" => "اسم الدورة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "source",
                "default_en" => "Source name",
                "default_ar" => "اسم المصدر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
        ]);
        //Salesman plan details
        Translation::insert([
            [
                "key" => "salesmanplan_detail_create_form",
                "default_en" => "Add new plan detail",
                "default_ar" => "اضف تفاصيل خطة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "salesmanplan_detail_edit_form",
                "default_en" => "Edit plan detail",
                "default_ar" => "تعديل تفاصيل الخطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "plan",
                "default_en" => "Plan name",
                "default_ar" => "اسم الخطة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "salesmanplan_detail_amount_from",
                "default_en" => "Amount from",
                "default_ar" => "الكمية من",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "salesmanplan_detail_amount_to",
                "default_en" => "Amount to",
                "default_ar" => "الكمية الى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
            [
                "key" => "salesmanplan_detail_commission_percent",
                "default_en" => "Commission percent",
                "default_ar" => "نسبة العمولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "employees"
            ],
        ]);
        //Hr payroll heads
        Translation::insert([
            [
                "key" => "payroll_head_create_form",
                "default_en" => "Add new payroll head",
                "default_ar" => "اضف رئيس جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_head_edit_form",
                "default_en" => "Edit payroll head",
                "default_ar" => "تعديل الرئيس",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_head_name_ar",
                "default_en" => "Payroll head name (arabic)",
                "default_ar" => "اسم الرئيس (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_head_name_en",
                "default_en" => "Payroll name (english)",
                "default_ar" => "اسم الرئيس (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //Job titles
        Translation::insert([
            [
                "key" => "job_title_create_form",
                "default_en" => "Add new job title",
                "default_ar" => "اضف عنوان جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "job_title_edit_form",
                "default_en" => "Edit job title",
                "default_ar" => "تعديل العنوان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "job_title_name_ar",
                "default_en" => "Job title head name (arabic)",
                "default_ar" => "اسم العنوان (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "job_title_name_en",
                "default_en" => "Job title name (english)",
                "default_ar" => "اسم العنوان (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //HR request types
        Translation::insert([
            [
                "key" => "request_type_create_form",
                "default_en" => "Add new request type",
                "default_ar" => "اضف نوع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_edit_form",
                "default_en" => "Edit type",
                "default_ar" => "تعديل النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_name_ar",
                "default_en" => "Type name (arabic)",
                "default_ar" => "اسم النوع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_name_en",
                "default_en" => "Type name (english)",
                "default_ar" => "اسم النوع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_start_from",
                "default_en" => "Start from",
                "default_ar" => "بداية من",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_end_date",
                "default_en" => "End date",
                "default_ar" => "تاريخ النهاية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_amount",
                "default_en" => "Amount",
                "default_ar" => "الكمية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_from_hour",
                "default_en" => "From hour",
                "default_ar" => "من الساعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type_to_hour",
                "default_en" => "To hour",
                "default_ar" => "الى الساعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //Equipments
        Translation::insert([
            [
                "key" => "equipment_create_form",
                "default_en" => "Add new Equipment form",
                "default_ar" => "اضف معدات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "equipment",
            ],
            [
                "key" => "equipment_edit_form",
                "default_en" => "Edit Equipment form",
                "default_ar" => "نموذج تعديل المعدات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "equipment",

            ],
            [
                "key" => "equipment_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "اسم ( الانجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "equipment",

            ],
            [
                "key" => "equipment_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "اسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "equipment",

            ],
            [
                "key" => "equipment_location",
                "default_en" => "Location",
                "default_ar" => "موقع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "equipment",

            ],
            [
                "key" => "equipment_periodic_maintenance",
                "default_en" => "Periodic Maintenance",
                "default_ar" => "صيانه دوريه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "equipment",

            ],
        ]);
        // priority
        Translation::insert([
            [
                "key" => "priority_create_form",
                "default_en" => "Add new priority",
                "default_ar" => "اضف اولويه جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "priority"
            ],
            [
                "key" => "priority_edit_form",
                "default_en" => "Edit priority form",
                "default_ar" => "نموذج تعديل الاولويه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "priority"

            ],
            [
                "key" => "priority_name_ar",
                "default_en" => " name (arabic)",
                "default_ar" => "اسم  (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "priority"

            ],
            [
                "key" => "priority_name_en",
                "default_en" => " name (english)",
                "default_ar" => "اسم  (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "priority"

            ],
            [
                "key" => "priority_is_valid",
                "default_en" => "priority status",
                "default_ar" => "حالة ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "priority"

            ],
            [
                "key" => "priority_is_default",
                "default_en" => "Is default",
                "default_ar" => "افتراضي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "priority"
            ],
        ]);
        //locations
        Translation::insert([
            [
                "key" => "location_create_form",
                "default_en" => "Add new Location form",
                "default_ar" => "اضف موقع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "location",
            ],
            [
                "key" => "location_edit_form",
                "default_en" => "Edit Location form",
                "default_ar" => "نموذج تعديل الموقع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "location",

            ],
            [
                "key" => "location_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "اسم ( الانجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "location",
            ],
            [
                "key" => "location_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "اسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "location",
            ],
            [
                "key" => "location_priority_id",
                "default_en" => "Priority",
                "default_ar" => "الاولويه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "location",
            ],
        ]);
        //Periodic maintenance
        Translation::insert([
            [
                "key" => "periodic_maintenance_create_form",
                "default_en" => "Add new Periodic Maintenance",
                "default_ar" => "اضف صيانة دورية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "periodic maintenance",
            ],
            [
                "key" => "periodic_maintenance_edit_form",
                "default_en" => "Edit Periodic Maintenance form",
                "default_ar" => "نموذج تعديل صيانة دورية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "periodic maintenance",

            ],
            [
                "key" => "periodic_maintenance_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "اسم ( الانجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "periodic maintenance",
            ],
            [
                "key" => "periodic_maintenance_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "اسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "periodic maintenance",
            ],
            [
                "key" => "periodic_maintenance_status",
                "default_en" => "Status",
                "default_ar" => "الحاله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "periodic maintenance",
            ],
            [
                "key" => "periodic_maintenance_task",
                "default_en" => "Task",
                "default_ar" => "مهمات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "periodic maintenance",
            ],
            [
                "key" => "periodic_maintenance_period",
                "default_en" => "Period",
                "default_ar" => "فترة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "periodic maintenance",
            ]
        ]);
        //HR online request
        Translation::insert([
            [
                "key" => "online_request_create_form",
                "default_en" => "Add new online request",
                "default_ar" => "اضف طلب جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_edit_form",
                "default_en" => "Edit online request",
                "default_ar" => "تعديل الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_admin_comment",
                "default_en" => "Reply comment",
                "default_ar" => "تعليق الرد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "request_type",
                "default_en" => "Request type name",
                "default_ar" => "اسم الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],

            [
                "key" => "employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr",
            ],
            [
                "key" => "online_request_start_from",
                "default_en" => "Start from",
                "default_ar" => "بداية من",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_last_status",
                "default_en" => "Status",
                "default_ar" => "الحالة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_end_date",
                "default_en" => "End date",
                "default_ar" => "تاريخ النهاية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_descriptions",
                "default_en" => "Description",
                "default_ar" => "الوصف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_amount",
                "default_en" => "Amount",
                "default_ar" => "مبلغ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_from_hour",
                "default_en" => "From hour",
                "default_ar" => "من الساعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_to_hour",
                "default_en" => "To hour",
                "default_ar" => "الى الساعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_approved_start_from",
                "default_en" => "Approved start from",
                "default_ar" => "اعتماد تاريخ البداية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_remark",
                "default_en" => "Remark",
                "default_ar" => "ملاحظة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_approved_end_date",
                "default_en" => "Approved end date",
                "default_ar" => "اعتماد تاريخ النهاية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_approved_date",
                "default_en" => "Reply date",
                "default_ar" => "تاريخ الرد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_approved_from_hour",
                "default_en" => "Approved from hour",
                "default_ar" => "اعتماد من الساعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_approved_to_hour",
                "default_en" => "Approved to hour",
                "default_ar" => "اعتماد الى الساعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "online_request_approved_amount",
                "default_en" => "Approved amount",
                "default_ar" => "اعتماد المبلغ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //Payroll statement
        Translation::insert([
            [
                "key" => "payroll_statement_view_form",
                "default_en" => "Payroll view form",
                "default_ar" => "نموذج عرض الراتب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_statement_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_statement_work_days",
                "default_en" => "Work days",
                "default_ar" => "ايام العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_statement_work_value",
                "default_en" => "Work value",
                "default_ar" => "قيمة العمل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_statement_net",
                "default_en" => "Net",
                "default_ar" => "الصافي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_statement_basic_salary",
                "default_en" => "Basic salary",
                "default_ar" => "المرتب الاساسي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_statement_benefits",
                "default_en" => "Benefits",
                "default_ar" => "الفوائد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "payroll_statement_deductions",
                "default_en" => "Deductions",
                "default_ar" => "النواقص",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //Vacation balance
        Translation::insert([
            [
                "key" => "vacation_balance_view_form",
                "default_en" => "Vacation balance form",
                "default_ar" => "نموذج عرض رصيد الاجازة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "vacation_balance_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "vacation_balance_days",
                "default_en" => "Days",
                "default_ar" => "الايام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //Emergency balance
        Translation::insert([
            [
                "key" => "emergency_balance_view_form",
                "default_en" => "Emergency balance form",
                "default_ar" => "نموذج عرض رصيد الطواريء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "emergency_balance_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "emergency_balance_days",
                "default_en" => "Days",
                "default_ar" => "الايام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //Request
        Translation::insert([
            [
                "key" => "request_view_form",
                "default_en" => "Request view form",
                "default_ar" => "نموذج عرض الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],

        ]);
        //End service
        Translation::insert([
            [
                "key" => "end_service_view_form",
                "default_en" => "End service view form",
                "default_ar" => "نموذج عرض نهاية الخدمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "end_service_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "end_service_days",
                "default_en" => "Days",
                "default_ar" => "الايام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
            [
                "key" => "end_service_value",
                "default_en" => "Value",
                "default_ar" => "القيمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "hr"
            ],
        ]);
        //sector
        Translation::insert([
            [
                "key" => "sector_create_form",
                "default_en" => "Add new sector",
                "default_ar" => "اضف قطاع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sector"
            ],
            [
                "key" => "sector_edit_form",
                "default_en" => "Edit sector form",
                "default_ar" => "تعديل نموذج قطاع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sector"

            ],
            [
                "key" => "sector_name_ar",
                "default_en" => "Sector name (arabic)",
                "default_ar" => "اسم القطاع (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sector"

            ],
            [
                "key" => "sector_name_en",
                "default_en" => "Sector name (english)",
                "default_ar" => "اسم القطاع (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "sector"

            ]
        ]);
        //customer resource
        Translation::insert([
            [
                "key" => "customer_resource_create_form",
                "default_en" => "Add new customer resource form",
                "default_ar" => "اضف موارد للعملاء جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer resource"
            ],
            [
                "key" => "customer_resource_edit_form",
                "default_en" => "Edit customer resource form",
                "default_ar" => "نموذج تعديل موارد العملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer resource"
            ],
            [
                "key" => "customer_resource_name_ar",
                "default_en" => " name (arabic)",
                "default_ar" => "اسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer resource"

            ],
            [
                "key" => "customer_resource_name_en",
                "default_en" => " name (english)",
                "default_ar" => "اسم  (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer resource"
            ],
            [
                "key" => "customer_resource_parent",
                "default_en" => "Parent name",
                "default_ar" => "اسم الاب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "customer resource"
            ],
        ]);
        //customer groups
        Translation::insert([
            [
                "key" => "customer_groups_create_form",
                "default_en" => "Add New Customer Groups",
                "default_ar" => "اضف مجموعة عملاء جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Customer Group"
            ],
            [
                "key" => "customer_groups_edit_form",
                "default_en" => "Edit Customer Group form",
                "default_ar" => "تعديل نموذج مجموعة العملاء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Customer Group"

            ],
            [
                "key" => "customer_groups_title_ar",
                "default_en" => "title (arabic)",
                "default_ar" => "العنوان (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Customer Group"

            ],
            [
                "key" => "customer_groups_title_en",
                "default_en" => "title (english)",
                "default_ar" => "العنوان (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Customer Group"

            ],
            [
                "key" => "customer_groups_discount",
                "default_en" => "Discount",
                "default_ar" => "الخصم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Customer Group"

            ],
            [
                "key" => "customer_groups_default",
                "default_en" => "Default",
                "default_ar" => "الحالة الافتراضية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Customer Group"

            ],
        ]);
        //general supplier
        Translation::insert([
            [
                "key" => "supplier_create_form",
                "default_en" => "Add new supplier",
                "default_ar" => "اضف زبون جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_edit_form",
                "default_en" => "Edit supplier form",
                "default_ar" => "نموذج تعديل الزبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_country",
                "default_en" => "Country name",
                "default_ar" => "اسم الدولة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_city",
                "default_en" => "City name",
                "default_ar" => "اسم المدينة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_name_ar",
                "default_en" => "Supplier name (arabic)",
                "default_ar" => "اسم المورد (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_name_en",
                "default_en" => "Supplier name (english)",
                "default_ar" => "اسم المورد (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_phone",
                "default_en" => "Supplier phone",
                "default_ar" => "هاتف المورد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_email",
                "default_en" => "email",
                "default_ar" => "بريد الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_nationality",
                "default_en" => "nationality",
                "default_ar" => "جنسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_national_id",
                "default_en" => "national id",
                "default_ar" => "الرقم القومي ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_contact_person",
                "default_en" => " contact person",
                "default_ar" => "معلومات  الشخصية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_contact_phones",
                "default_en" => " contact phones",
                "default_ar" => "هواتف اتصال ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_whatsapp",
                "default_en" => " whatsapp",
                "default_ar" => "رقم واتساب ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_code",
                "default_en" => " code",
                "default_ar" => "كود ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "supplier_passport_number",
                "default_en" => " passport number",
                "default_ar" => "رقم جواز سفر ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "customer_employee",
                "default_en" => "Employee name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "customer_salesman",
                "default_en" => "Salesman name",
                "default_ar" => "اسم البائع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_facebook",
                "default_en" => "Facebook",
                "default_ar" => "الفيسبوك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_instagram",
                "default_en" => "Instagram",
                "default_ar" => "انستجرام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_linkedin",
                "default_en" => "Linkedin",
                "default_ar" => "لنكدان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_snapchat",
                "default_en" => "Snapchat",
                "default_ar" => "سناب شات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_twitter",
                "default_en" => "Twitter",
                "default_ar" => "تويتر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_website",
                "default_en" => "Website",
                "default_ar" => "الموقع الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_note",
                "default_en" => "Note",
                "default_ar" => "الملاحظه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_main_category",
                "default_en" => "Main Category",
                "default_ar" => "الفئه الرئيسيه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_sub_category",
                "default_en" => "Sub Category",
                "default_ar" => "الفئه الرئيسيه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_source",
                "default_en" => "Customer Source",
                "default_ar" => "مصدر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_sector",
                "default_en" => "Sector",
                "default_ar" => "قطاع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_customer",
                "default_en" => "Customer",
                "default_ar" => "زبون",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_customer_group",
                "default_en" => "Customer Group",
                "default_ar" => "مجموعه الم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
            [
                "key" => "supplier_bank_account",
                "default_en" => "Bank account name",
                "default_ar" => "اسم الحساب البنكي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"

            ],
            [
                "key" => "customer_sub_category",
                "default_en" => "Sub Category",
                "default_ar" => "الفئه الرئيسيه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Supplier"
            ],
        ]);
        //brand
        Translation::insert([
            [
                "key" => "brand_create_form",
                "default_en" => "Add New Brand",
                "default_ar" => "اضف ماركة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"
            ],
            [
                "key" => "brand_edit_form",
                "default_en" => "Edit Brand form",
                "default_ar" => "تعديل الماركة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"

            ],
            [
                "key" => "brand_name_ar",
                "default_en" => "Brand Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"

            ],
            [
                "key" => "brand_name_en",
                "default_en" => "Brand Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"

            ]

        ]);
        //group
        Translation::insert([
            [
                "key" => "group_create_form",
                "default_en" => "Add New Group",
                "default_ar" => "اضف مجموعة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"
            ],
            [
                "key" => "group_edit_form",
                "default_en" => "Edit Group form",
                "default_ar" => "تعديل المجموعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"

            ],
            [
                "key" => "group_name_ar",
                "default_en" => "Group Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"

            ],
            [
                "key" => "group_name_en",
                "default_en" => "Group Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"

            ]

        ]);
        //Variant Attributes
        Translation::insert([
            [
                "key" => "variant_attributes_create_form",
                "default_en" => "Add New Variant Attributes",
                "default_ar" => "اضف سمة المتغيرة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Variant Attributes"
            ],
            [
                "key" => "variant_attributes_edit_form",
                "default_en" => "Edit Variant Attributes form",
                "default_ar" => "تعديل السمات المتغيرة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Variant Attributes"

            ],
            [
                "key" => "variant_attributes_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Variant Attributes"

            ],
            [
                "key" => "variant_attributes_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Variant Attributes"

            ]

        ]);
        //taxes
        Translation::insert([
            [
                "key" => "taxes_create_form",
                "default_en" => "Add New Tax",
                "default_ar" => "اضف ضريبة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Taxes"
            ],
            [
                "key" => "taxes_edit_form",
                "default_en" => "Edit Tax form",
                "default_ar" => "تعديل الضريبة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Taxes"

            ],
            [
                "key" => "taxes_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Taxes"

            ],
            [
                "key" => "taxes_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Taxes"

            ],
            [
                "key" => "taxes_percentage",
                "default_en" => "Percentage",
                "default_ar" => "النسبة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Taxes"

            ],
            [
                "key" => "taxes_default",
                "default_en" => "Default",
                "default_ar" => "الحالة الافتراضية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Taxes"

            ],

        ]);
        //inventories
        Translation::insert([
            [
                "key" => "inventories_branch",
                "default_en" => "Branch",
                "default_ar" => "الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_brand",
                "default_en" => "Brand",
                "default_ar" => "الماركة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_category",
                "default_en" => "Category",
                "default_ar" => "الفئة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_group",
                "default_en" => "Group",
                "default_ar" => "المجموعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_re_order",
                "default_en" => "Re order",
                "default_ar" => "اعادة الطلب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_ID",
                "default_en" => "ID",
                "default_ar" => "الرقم التسلسلى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_SKU",
                "default_en" => "SKU",
                "default_ar" => "كود التخزين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Barcode",
                "default_en" => "Barcode",
                "default_ar" => "الباركود",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Item_Name",
                "default_en" => "Item Name",
                "default_ar" => "اسم الصنف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Variant_Name",
                "default_en" => "Variant Name",
                "default_ar" => "اسم المتغير",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Category_Name",
                "default_en" => "Category Name",
                "default_ar" => "اسم الفئه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Group_Name",
                "default_en" => "Group Name",
                "default_ar" => "اسم المجموعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Brand_Name",
                "default_en" => "Brand Name",
                "default_ar" => "اسم الماركة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Purchase_Price",
                "default_en" => "Purchase Price",
                "default_ar" => "سعر الشراء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Selling_Price",
                "default_en" => "Selling Price",
                "default_ar" => "سعر البيع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
            [
                "key" => "inventories_Inventory",
                "default_en" => "Inventory",
                "default_ar" => "الجرد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS inventories"
            ],
        ]);

        // product (point of sale)
        Translation::insert([
            [
                "key" => "pointOfSell_create_form",
                "default_en" => "Add New Product",
                "default_ar" => "اضف منتج جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_edit_form",
                "default_en" => "Edit Product form",
                "default_ar" => "تعديل المنتج",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"

            ],
            [
                "key" => "pointOfSell_show_form",
                "default_en" => "Show Product",
                "default_ar" => "عرض المنتج",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"

            ],
            [
                "key" => "pointOfSell_product_title",
                "default_en" => "Title (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_title_e",
                "default_en" => "Title (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_description",
                "default_en" => "Description (arabic)",
                "default_ar" => "الوصف (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_description_e",
                "default_en" => "Description (english)",
                "default_ar" => "الوصف (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_category",
                "default_en" => "Category",
                "default_ar" => "الفئه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_brand",
                "default_en" => "Brand",
                "default_ar" => "الماركه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_unit",
                "default_en" => "Unit",
                "default_ar" => "الوحده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_group",
                "default_en" => "Group",
                "default_ar" => "مجموعه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_tax",
                "default_en" => "Tax",
                "default_ar" => "الضريبه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_branch",
                "default_en" => "Branch",
                "default_ar" => "الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_product_type",
                "default_en" => "Product Type",
                "default_ar" => "نوع المنتج",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_is_quantity",
                "default_ar" => "هل يوجد كميه؟",
                "default_en" => "is_quantity",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_purchase_price",
                "default_en" => "Purchase Price",
                "default_ar" => "سعر الشراء",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_selling_price",
                "default_en" => "Selling Price",
                "default_ar" => "سعر البيع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_quantity",
                "default_en" => "Quantity",
                "default_ar" => "الكميه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_sku",
                "default_en" => "Sku",
                "default_ar" => "Sku",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_Barcode",
                "default_en" => "Barcode",
                "default_ar" => "الباركود",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_re_order",
                "default_en" => "Re Order",
                "default_ar" => "Re Order",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_created_by",
                "default_en" => "Created By",
                "default_ar" => "بواسطه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
            [
                "key" => "pointOfSell_product_descriptionShow",
                "default_en" => "Description",
                "default_ar" => "الوصف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS product"
            ],
        ]);

        //pos cash register
        Translation::insert([
            [
                "key" => "cash_register_create_form",
                "default_en" => "Add New Cash Register",
                "default_ar" => "اضف تسجيل النقدية جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS Cash Register"
            ],
            [
                "key" => "cash_register_edit_form",
                "default_en" => "Edit Cash Register form",
                "default_ar" => "تعديل نموذج تسجيل النقدية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS Cash Register"

            ],
            [
                "key" => "cash_register_title_ar",
                "default_en" => "title (arabic)",
                "default_ar" => "العنوان (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS Cash Register"

            ],
            [
                "key" => "cash_register_title_en",
                "default_en" => "title (english)",
                "default_ar" => "العنوان (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS Cash Register"

            ],
            [
                "key" => "cash_register_branch",
                "default_en" => "Branch",
                "default_ar" => "الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS Cash Register"

            ],
            [
                "key" => "cash_register_multiple_access",
                "default_en" => "Allow multi user",
                "default_ar" => "السماح لعدة مستخدمين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS Cash Register"

            ],
            [
                "key" => "cash_register_created_by",
                "default_en" => "Created By",
                "default_ar" => "تمت الاضافة بواسطه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "POS Cash Register"

            ],
        ]);

        //booking rooms
        Translation::insert([
            [
                "key" => "room_create_form",
                "default_en" => "Add New Room",
                "default_ar" => "اضف غرفة جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"
            ],
            [
                "key" => "room_edit_form",
                "default_en" => "Edit Room form",
                "default_ar" => "تعديل نموذج الغرفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_code",
                "default_en" => "Code",
                "default_ar" => "الكود",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_price",
                "default_en" => "Price",
                "default_ar" => "السعر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"
            ],
            [
                "key" => "room_description_ar",
                "default_en" => "Description (arabic)",
                "default_ar" => "الوصف (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_description_en",
                "default_en" => "Description (english)",
                "default_ar" => "الوصف (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_unit_status",
                "default_en" => "Status",
                "default_ar" => "الحاله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"
            ],
            [    "key" => "room_number_of_individuals",
                "default_en" => "Number Of Individuals",
                "default_ar" => "عدد الافراد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_extra_guest_price",
                "default_en" => "Extra Guest Price",
                "default_ar" => "سعر الفرد الزياده",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "room_floor",
                "default_en" => "Floor",
                "default_ar" => "الطابق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"
            ]
        ]);

        //booking attendant
        Translation::insert([
            [
                "key" => "attendant_create_form",
                "default_en" => "Add New attendant",
                "default_ar" => "اضف رفيق جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking attendands"
            ],
            [
                "key" => "attendant_edit_form",
                "default_en" => "Edit attendant",
                "default_ar" => "تعديل بيانات المرافق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "attendant_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "attendant_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "attendant_customer",
                "default_en" => "Customer Name",
                "default_ar" => "اسم العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
            [
                "key" => "attendant_national_id",
                "default_en" => "Nationality",
                "default_ar" => "الجنسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"
            ],
            [
                "key" => "attendant_id_number",
                "default_en" => "Id number",
                "default_ar" => "رقم الهوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"
            ],

            [
                "key" => "attendant_notes",
                "default_en" => "Notes",
                "default_ar" => "الملاحظات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Rooms"

            ],
        ]);

        //booking guest
        Translation::insert([
            [
                "key" => "guest_employee",
                "default_en" => "Employee Name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_create_form",
                "default_en" => "Add new guest",
                "default_ar" => "اضف نزيل جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"
            ],
            [
                "key" => "guest_edit_form",
                "default_en" => "Edit guest",
                "default_ar" => "تعديل بيانات النزيل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_phone",
                "default_en" => "Phone",
                "default_ar" => "رقم الهاتف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_email",
                "default_en" => "Email",
                "default_ar" => "البريد الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_id_number",
                "default_en" => "ID Number",
                "default_ar" => "رقم الهوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_address",
                "default_en" => "Address",
                "default_ar" => "العنوان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"
            ],
            [
                "key" => "guest_nationality",
                "default_en" => "Nationality",
                "default_ar" => "الجنسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "guest_passport_number",
                "default_en" => "Passport Number",
                "default_ar" => "رقم جواز السفر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "guest_passport_expiration_date",
                "default_en" => "Passport Expiration Date",
                "default_ar" => "تاريخ انتهاء الجواز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],
            [
                "key" => "guest_visa_number",
                "default_en" => "Visa Number",
                "default_ar" => "رقم التأشيرة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "general customer"

            ],

        ]);

        //booking company
        Translation::insert([
            [
                "key" => "company_create_form",
                "default_en" => "Add new company",
                "default_ar" => "اضف شركة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"
            ],
            [
                "key" => "company_email",
                "default_en" => "Email",
                "default_ar" => "البريد الالكتروني",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "company_employee",
                "default_en" => "Employee Name",
                "default_ar" => "اسم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "company_edit_form",
                "default_en" => "Edit company form",
                "default_ar" => "نموذج تعديل الشركة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "company_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "company_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "company_phone",
                "default_en" => "Phone",
                "default_ar" => "رقم الهاتف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "company_tax_record",
                "default_en" => "Tax record",
                "default_ar" => "السجل الضريبي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "comapny_address",
                "default_en" => "Address",
                "default_ar" => "العنوان",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "comapny_contact_person",
                "default_en" => "Contact Person",
                "default_ar" => "الشخص المسؤل عن الاتصال",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
            [
                "key" => "comapny_contact_phones",
                "default_en" => "Contact Phone",
                "default_ar" => "هاتف الاتصال",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"

            ],
        ]);
        //booking setting
        Translation::insert([
            [
                "key" => "key_value_edit_form",
                "default_en" => "Edit key and value",
                "default_ar" => "تعديل المفتاح والقيمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"
            ],
            [
                "key" => "key",
                "default_en" => "Key",
                "default_ar" => "المفتاح",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"
            ],
            [
                "key" => "value",
                "default_en" => "Value",
                "default_ar" => "القيمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Booking Guest"
            ],

        ]);

        //Payment Method
        Translation::insert([
            [
                "key" => "payment_method_create_form",
                "default_en" => "Add new Payment Method",
                "default_ar" => "اضف طريقة دفع جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Payment Method"
            ],
            [
                "key" => "payment_method_edit_form",
                "default_en" => "Edit Payment Method form",
                "default_ar" => "نموذج تعديل طريقة الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Payment Method"

            ],
            [
                "key" => "payment_method_name_ar",
                "default_en" => "Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Payment Method"

            ],
            [
                "key" => "payment_method_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Payment Method"

            ],
            [
                "key" => "payment_method_default",
                "default_en" => "Is Default",
                "default_ar" => "الافتراضى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Payment Method"

            ],

        ]);

        //check in report
        Translation::insert([
            [
                "key" => "check_in_report_customer",
                "default_en" => "Customer",
                "default_ar" => "نزيل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_serial_number",
                "default_en" => "Serial Number",
                "default_ar" => "الرقم التسلسلى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_date_from",
                "default_en" => "Date From",
                "default_ar" => "من تاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_date_to",
                "default_en" => "Date To",
                "default_ar" => "الى تاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_booking_days",
                "default_en" => "Booking Days",
                "default_ar" => "أيام الحجز",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_check_in_time",
                "default_en" => "Check In Time",
                "default_ar" => "وقت الوصول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_room",
                "default_en" => "Room",
                "default_ar" => "غرفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_price_per_day",
                "default_en" => "Price Per Day",
                "default_ar" => "سعر اليوم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_total",
                "default_en" => "Total",
                "default_ar" => "الاجمالى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],
            [
                "key" => "check_in_report_discount",
                "default_en" => "Discount",
                "default_ar" => "الخصم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "check in report"
            ],

        ]);

        //accommodation invoice report
        Translation::insert([
            [
                "key" => "accommodation_report_customer",
                "default_en" => "Customer",
                "default_ar" => "نزيل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "accommodation invoice report"
            ],
            [
                "key" => "accommodation_report_serial_number",
                "default_en" => "Serial Number",
                "default_ar" => "الرقم التسلسلى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "accommodation invoice report"
            ],
            [
                "key" => "accommodation_report_date",
                "default_en" => "Date",
                "default_ar" => "تاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "accommodation invoice report"
            ],
            [
                "key" => "accommodation_report_room",
                "default_en" => "Room",
                "default_ar" => "غرفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "accommodation invoice report"
            ],
            [
                "key" => "accommodation_report_price_per_day",
                "default_en" => "Price Per Day",
                "default_ar" => "سعر اليوم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "accommodation invoice report"
            ],
            [
                "key" => "accommodation_report_total",
                "default_en" => "Total",
                "default_ar" => "الاجمالى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "accommodation invoice report"
            ],
            [
                "key" => "accommodation_report_discount",
                "default_en" => "Discount",
                "default_ar" => "الخصم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "accommodation invoice report"
            ],

        ]);

        //items report
        Translation::insert([
            [
                "key" => "items_report_customer",
                "default_en" => "Customer",
                "default_ar" => "نزيل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],
            [
                "key" => "items_report_serial_number",
                "default_en" => "Serial Number",
                "default_ar" => "الرقم التسلسلى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],
            [
                "key" => "items_report_date",
                "default_en" => "Date",
                "default_ar" => "تاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],
            [
                "key" => "items_report_item",
                "default_en" => "Service",
                "default_ar" => "الخدمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],
            [
                "key" => "items_report_quantity",
                "default_en" => "Quantity",
                "default_ar" => "الكمية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],
            [
                "key" => "items_report_price_per_day",
                "default_en" => "Price",
                "default_ar" => "السعر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],
            [
                "key" => "items_report_total",
                "default_en" => "Total",
                "default_ar" => "الاجمالى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],
            [
                "key" => "items_report_discount",
                "default_en" => "Discount",
                "default_ar" => "الخصم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "items report"
            ],

        ]);

        //club member reject
        Translation::insert([
            [
                "key" => "member_reject_create_form",
                "default_en" => "Add new member reject",
                "default_ar" => "اضف شطب عضوية جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_edit_form",
                "default_en" => "Edit member reject form",
                "default_ar" => "تعديل شطب العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_branch",
                "default_en" => "Branch",
                "default_ar" => "الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_Serial_Number",
                "default_en" => "Serial Number",
                "default_ar" => "الرقم التسلسلى",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_member",
                "default_en" => "Member",
                "default_ar" => "العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_session_number",
                "default_en" => "session number",
                "default_ar" => "رقم الجلسه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_reason",
                "default_en" => "reason",
                "default_ar" => "السبب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_note",
                "default_en" => "note",
                "default_ar" => "ملاحظات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],
            [
                "key" => "member_reject_entity",
                "default_en" => "Authority",
                "default_ar" => "الجهة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "member reject"
            ],

        ]);

        // club group
        Translation::insert([
            [
                "key" => "club_group_create_form",
                "default_en" => "Add New Group",
                "default_ar" => "اضف مجموعة جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"
            ],
            [
                "key" => "club_group_edit_form",
                "default_en" => "Edit Group form",
                "default_ar" => "تعديل المجموعة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"

            ],
            [
                "key" => "club_group_name_ar",
                "default_en" => "Group Name (arabic)",
                "default_ar" => "الاسم (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"

            ],
            [
                "key" => "club_group_name_en",
                "default_en" => "Group Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Group"

            ]

        ]);

        //club subscription
        Translation::insert([
            [
                "key" => "new_subscription_create_form",
                "default_en" => "Add Subscription",
                "default_ar" => "اضافة اشتراك لعضو جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],
            [
                "key" => "new_subscription_edit_form",
                "default_en" => "Edit Subscription form",
                "default_ar" => "تعديل اشتراك لعضو جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],
            [
                "key" => "new_subscription_member",
                "default_en" => "Member",
                "default_ar" => "العضو",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],
            [
                "key" => "new_subscription_year_from",
                "default_en" => "From Year",
                "default_ar" => "تاريخ بداية الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],
            [
                "key" => "new_subscription_year_to",
                "default_en" => "To Year",
                "default_ar" => "تاريخ نهاية الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],
            [
                "key" => "new_subscription_type",
                "default_en" => "Type",
                "default_ar" => "نوع الدفع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],
            [
                "key" => "new_subscription_amount",
                "default_en" => "Subscription Amount",
                "default_ar" => "قيمة الاشتراك",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],
            [
                "key" => "new_subscription_number_of_years",
                "default_en" => "Number Of Years",
                "default_ar" => "عدد السنين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "New Subscription"
            ],

        ]);
        //club statuses
        Translation::insert([
            [
                "key" => "club_statuses_create_form",
                "default_en" => "Add new status",
                "default_ar" => "اضف حاله جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"
            ],
            [
                "key" => "club_statuses_edit_form",
                "default_en" => "Edit status form",
                "default_ar" => "نموذج تعديل الحاله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "club_statuses_name_ar",
                "default_en" => "status name (arabic)",
                "default_ar" => "اسم الحاله (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "club_statuses_name_en",
                "default_en" => "status name (english)",
                "default_ar" => "اسم الحاله (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "units"

            ],
            [
                "key" => "club_statuses_member_type",
                "default_en" => "Member Type",
                "default_ar" => "نوع العضوية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "club"
            ],
        ]);

        //floor
        Translation::insert([
            [
                "key" => "floor_create_form",
                "default_en" => "Add New Floor",
                "default_ar" => "اضف طابق جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"
            ],
            [
                "key" => "floor_edit_form",
                "default_en" => "Edit Floor form",
                "default_ar" => "تعديل الطابق",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"

            ],
            [
                "key" => "floor_name_ar",
                "default_en" => " Name (arabic)",
                "default_ar" => "الاسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"

            ],
            [
                "key" => "floor_name_en",
                "default_en" => "Name (english)",
                "default_ar" => "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Brand"

            ]

        ]);

        //attendance
        Translation::insert([
            [
                "key" => "attendance_fingerprint_device",
                "default_en" => " The employee's number on the fingerprint device",
                "default_ar" => "رقم الموظف على جهاز البصمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance"

            ],
            [
                "key" => "attendance_vdate",
                "default_en" => "Fingerprint history",
                "default_ar" => "تاريخ البصمه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance"

            ],
            [
                "key" => "attendance_type",
                "default_en" => "Fingerprint type",
                "default_ar" => "نوع البصمة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance"

            ],
            [
                "key" => "attendance_Fingerprint_number",
                "default_en" => "Fingerprint device number",
                "default_ar" => "رقم جهاز البصمه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance"
            ]

        ]);

        //attendance times
        Translation::insert([
            [
                "key" => "attendance_times_create_form",
                "default_en" => "Add new attendance times",
                "default_ar" => "اضف وقت حضور جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"
            ],
            [
                "key" => "attendance_times_edit_form",
                "default_en" => "Edit attendance times form",
                "default_ar" => "نموذج تعديل وقت الحضور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_times_name_ar",
                "default_en" => " Name (arabic)",
                "default_ar" => "الاسم (العربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_times_name_en",
                "default_en" => " Name (english)",
                "default_ar" =>  "الاسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_times_tt_type",
                "default_en" => "Work type",
                "default_ar" => "نوع دوام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_times_tt_monthly_hours",
                "default_en" => "Number of monthly working hours",
                "default_ar" => "عدد ساعات الدوام الشهرية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"
            ]

        ]);

        //attendance times detail
        Translation::insert([
            [
                "key" => "attendance_detail_day_no",
                "default_en" => "Day",
                "default_ar" => "يوم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_detail_is_off",
                "default_en" => " holiday ?",
                "default_ar" =>  "يوم عطله ؟",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_detail_shift1_from",
                "default_en" => "Shift 1 (from)",
                "default_ar" => "شفت 1 (من)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_detail_shift1_to",
                "default_en" => "Shift 1 (to)",
                "default_ar" => "شفت 1 (الي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"
            ],
            [
                "key" => "attendance_detail_shift2_from",
                "default_en" => "Shift 2 (from)",
                "default_ar" => "شفت 2 (من)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_detail_shift2_to",
                "default_en" => "Shift 2 (to)",
                "default_ar" => "شفت 2 (الي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"
            ]
        ]);

        //attendance setting
        Translation::insert([
            [
                "key" => "attendance_setting_edit_form",
                "default_en" => "Edit Attendance Setting form",
                "default_ar" => "نموذج تعديل اعدادات الحضور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance setting"

            ],
            [
                "key" => "attendance_setting_pre_in",
                "default_en" => "It is allowed to arrive a few minutes before the appointment",
                "default_ar" => "مسموح الحضور قبل الموعد بكام دقيقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance setting"

            ],
            [
                "key" => "attendance_setting_post_in",
                "default_en" => "A fingerprint is allowed after the attendance time, without a delay of a few minutes",
                "default_ar" =>  "مسموح بصمه بعد موعد الحضور بدون تاخير بكام دقيقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance setting"

            ],
            [
                "key" => "attendance_setting_absent_minutes",
                "default_en" => "It is considered late after a few minutes",
                "default_ar" => "يعتبر متاخر بعد كام دقيقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance setting"
            ],
            [
                "key" => "attendance_setting_pre_out",
                "default_en" => "It is allowed to leave a few minutes before the appointment",
                "default_ar" => "مسموح الانصراف قبل الموعد بكام دقيقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance setting"
            ],
            [
                "key" => "attendance_setting_post_out",
                "default_en" => "Dismissal is considered (without additional fees) after a few minutes",
                "default_ar" => "يعتبر الانصراف (بدون اضافي) بعد كام دقيقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance setting"

            ],
            [
                "key" => "attendance_setting_max_out",
                "default_en" => "The maximum fingerprint is a few minutes after the departure time",
                "default_ar" => "اقصي بصمه بعد الموعد الانصراف بكام دقيقه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"
            ]
        ]);

        //employees timetables header
        Translation::insert([
            [
                "key" => "time_employee_create_form",
                "default_en" => "Add new Staff Time",
                "default_ar" => "اضف دوام للموظفين ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"
            ],
            [
                "key" => "time_employee_edit_form",
                "default_en" => "Edit Staff Time form",
                "default_ar" => "نموذج تعديل دوام الموظفين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "time_employee_timetables_header",
                "default_en" => "work hours",
                "default_ar" => "الدوام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "time_employee_start_from",
                "default_en" => "Start From",
                "default_ar" =>  "بدايه من",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
        ]);

        //Attendance And Departure
        Translation::insert([
            [
                "key" => "attendance_and_departure_employee_id",
                "default_en" => "Employee number",
                "default_ar" => "رقم الموظف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_employee_name",
                "default_en" => "Name",
                "default_ar" => "الاسم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_day",
                "default_en" => "Day",
                "default_ar" => "اليوم",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_attendance",
                "default_en" => "Attendance",
                "default_ar" => "الحضور",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_departure",
                "default_en" => "Departure",
                "default_ar" => "الانصراف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_attendance_shift2",
                "default_en" => "Attendance (shift 2)",
                "default_ar" => "حضور (شفت 2 )",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_departure_shift2",
                "default_en" => "Departure (shift 2)",
                "default_ar" => "الانصراف (شفت 2 )",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_delay",
                "default_en" => "Delay",
                "default_ar" => "تاخير",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_additional",
                "default_en" => "Additional",
                "default_ar" => "اضافي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_Actualnumberofhours",
                "default_en" => "Actual number of hours",
                "default_ar" => "عدد ساعات فعليه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_absence",
                "default_en" => "absence ?",
                "default_ar" => "غياب ؟",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_note",
                "default_en" => "Note",
                "default_ar" => "ملاحظه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"

            ],
            [
                "key" => "attendance_and_departure_employees",
                "default_en" => "employees",
                "default_ar" => "الموظفين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "attendance times"
            ]
        ]);

        //money voucher
        Translation::insert([
            [
                "key" => "money_voucher_create_form",
                "default_en" => "Add new money voucher",
                "default_ar" => "اضف مستند مالي ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"
            ],
            [
                "key" => "money_voucher_edit_form",
                "default_en" => "Edit money voucher form",
                "default_ar" => "نموذج تعديل المستند المالي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_branch",
                "default_en" => "Branch",
                "default_ar" => "الفرع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_serial_number",
                "default_en" => "Serial Number",
                "default_ar" => "الرقم المسلسل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_date",
                "default_en" => "Date",
                "default_ar" => "التاريخ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_customer",
                "default_en" => "Client",
                "default_ar" => "العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_amount",
                "default_en" => "Amount",
                "default_ar" => "المبلغ",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_payment_method",
                "default_en" => "Payment Method",
                "default_ar" => "طريقة السداد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_salesmen",
                "default_en" => "Salesmen",
                "default_ar" => "موظف المبيعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_document",
                "default_en" => "Document Type",
                "default_ar" => "نوع المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],
            [
                "key" => "money_voucher_client_type",
                "default_en" => "Client Type",
                "default_ar" => "نوع العميل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "Money Voucher"

            ],

        ]);
        //general message
        Translation::insert([
            [
                "key" => "message_create_form",
                "default_en" => "Add new Message",
                "default_ar" => "اضف رساله جديدة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "message_edit_form",
                "default_en" => "Edit Message form",
                "default_ar" => "نموذج تعديل الرساله",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "message_content_ar",
                "default_en" => "Content (arabic)",
                "default_ar" => "المحتوي (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "message_content_en",
                "default_en" => "Content (english)",
                "default_ar" => "المحتوي (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "message_type",
                "default_en" => "type",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ],
            [
                "key" => "message_variable",
                "default_en" => "Variable",
                "default_ar" => "المتغيرات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "avenue"
            ]
        ]);

        //general lawyer
        Translation::insert([
            [
                "key" => "lawyer_create_form",
                "default_en" => "Add new lawyer",
                "default_ar" => "اضف محامي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "lawyer_edit_form",
                "default_en" => "Edit lawyer form",
                "default_ar" => "تعديل نموذج المحامي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "lawyer_name_ar",
                "default_en" => "name (arabic)",
                "default_ar" => "اسم  (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "lawyer_name_en",
                "default_en" => "name (english)",
                "default_ar" => "اسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "lawyer_account_no",
                "default_en" => "Account Number",
                "default_ar" => "رقم الحساب",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
        ]);

        //general lawyer
        Translation::insert([
            [
                "key" => "legal_procedure_create_form",
                "default_en" => "Add new lawyer",
                "default_ar" => "اضف محامي جديد",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "legal_procedure_edit_form",
                "default_en" => "Edit lawyer form",
                "default_ar" => "تعديل نموذج المحامي",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "legal_procedure_name_ar",
                "default_en" => "name (arabic)",
                "default_ar" => "اسم  (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
            [
                "key" => "legal_procedure_name_en",
                "default_en" => "name (english)",
                "default_ar" => "اسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "branch"
            ],
        ]);

        //general document linked categories
        Translation::insert([
            [
                "key" => "document_linked_categories_create_form",
                "default_en" => "Add new document linked categories",
                "default_ar" => "اضافة تصنيفات مرتبطه بالمستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document linked categories"
            ],
            [
                "key" => "document_linked_categories_edit_form",
                "default_en" => "Edit document linked categories",
                "default_ar" => "تعديل تصنيفات مرتبطه بالمستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document linked categories"
            ],
            [
                "key" => "document_linked_categories_name_ar",
                "default_en" => "name (arabic)",
                "default_ar" => "اسم  (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document linked categories"
            ],
            [
                "key" => "document_linked_categories_name_en",
                "default_en" => "name (english)",
                "default_ar" => "اسم (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document linked categories"
            ],
            [
                "key" => "document_linked_categories_title_ar",
                "default_en" => "title (arabic)",
                "default_ar" => "العنوان (عربي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document linked categories"
            ],
            [
                "key" => "document_linked_categories_title_en",
                "default_en" => "title (english)",
                "default_ar" => "العنوان (انجليزي)",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document linked categories"
            ],
            [
                "key" => "document_linked_categories_db_table",
                "default_en" => "database table",
                "default_ar" => "جدول قواعد البيانات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "document linked categories"
            ],
        ]);

        //general link documents with status
        Translation::insert([
            [
                "key" => "link_documents_with_status_create_form",
                "default_en" => "Add new link documents with status",
                "default_ar" => "اضافة ربط المستندات والحالات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "link documents with status"
            ],
            [
                "key" => "link_documents_with_status_edit_form",
                "default_en" => "Edit link documents with status",
                "default_ar" => "تعديل ربط المستندات والحالات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "link documents with status"
            ],
            [
                "key" => "link_documents_with_status_document",
                "default_en" => "Document",
                "default_ar" => "المستند",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "link documents with status"
            ],
            [
                "key" => "link_documents_with_status_document_module_type",
                "default_en" => "document linked categories",
                "default_ar" => "تصنيفات مرتبطه بالمستندات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "link documents with status"
            ],
            [
                "key" => "link_documents_with_status_status",
                "default_en" => "Status",
                "default_ar" => "الحالة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "link documents with status"
            ],
        ]);

        // House Keeping Daily
        Translation::insert([
            [
                "key" => "houseKeepingDaily_rome",
                "default_en" => "Rome.No",
                "default_ar" => "رقم الغرفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
            [
                "key" => "houseKeepingDaily_category",
                "default_en" => "Category",
                "default_ar" => "الفئة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
            [
                "key" => "houseKeepingDaily_nationality",
                "default_en" => "Nationality",
                "default_ar" => "الجنسية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
            [
                "key" => "houseKeepingDaily_HouseKeeping_Name",
                "default_en" => "HouseKeeping Name",
                "default_ar" => "اسم موظف خدمة الغرف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
            [
                "key" => "houseKeepingDaily_startingTime",
                "default_en" => "Starting Time",
                "default_ar" => "وقت البداية",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
            [
                "key" => "houseKeepingDaily_endingTime",
                "default_en" => "Ending Time",
                "default_ar" => "وقت النهايه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
            [
                "key" => "houseKeepingDaily_cleaned",
                "default_en" => "Cleaned ?",
                "default_ar" => "تم تنظيفها ؟",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
            [
                "key" => "houseKeepingDaily_remarks",
                "default_en" => "HouseKeeping Remarks",
                "default_ar" => "ملاحظات خدمة الغرف",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "houseKeepingDaily"
            ],
        ]);

        //daily-checked-in
        Translation::insert([
            [
                "key" => "dailyCheckedIn_rome",
                "default_en" => "Rome.No",
                "default_ar" => "رقم الغرفة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_guest_name",
                "default_en" => "Guest Name",
                "default_ar" => "اسم النزيل",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_nationality",
                "default_en" => "Nationality",
                "default_ar" => "الجنسيه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_pax",
                "default_en" => "PAX",
                "default_ar" => "المرافقين",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_type",
                "default_en" => "Type",
                "default_ar" => "النوع",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_Arrival",
                "default_en" => "Arrival",
                "default_ar" => "الوصول",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_departure",
                "default_en" => "Departure",
                "default_ar" => "المغادرة",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_totalDays",
                "default_en" => "Total Days",
                "default_ar" => "عدد الايام",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_discount_on_rate",
                "default_en" => "Discount On Rate",
                "default_ar" => "الخصم على السعر",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_accomodationNetRate",
                "default_en" => "Accomodation Net Rate",
                "default_ar" => "صافي سعر الإقامه",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
            [
                "key" => "dailyCheckedIn_payments",
                "default_en" => "Payments",
                "default_ar" => "المدفوعات",
                "new_ar" => "",
                "new_en" => "",
                "company_id" => 0,
                "screen" => "daily-checked-in"
            ],
        ]);

    }
}

