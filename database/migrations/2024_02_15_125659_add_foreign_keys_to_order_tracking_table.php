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
        Schema::table('order_tracking', function (Blueprint $table) {
            $table->foreign(['order_id'], 'order_tracking_ibfk_1')->references(['id'])->on('orders')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_tracking', function (Blueprint $table) {
            $table->dropForeign('order_tracking_ibfk_1');
        });
    }
};
