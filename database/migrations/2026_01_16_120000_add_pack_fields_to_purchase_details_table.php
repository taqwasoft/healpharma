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
            $table->integer('pack_size')->nullable()->after('bonus_qty')->comment('Units per pack');
            $table->integer('pack_qty')->nullable()->after('pack_size')->comment('Number of packs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropColumn(['pack_size', 'pack_qty']);
        });
    }
};
