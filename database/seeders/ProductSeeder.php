<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::firstOrCreate([
            'name' => 'Default Category',
            'slug' => 'default-category'
        ]);

        $brand = Brand::firstOrCreate([
            'name' => 'Default Brand',
            'slug' => 'default-brand'
        ]);

        $product = Product::firstOrCreate([
            'slug' => 'sample-product'
        ], [
            'name' => 'Sample Product',
            'short_description' => 'A comfortable stylish top for your daily wear.',
            'description' => 'A high-quality sample product with modern styling and premium materials. Great for demo and testing purposes.',
            'regular_price' => 199.99,
            'sale_price' => 149.99,
            'SKU' => 'SAMPLE001',
            'stock_status' => 'instock',
            'featured' => false,
            'quantity' => 10,
            'image' => '1731405948.jpg',
            'images' => '1731405948-1.jpg,1731405948-2.jpeg',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
        ]);
    }
}
