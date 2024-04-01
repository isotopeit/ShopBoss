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
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('branch_id')->constrained('shopboss_branches');
            $table->foreignId('sale_id')->constrained('sales');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('purchase_detail_id')->constrained('purchase_details');
            $table->string('product_name', 128);
            $table->string('product_code', 32);
            $table->float('quantity');
            $table->float('return_qty')->default(0);
            $table->float('price');
            $table->float('unit_price');
            $table->float('sub_total');
            $table->float('product_discount_amount');
            $table->float('product_tax_amount');
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
        Schema::dropIfExists('sale_details');
    }
};