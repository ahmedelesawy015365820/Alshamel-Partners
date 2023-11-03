<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Type::addType('tinyInteger', 'Doctrine\DBAL\Types\SmallIntType');

        DB::statement('UPDATE general_users SET is_active = IF(is_active = "active", 1, 0) WHERE is_active IS NOT NULL');

        Schema::table('general_users', function (Blueprint $table) {
            $table->boolean("is_active")->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_users', function (Blueprint $table) {
            $table->string("is_active")->default('active')->change();
        });
        DB::statement('UPDATE general_users SET is_active = IF(is_active = 1, "active", "inactive") WHERE is_active IS NOT NULL');

    }
};
