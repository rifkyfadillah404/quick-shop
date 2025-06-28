<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Available sample images
        $availableImages = [
            'products/sample-product-1.jpg',
            'products/sample-product-2.jpg',
            'products/sample-product-3.jpg',
            'products/sample-product-4.jpg',
            'products/sample-product-5.jpg',
            'products/sample-product-6.jpg',
            'products/sample-product-7.jpg',
            'products/sample-product-8.jpg',
            'products/sample-product-9.jpg',
            'products/sample-product-10.jpg',
        ];

        $products = Product::all();

        foreach ($products as $product) {
            // Randomly assign 1-3 images to each product
            $numImages = rand(1, 3);
            $productImages = [];

            for ($i = 0; $i < $numImages; $i++) {
                $randomImage = $availableImages[array_rand($availableImages)];
                if (!in_array($randomImage, $productImages)) {
                    $productImages[] = $randomImage;
                }
            }

            // Update product with image paths
            $product->update([
                'images' => $productImages
            ]);

            $this->command->info("Updated product: {$product->name} with " . count($productImages) . " images");
        }
    }
}
