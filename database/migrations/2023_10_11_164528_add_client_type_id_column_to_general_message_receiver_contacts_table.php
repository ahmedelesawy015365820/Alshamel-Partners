<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MessageReceiverContact;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_message_receiver_contacts', function (Blueprint $table) {
            $table->foreignIdFor(MessageReceiverContact::class, 'client_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_message_receiver_contacts', function (Blueprint $table) {
            $table->dropForeign(['client_type_id'])->change();
        });
    }
};
