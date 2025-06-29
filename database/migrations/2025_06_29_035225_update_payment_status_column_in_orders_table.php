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
        Schema::table('orders', function (Blueprint $table) {
            // Change payment_status from enum to string to support more statuses
            $table->string('payment_status', 30)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to enum
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->change();
        });
    }
};
