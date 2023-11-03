<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('general_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->nullable();
            $table->foreignId('branch_id')->constrained('general_branches')->references("id")->nullable()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("name" , 100)->nullable()->comment("Name Arabic");
            $table->string("name_e" , 100)->nullable()->comment("Name English");
            $table->string('is_active')->default('active');
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
        Schema::dropIfExists('general_stores');
    }
}
