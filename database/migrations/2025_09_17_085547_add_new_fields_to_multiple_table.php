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
        Schema::table('stocks', function (Blueprint $table) {
            $table->double('purchase_without_tax')->default(0)->after('batch_no');
            $table->double('purchase_with_tax')->default(0)->after('purchase_without_tax');
            $table->double('sales_price')->default(0)->after('purchase_with_tax');
            $table->double('wholesale_price')->default(0)->after('sales_price');
            $table->double('dealer_price')->default(0)->after('wholesale_price');
            $table->double('profit_percent')->default(0)->after('dealer_price');
            $table->string('mfg_date')->nullable()->after('profit_percent');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('product_type')->default('single')->after('tax_type');
            $table->double('tax_amount', 10, 2)->default(0)->after('product_type');
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->foreignId('stock_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->foreignId('stock_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->string('mfg_date')->nullable()->after('profit_percent');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['purchase_without_tax', 'purchase_with_tax', 'sales_price', 'wholesale_price','dealer_price','profit_percent','mfg_date']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['product_type', 'tax_amount']);
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn('stock_id');
        });

        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn(['stock_id', 'mfg_date']);
        });
    }
};
