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
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->integer('invoice_qty')->default(0)->after('quantities');
            $table->integer('bonus_qty')->default(0)->after('invoice_qty');
            $table->double('gross_total_price')->default(0)->after('bonus_qty');
            $table->double('vat_amount')->default(0)->after('gross_total_price');
            $table->double('discount_amount')->default(0)->after('vat_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn(['invoice_qty', 'bonus_qty', 'gross_total_price', 'vat_amount', 'discount_amount']);
        });
    }
};
