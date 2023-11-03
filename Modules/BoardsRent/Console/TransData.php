<?php

namespace Modules\BoardsRent\Console;

use App\Models\City;
use App\Models\Governorate;
use App\Models\Street;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'board-rent:trans-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->packages();
        $this->info('packages done');
        $this->categories();
        $this->info('categories done');
        $this->unitstatus();
        $this->info('unitstatus done');
        $this->status();
        $this->info('status done');
        $this->locations();
        $this->info('locations done');
        $this->panels();
        $this->info('panels done');
        $this->drop();
        $this->info('drop done');

    }

    public function packages()
    {
        Schema::dropIfExists('packages');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/packages.sql'));
        DB::unprepared($sql);

        $packages = DB::table("packages")->get();

        foreach ($packages as $package) {
            \Modules\BoardsRent\Entities\Package::create([
                'id' => $package->id,
                'name' => $package->name,
                'name_e' => $package->name,
                'code' => $package->code,
                'price' => $package->price,
            ]);
        }

    }

    private function unitstatus()
    {
        Schema::dropIfExists('unitstatus');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/unitstatus.sql'));
        DB::unprepared($sql);
        // run file
        $unit_statues = DB::table("unitstatus")->get();
        foreach ($unit_statues as $new_status) {
            $name = json_decode($new_status->name);
            $created = \Modules\BoardsRent\Entities\UnitStatus::create([
                'id' => $new_status->id,
                'name' => $name->ar,
                'name_e' => $name->en,
                "status_id" => 1,
            ]);
        }

    }

    private function status()
    {
        Schema::dropIfExists('status');

        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/status.sql'));
        DB::unprepared($sql);
        $statuses = DB::table("status")->get();
        foreach ($statuses as $status) {
            $name = json_decode($status->name);
            \App\Models\Status::create([
                'id' => $status->id,
                'name' => $name->ar,
                'name_e' => $name->en,
                'color' => $status->color,
                'icon' => $status->icon,
            ]);
        }

    }

    private function locations()
    {
        Schema::dropIfExists('locations');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/locations.sql'));
        DB::unprepared($sql);
        Schema::table("general_governorates", function ($table) {
            $table->integer('location_id')->nullable();

        });

        Schema::table("general_cities", function ($table) {
            $table->integer('location_id')->nullable();

        });
        // make location_id fillable in here

        $governorates = DB::table("locations")->whereNull('parent_id')->get();
        foreach ($governorates as $governorate) {
            $name = json_decode($governorate->name);
            $governorate = \App\Models\Governorate::create([
                'id' => $governorate->id,
                'name' => $name->ar,
                'name_e' => $name->en,
                "is_active" => "inactive",
                "country_id" => 1,
                'location_id' => $governorate->id,
            ]);
            $cities = DB::table("locations")->where('parent_id', $governorate->id)->get();

            foreach ($cities as $city) {

                $city_name = json_decode($city->name);
                \App\Models\City::create([
                    "country_id" => 1,
                    "governorate_id" => $governorate->id,
                    "name" => $city_name->ar,
                    "name_e" => $city_name->en,
                    'location_id' => $city->id,
                ]);

            }

        }

    }

    private function categories()
    {
        Schema::dropIfExists('categories');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/categories.sql'));
        DB::unprepared($sql);
        // run file
        $models = DB::table("categories")->get();
        foreach ($models as $model) {
            $name = json_decode($model->name);
            \App\Models\Category::create([
                'id' => $model->id,
                'name' => $name->ar,
                'name_e' => $name->en,
            ]);
        }

    }

    private function panels()
    {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('banner_category');
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/banners.sql'));
        DB::unprepared($sql);
        $sql = file_get_contents(base_path('Modules/BoardsRent/Resources/assets/db/banner_category.sql'));
        DB::unprepared($sql);

        $models = DB::table("banners")->get();
        foreach ($models as $model) {
            $price = json_encode([
                'day' => $model->price,
                'week' => $model->price_2week / 2,
                'month' => $model->price_month,
                "quarter_year" => $model->price_3month,
                "half_year" => $model->price_6month,
                "year" => $model->price_year,
            ]);
            $category_id = DB::table("banner_category")->where('bannar_id', $model->id)->first();

            $category_id = $category_id ? $category_id->category_id : null;
            Schema::table("boards_rent_panels", function ($table) {
                $table->string('size')->nullable()->change();
                $table->double('lat')->nullable()->change();
                $table->double('lng')->nullable()->change();

            });
            $face = $model->is_double_face ? 'Multi' : $model->face;
            if (!$face) {
                $face = 'One-Face';
            }
            $location = DB::table('locations')->where('id', $model->location_id)->first();
            $governorate_id = null;
            $city_id = null;
            if ($location) {
                if ($location->parent_id) {
                    $city_id = City::where("location_id", $location->id)->first();
                    $city_id = $city_id ? $city_id->id : null;
                } else {
                    $governorate_id = Governorate::where("location_id", $location->id)->first();
                    $governorate_id = $governorate_id ? $governorate_id->id : null;
                }

            }

            $avenue = \App\Models\Avenue::create([
                "name" => "منطقة الافتراضية",
                "name_e" => "Default Avenue",
            ]);
            $street_id = null;
            if ($model->street_name) {
                $street = Street::where('name', $model->street_name)->first();
                if ($street) {
                    $street_id = $street->id;
                } else {
                    $street = Street::create([
                        "name" => $model->street_name,
                        "name_e" => $model->street_name,
                        "avenue_id" => $avenue->id,
                    ]);
                    $street_id = $street->id;
                }
            }

            \Modules\BoardsRent\Entities\Panel::create([
                'name' => $model->name,
                'price' => $price,
                "code" => $model->code,
                "new_code" => $model->new_code,
                "size" => $model->size,
                "note" => $model->note,
                "face" => $face,
                "unit_status_id" => $model->unit_status_id,
                "category_id" => $category_id,
                "lat" => $model->latitude,
                "lng" => $model->longitude,
                "country_id" => 1,
                "governorate_id" => $governorate_id,
                "city_id" => $city_id,
                "street_id" => $street_id,
            ]);

        }

    }

    private function drop()
    {
        Schema::table("general_governorates", function ($table) {
            $table->dropColumn('location_id');

        });

        Schema::table("general_cities", function ($table) {
            $table->dropColumn('location_id');

        });

        Schema::dropIfExists('banners');
        Schema::dropIfExists('banner_category');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('status');
        Schema::dropIfExists('unitstatus');
        Schema::dropIfExists('packages');

    }

}
