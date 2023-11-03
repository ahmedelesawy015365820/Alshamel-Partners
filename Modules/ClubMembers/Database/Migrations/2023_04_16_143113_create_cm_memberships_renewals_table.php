<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cm_memberships_renewals', function (Blueprint $table) {
            $table->id();
            $table->string('from')->unique();
            $table->string('to')->unique();

            $table->boolean('membership_availability')->default(0);
            $table->unsignedDouble('membership_cost');

            $table->boolean('renewal_availability')->default(0);
            $table->unsignedDouble('renewal_cost');
            $table->unsignedInteger('company_id')->default(0);

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
        Schema::dropIfExists('cm_memberships_renewals');
    }
};
