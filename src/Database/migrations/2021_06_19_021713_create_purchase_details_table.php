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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('branch_id')->constrained('shopboss_branches');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->string('product_name', 128);
            $table->string('product_code', 32);
            $table->float('unit_price');
            $table->float('purchase_qty');
            $table->float('product_discount_amount');
            $table->float('product_tax_amount');
            $table->float('sub_total');
            $table->float('sale_qty')->default(0);
            $table->float('purchase_return_qty')->default(0);
            $table->float('available_qty')->default(0);
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
        Schema::dropIfExists('purchase_details');
    }
};