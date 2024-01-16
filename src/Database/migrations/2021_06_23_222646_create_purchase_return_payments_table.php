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
        Schema::create('purchase_return_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('purchase_return_id')->constrained('purchase_returns');
            $table->float('amount');
            $table->date('date');
            $table->string('reference', 32);
            $table->string('payment_method', 32);
            $table->string('note', 100)->nullable();
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
        Schema::dropIfExists('purchase_return_payments');
    }
};