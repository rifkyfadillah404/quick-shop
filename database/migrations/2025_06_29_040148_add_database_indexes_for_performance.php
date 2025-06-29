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
        // Add indexes for products table
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'in_stock']); // For product filtering
            $table->index(['category_id', 'is_active']); // For category filtering
            $table->index(['price']); // For price sorting/filtering
            $table->index(['created_at']); // For newest sorting
            $table->index(['name']); // For search and sorting
            $table->index(['is_featured', 'is_active', 'in_stock']); // For featured products
        });

        // Add indexes for categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active']); // For active categories
            $table->index(['slug']); // For category lookup
        });

        // Add indexes for orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']); // For user orders
            $table->index(['status']); // For order status filtering
            $table->index(['payment_status']); // For payment status filtering
            $table->index(['created_at']); // For order sorting
        });

        // Add indexes for cart_items table
        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['user_id']); // For user cart lookup
            $table->index(['product_id']); // For product lookup
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes for products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'in_stock']);
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['price']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['name']);
            $table->dropIndex(['is_featured', 'is_active', 'in_stock']);
        });

        // Remove indexes for categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['slug']);
        });

        // Remove indexes for orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
        });

        // Remove indexes for cart_items table
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['product_id']);
        });
    }
};
