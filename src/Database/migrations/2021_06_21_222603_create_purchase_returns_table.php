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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->date('date');
            $table->string('reference', 32);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('supplier_name', 64);
            $table->float('damaged_percentage');
            $table->float('damaged_amount');
            $table->float('total_amount');
            $table->float('paid_amount');
            $table->float('due_amount');
            $table->string('payment_status', 32);
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
        Schema::dropIfExists('purchase_returns');
    }
};