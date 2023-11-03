<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rlst_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->default(0);
            $table->string('name', 100)->default(0);
            $table->string('name_e', 100)->default(0);
            $table->text('description')->nullable();
            $table->text('description_e')->nullable();
            $table->integer('unit_ty')->nullable()->default(0);
            $table->unsignedInteger('unit_status_id')->nullable()->default(0);
            $table->double('unit_area')->nullable()->default(0);
            $table->double('unit_net_area')->nullable()->default(0)->comment('صافي');
            $table->unsignedInteger('rooms')->nullable()->default(0);
            $table->unsignedInteger('path')->nullable()->default(0);
            $table->unsignedInteger('view')->nullable()->default(0);
            $table->unsignedInteger('floor')->nullable()->default(0);
            $table->integer('finishing')->nullable()->default(0);
            $table->unsignedInteger('building_id')->nullable()->default(0);
            $table->json('properties')->nullable();
            $table->string("module")->nullable();
            $table->softDeletes();
            $table->timestamps();

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rlst_units');
    }
};
