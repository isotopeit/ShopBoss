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
        Schema::create('adjusted_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('adjustment_id')->constrained('adjustments');
            $table->foreignId('product_id');
            $table->float('quantity');
            $table->string('type');
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adjusted_products');
    }
};