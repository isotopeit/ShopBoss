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
        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('sale_id')->constrained('sales');
            $table->foreignId('sale_return_id')->constrained('sale_returns');
            $table->foreignId('sale_detail_id')->constrained('sale_details');
            $table->foreignId('product_id')->constrained('products');
            $table->string('product_name', 128);
            $table->string('product_code', 32);
            $table->float('quantity');
            $table->float('unit_price');
            $table->float('sub_total');
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
        Schema::dropIfExists('sale_return_details');
    }
};