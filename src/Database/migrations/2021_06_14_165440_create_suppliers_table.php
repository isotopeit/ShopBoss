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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('supplier_name', 128);
            $table->string('supplier_email', 64)->nullable();
            $table->string('supplier_phone', 16);
            $table->string('company_name', 64)->nullable();
            $table->string('city', 32)->nullable();
            $table->string('country', 32)->nullable();
            $table->string('address', 191)->nullable();
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
        Schema::dropIfExists('suppliers');
    }
};