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
        Schema::create('rlst_building_policies', function (Blueprint $table) {
            $table->engine = 'InnoDB'; 
            $table->id();
            $table->unsignedInteger('building_id');
            //$table->foreign('building_id')->references('id')->on('rlst_buildings')->comment('عقار');
            $table->date('date')->date_format('Y-m')->comment('تاريخ تطبيق السياسة');
            $table->unsignedBigInteger('policy_id');
            $table->foreign('policy_id')->references('id')->on('rlst_policies')->comment(' السياسةالادارية للعقار');
            $table->double('amount')->comment('المبلغ سواء للشركة او للمالك');
            $table->double('percentage', 5, 2)->comment('نسبة الشركة من المحصل');

            $table->unique(['building_id', 'date']);

        });
    }

    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rlst_building_policies');
    }
};
