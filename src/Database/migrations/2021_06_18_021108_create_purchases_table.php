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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->date('date');
            $table->string('reference', 32);
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('supplier_name', 128);
            $table->float('tax_percentage')->default(0);
            $table->float('tax_amount')->default(0);
            $table->float('discount_percentage')->default(0);
            $table->float('discount_amount')->default(0);
            $table->float('shipping_amount')->default(0);
            $table->float('total_amount');
            $table->float('paid_amount');
            $table->float('due_amount');
            $table->string('payment_status', 16);
            $table->string('payment_method', 16);
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
        Schema::dropIfExists('purchases');
    }
};