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
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('tax_name')->nullable()->after('shopOpeningBalance');
            $table->string('tax_no')->nullable()->after('shopOpeningBalance');
        });
        Schema::table('parties', function (Blueprint $table) {
            $table->dropUnique('parties_phone_unique');
            $table->string('phone')->nullable()->change();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->double('dealer_price')->default(0)->after('wholesale_price');
            $table->double('alert_qty', 10, 2)->default(0)->change();
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['type_id']);
            $table->dropForeign(['manufacturer_id']);
            $table->dropForeign(['box_size_id']);
            $table->dropForeign(['tax_id']);
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            $table->foreign('type_id')->references('id')->on('medicine_types')->nullOnDelete();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->nullOnDelete();
            $table->foreign('box_size_id')->references('id')->on('box_sizes')->nullOnDelete();
            $table->foreign('tax_id')->references('id')->on('taxes')->nullOnDelete();
        });
        Schema::table('stocks', function (Blueprint $table) {
            $table->double('productStock', 10, 2)->default(0)->change();
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('payment_type_id')->nullable()->after('paymentType');
            $table->string('discount_type')->default('flat')->after('discountAmount'); // flat, percent
            $table->double('discount_percent')->default(0)->after('discountAmount');
            $table->double('shipping_charge')->default(0)->after('discountAmount');
            $table->string('image')->nullable()->after('saleDate');
            $table->string('rounding_option')->nullable()->after('totalAmount');
            $table->double('rounding_amount', 10, 2)->default(0)->after('totalAmount');
            $table->double('actual_total_amount', 10, 2)->default(0)->after('totalAmount');
            $table->double('change_amount')->default(0)->after('paidAmount');
            $table->string('status')->default('final')->after('sale_data'); // draft, final
        });
        Schema::table('sale_details', function (Blueprint $table) {
            $table->double('quantities', 10, 2)->default(0)->change();
        });
        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->double('return_qty', 10, 2)->default(0)->change();
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('payment_type_id')->nullable()->after('paymentType');
            $table->string("paymentType")->nullable()->change();
            $table->string('discount_type')->default('flat')->after('discountAmount'); // flat, percent
            $table->double('discount_percent')->default(0)->after('discountAmount');
            $table->double('shipping_charge')->default(0)->after('discountAmount');
            $table->double('change_amount')->default(0)->after('paidAmount');
            $table->string('status')->nullable()->after('purchase_data'); // draft, final
        });
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->double('dealer_price')->default(0)->after('wholesale_price');

            $table->double('quantities', 10, 2)->default(0)->change();
        });
        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->double('return_qty', 10, 2)->default(0)->change();
        });
        Schema::table('due_collects', function (Blueprint $table) {
            $table->foreignId('payment_type_id')->nullable()->after('paymentType');
            $table->string("paymentType")->nullable()->change();
        });
        Schema::table('incomes', function (Blueprint $table) {
            $table->foreignId('payment_type_id')->nullable()->after('paymentType');
            $table->string("paymentType")->nullable()->change();
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('payment_type_id')->nullable()->after('paymentType');
            $table->string("paymentType")->nullable()->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['tax_name', '_no']);
        });
        Schema::table('parties', function (Blueprint $table) {
            $table->string('phone')->unique()->nullable()->change();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('dealer_price');
            $table->integer('alert_qty')->default(0)->change();
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['type_id']);
            $table->dropForeign(['manufacturer_id']);
            $table->dropForeign(['box_size_id']);
            $table->dropForeign(['tax_id']);
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
            $table->foreign('type_id')->references('id')->on('medicine_types')->cascadeOnDelete();
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->cascadeOnDelete();
            $table->foreign('box_size_id')->references('id')->on('box_sizes')->cascadeOnDelete();
            $table->foreign('tax_id')->references('id')->on('taxes')->cascadeOnDelete();
        });
        Schema::table('stocks', function (Blueprint $table) {
            $table->integer('productStock')->default(0)->change();;
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['payment_type_id','discount_type', 'discount_percent', 'shipping_charge', 'image', 'rounding_option', 'rounding_amount', 'actual_total_amount','change_amount','status']);
        });
        Schema::table('sale_details', function (Blueprint $table) {
            $table->integer('quantities')->default(0)->change();;
        });
        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->integer('return_qty')->change();;
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->string("paymentType")->default("Cash")->change();
            $table->dropColumn(['payment_type_id','discount_type', 'discount_percent', 'shipping_charge','change_amount','status']);
        });
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn('dealer_price');
            $table->integer('quantities')->default(0)->change();;
        });
        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->integer('return_qty')->change();;
        });

        Schema::table('due_collects', function (Blueprint $table) {
            $table->dropColumn('payment_type_id');
            $table->string("paymentType")->default("Cash")->change();
        });
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('payment_type_id');
            $table->string("paymentType")->default("Cash")->change();
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('payment_type_id');
            $table->string("paymentType")->default("Cash")->change();
        });
    }
};
