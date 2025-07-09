<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            }
        });
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            }
        });
        Schema::table('purchase_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_payments', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            }
        });
        Schema::table('sale_return_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_return_payments', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            }
        });
        Schema::table('purchase_return_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_return_payments', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            }
        });

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove branch_id and foreign key from each table
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
        Schema::table('purchase_payments', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_payments', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
        Schema::table('sale_return_payments', function (Blueprint $table) {
            if (Schema::hasColumn('sale_return_payments', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });
        Schema::table('purchase_return_payments', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_return_payments', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
        });


    }
};
