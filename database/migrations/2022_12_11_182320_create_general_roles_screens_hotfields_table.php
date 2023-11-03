<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::create('general_roles_screens_hotfields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('general_roles')->references("id")->nullable();
            $table->unsignedInteger('workflow_id')->nullable();
            $table->unsignedInteger('hotfield_id')->nullable();
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
        Schema::dropIfExists('general_roles_screens_hotfields');
    }
};
