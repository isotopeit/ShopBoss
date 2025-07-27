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
        Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->foreignId('purchase_detail_id')->constrained('purchase_details');
            $table->string('product_name')->nullable();
            $table->string('product_code')->nullable();
            $table->float('unit_price');
            $table->float('quantity');
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
        Schema::dropIfExists('purchase_return_details');
    }
};