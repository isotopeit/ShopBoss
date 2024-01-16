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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('category_name', 32);
            $table->string('product_name', 128);
            $table->string('product_code', 32)->nullable();
            $table->float('product_cost')->default(0);
            $table->float('product_price')->default(0);
            $table->string('uom', 16)->nullable();
            $table->float('product_stock_alert');
            $table->string('product_note', 100)->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};