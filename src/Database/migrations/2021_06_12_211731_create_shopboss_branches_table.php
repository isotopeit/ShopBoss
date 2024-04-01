<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shopboss_branches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('branch_name', 64);
            $table->string('branch_description', 128)->nullable();
            $table->integer('branch_no')->default(1);
            $table->string('branch_location', 128)->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopboss_branches');
    }
};
