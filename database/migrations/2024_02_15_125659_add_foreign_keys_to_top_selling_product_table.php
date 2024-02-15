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
        Schema::table('top_selling_product', function (Blueprint $table) {
            $table->foreign(['product_id'], 'top_selling_product_ibfk_1')->references(['id'])->on('product')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('top_selling_product', function (Blueprint $table) {
            $table->dropForeign('top_selling_product_ibfk_1');
        });
    }
};
