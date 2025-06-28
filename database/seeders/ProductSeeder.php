<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::where('slug', 'electronics')->first();
        $clothing = Category::where('slug', 'clothing')->first();
        $books = Category::where('slug', 'books')->first();

        $products = [
            [
                'category_id' => $electronics->id,
                'name' => 'Smartphone Samsung Galaxy',
                'description' => 'Latest Samsung Galaxy smartphone with advanced features',
                'short_description' => 'High-end smartphone',
                'price' => 899.99,
                'sale_price' => 799.99,
                'sku' => 'PHONE-001',
                'stock_quantity' => 50,
            ],
            [
                'category_id' => $electronics->id,
                'name' => 'Laptop Dell XPS',
                'description' => 'Powerful laptop for work and gaming',
                'short_description' => 'High-performance laptop',
                'price' => 1299.99,
                'sku' => 'LAPTOP-001',
                'stock_quantity' => 25,
            ],
            [
                'category_id' => $clothing->id,
                'name' => 'Cotton T-Shirt',
                'description' => 'Comfortable cotton t-shirt in various colors',
                'short_description' => 'Basic cotton tee',
                'price' => 19.99,
                'sku' => 'SHIRT-001',
                'stock_quantity' => 100,
            ],
            [
                'category_id' => $books->id,
                'name' => 'Laravel Programming Guide',
                'description' => 'Complete guide to Laravel framework development',
                'short_description' => 'Laravel development book',
                'price' => 49.99,
                'sku' => 'BOOK-001',
                'stock_quantity' => 30,
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'short_description' => $product['short_description'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'] ?? null,
                'sku' => $product['sku'],
                'stock_quantity' => $product['stock_quantity'],
                'manage_stock' => true,
                'in_stock' => true,
                'is_active' => true,
            ]);
        }
    }
}
