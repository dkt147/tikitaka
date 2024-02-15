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
        Schema::table('shop_review', function (Blueprint $table) {
            $table->foreign(['shop_id'], 'shop_review_ibfk_1')->references(['id'])->on('shop')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_review', function (Blueprint $table) {
            $table->dropForeign('shop_review_ibfk_1');
        });
    }
};
