<?php

namespace Database\Seeders;

use App\Helpers\VietnameseHelper;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SearchIndexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::with(['brand', 'category'])->get();
        foreach ($products as $product) {
            $brandName = $product->brand?->name ?? '';
            $categoryName = $product->category?->name ?? '';

            $product->search_index = VietnameseHelper::buildSearchIndex(
                $product->name,
                $brandName,
                $categoryName,
                $product->material,
                $product->color,
                $product->sku,
                $product->description
            );
            $product->saveQuietly();
        }

        $this->command->info("Indexed {$products->count()} products successfully.");
    }
}
