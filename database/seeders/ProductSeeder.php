<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            ['name' => 'Vitamins & Supplements', 'slug' => 'vitamins-supplements'],
            ['name' => 'Protein & Fitness', 'slug' => 'protein-fitness'],
            ['name' => 'Herbs & Botanicals', 'slug' => 'herbs-botanicals'],
            ['name' => 'Digestive Health', 'slug' => 'digestive-health'],
            ['name' => 'Immune Support', 'slug' => 'immune-support'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Brands
        $brands = [
            ['name' => 'Calocurb', 'slug' => 'calocurb'],
            ['name' => 'Designs for Health', 'slug' => 'designs-for-health'],
            ['name' => 'Thorne', 'slug' => 'thorne'],
            ['name' => 'Klean Athlete', 'slug' => 'klean-athlete'],
            ['name' => 'Inwell Biosciences', 'slug' => 'inwell-biosciences'],
            ['name' => 'Integrative Therapeutics', 'slug' => 'integrative-therapeutics'],
            ['name' => 'Infiniwell', 'slug' => 'infiniwell'],
            ['name' => 'Viviscal', 'slug' => 'viviscal'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        // Create Products
        $products = [
            [
                'name' => 'Calocurb Amarasate Appetite Control',
                'slug' => 'calocurb-amarasate-appetite-control',
                'brand_id' => Brand::where('slug', 'calocurb')->first()->id,
                'category_id' => Category::where('slug', 'vitamins-supplements')->first()->id,
                'price' => 89.99,
                'sale_price' => null,
                'sku' => 'CAL-001',
                'stock_quantity' => 50,
                'in_stock' => true,
                'description' => 'Natural appetite control supplement with Amarasate extract.',
                'short_description' => 'Support healthy appetite control naturally.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'Twice Daily Multi',
                'slug' => 'twice-daily-multi',
                'brand_id' => Brand::where('slug', 'designs-for-health')->first()->id,
                'category_id' => Category::where('slug', 'vitamins-supplements')->first()->id,
                'price' => 32.25,
                'sale_price' => null,
                'sku' => 'DFH-001',
                'stock_quantity' => 100,
                'in_stock' => true,
                'description' => 'Comprehensive multivitamin formula for daily nutritional support.',
                'short_description' => 'Complete daily multivitamin support.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'Zinc Picolinate 30 mg',
                'slug' => 'zinc-picolinate-30mg',
                'brand_id' => Brand::where('slug', 'thorne')->first()->id,
                'category_id' => Category::where('slug', 'immune-support')->first()->id,
                'price' => 19.00,
                'sale_price' => null,
                'sku' => 'THR-001',
                'stock_quantity' => 75,
                'in_stock' => true,
                'description' => 'Highly absorbable zinc supplement for immune support.',
                'short_description' => 'Premium zinc for immune health.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'Klean Ashwagandha',
                'slug' => 'klean-ashwagandha',
                'brand_id' => Brand::where('slug', 'klean-athlete')->first()->id,
                'category_id' => Category::where('slug', 'herbs-botanicals')->first()->id,
                'price' => 24.35,
                'sale_price' => null,
                'sku' => 'KLE-001',
                'stock_quantity' => 60,
                'in_stock' => true,
                'description' => 'Adaptogenic herb for stress support and energy balance.',
                'short_description' => 'Natural stress support supplement.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'D3 & K2',
                'slug' => 'd3-k2',
                'brand_id' => Brand::where('slug', 'inwell-biosciences')->first()->id,
                'category_id' => Category::where('slug', 'vitamins-supplements')->first()->id,
                'price' => 34.99,
                'sale_price' => null,
                'sku' => 'INW-001',
                'stock_quantity' => 80,
                'in_stock' => true,
                'description' => 'Essential vitamins D3 and K2 for bone and cardiovascular health.',
                'short_description' => 'Bone and heart health support.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'Fiber Formula',
                'slug' => 'fiber-formula',
                'brand_id' => Brand::where('slug', 'integrative-therapeutics')->first()->id,
                'category_id' => Category::where('slug', 'digestive-health')->first()->id,
                'price' => 14.50,
                'sale_price' => null,
                'sku' => 'INT-001',
                'stock_quantity' => 90,
                'in_stock' => true,
                'description' => 'Comprehensive fiber supplement for digestive health.',
                'short_description' => 'Support healthy digestion.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'Probiotic Supreme',
                'slug' => 'probiotic-supreme',
                'brand_id' => Brand::where('slug', 'designs-for-health')->first()->id,
                'category_id' => Category::where('slug', 'digestive-health')->first()->id,
                'price' => 91.00,
                'sale_price' => null,
                'sku' => 'DFH-002',
                'stock_quantity' => 40,
                'in_stock' => true,
                'description' => 'Advanced probiotic formula with multiple beneficial strains.',
                'short_description' => 'Premium probiotic support.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'NMN - Healthy Aging Support',
                'slug' => 'nmn-healthy-aging-support',
                'brand_id' => Brand::where('slug', 'infiniwell')->first()->id,
                'category_id' => Category::where('slug', 'vitamins-supplements')->first()->id,
                'price' => 69.99,
                'sale_price' => 62.99,
                'sku' => 'INF-001',
                'stock_quantity' => 55,
                'in_stock' => true,
                'description' => 'Nicotinamide Mononucleotide for cellular health and healthy aging.',
                'short_description' => 'Support cellular aging and NAD levels.',
                'is_best_seller' => false,
                'is_new_arrival' => false,
                'is_customer_favorite' => true,
            ],
            [
                'name' => 'Viviscal Pro Hair Health',
                'slug' => 'viviscal-pro-hair-health',
                'brand_id' => Brand::where('slug', 'viviscal')->first()->id,
                'category_id' => Category::where('slug', 'vitamins-supplements')->first()->id,
                'price' => 66.99,
                'sale_price' => null,
                'sku' => 'VIV-001',
                'stock_quantity' => 30,
                'in_stock' => true,
                'description' => 'Professional-grade hair growth supplement.',
                'short_description' => 'Support healthy hair growth.',
                'is_best_seller' => false,
                'is_new_arrival' => true,
                'is_customer_favorite' => false,
            ],
            [
                'name' => 'PurePaleo Protein Vanilla',
                'slug' => 'purepaleo-protein-vanilla',
                'brand_id' => Brand::where('slug', 'designs-for-health')->first()->id,
                'category_id' => Category::where('slug', 'protein-fitness')->first()->id,
                'price' => 72.63,
                'sale_price' => null,
                'sku' => 'DFH-003',
                'stock_quantity' => 45,
                'in_stock' => true,
                'description' => 'Clean, paleo-friendly protein powder in vanilla flavor.',
                'short_description' => 'Premium paleo protein powder.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
