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
        Schema::table('box_sizes', function (Blueprint $table) {
            $table->integer('value')->nullable()->after('name')->comment('Numeric value of box size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('box_sizes', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
};
