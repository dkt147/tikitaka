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
        Schema::create('product', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('category_id')->nullable()->index('category_id');
            $table->integer('shop_id')->nullable()->index('shop_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->decimal('price', 10);
            $table->string('currency', 3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
};
