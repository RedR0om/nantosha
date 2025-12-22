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
        // Note: Categories and Brands should be seeded separately via CategorySeeder and BrandSeeder
        // This seeder assumes they already exist, but we'll verify they exist before creating products
        
        // Verify required categories and brands exist
        $ivermectinCategory = Category::where('slug', 'ivermectin-products')->first();
        $nantoshaBrand = Brand::where('slug', 'nantosha-pharmaceuticals')->first();
        $medpharmBrand = Brand::where('slug', 'medpharm-japan')->first();
        
        if (!$ivermectinCategory || !$nantoshaBrand || !$medpharmBrand) {
            $this->command->warn('Required categories or brands not found. Please run CategorySeeder and BrandSeeder first.');
            return;
        }

        // Create Products (Ivermectin capsules with JPY pricing)
        $products = [
            [
                'name' => 'Ivermectin 12mg Capsules (30 count)',
                'slug' => 'ivermectin-12mg-capsules-30',
                'brand_id' => $nantoshaBrand->id,
                'category_id' => $ivermectinCategory->id,
                'price' => 15000, // JPY
                'sale_price' => 13500, // JPY
                'price_per_capsule' => 450, // JPY
                'price_per_mg' => 37.5, // JPY per mg
                'sku' => 'IVM-12MG-30',
                'stock_quantity' => 100,
                'in_stock' => true,
                'description' => 'Genuine Ivermectin 12mg capsules, 30 count. Verified by Japanese research institutions. Each capsule contains 12mg of Ivermectin, manufactured according to strict quality standards.',
                'short_description' => 'Genuine Ivermectin 12mg capsules - 30 count. Verified by Japanese research institutions.',
                'ingredients' => "Ivermectin: 12mg\nInactive Ingredients: Microcrystalline Cellulose, Magnesium Stearate, Gelatin Capsule",
                'directions' => 'Take as directed by your healthcare professional. For Strongyloidiasis: Typically 200 mcg/kg body weight as a single dose. For Scabies: Typically 200 mcg/kg body weight, may require a second dose after 1-2 weeks. Consult your doctor for proper dosage.',
                'warnings' => 'This product is not intended for pregnant or nursing women or by persons under 18. Consult your healthcare professional prior to use if you have or suspect a medical condition or are taking prescription drugs. Discontinue use and seek advice of a doctor if any adverse reactions occur. The Japanese MHLW only approves Ivermectin for two diseases: Strongyloidiasis and Scabies. Resale or transfer to third parties is strictly prohibited.',
                'country_of_origin' => 'Japan',
                'manufacturer' => 'Nantosha Pharmaceuticals Co., Ltd.',
                'supplement_facts' => 'Each capsule contains: Ivermectin 12mg. Manufactured in Japan under strict quality control standards.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => true,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Ivermectin 12mg Capsules (60 count)',
                'slug' => 'ivermectin-12mg-capsules-60',
                'brand_id' => $nantoshaBrand->id,
                'category_id' => $ivermectinCategory->id,
                'price' => 28000, // JPY
                'sale_price' => 25000, // JPY
                'price_per_capsule' => 416.67, // JPY
                'price_per_mg' => 34.72, // JPY per mg
                'sku' => 'IVM-12MG-60',
                'stock_quantity' => 75,
                'in_stock' => true,
                'description' => 'Genuine Ivermectin 12mg capsules, 60 count. Best value option for extended treatment. Verified by Japanese research institutions. Each capsule contains 12mg of Ivermectin.',
                'short_description' => 'Genuine Ivermectin 12mg capsules - 60 count. Best value for extended treatment.',
                'ingredients' => "Ivermectin: 12mg\nInactive Ingredients: Microcrystalline Cellulose, Magnesium Stearate, Gelatin Capsule",
                'directions' => 'Take as directed by your healthcare professional. For Strongyloidiasis: Typically 200 mcg/kg body weight as a single dose. For Scabies: Typically 200 mcg/kg body weight, may require a second dose after 1-2 weeks. Consult your doctor for proper dosage.',
                'warnings' => 'This product is not intended for pregnant or nursing women or by persons under 18. Consult your healthcare professional prior to use if you have or suspect a medical condition or are taking prescription drugs. Discontinue use and seek advice of a doctor if any adverse reactions occur. The Japanese MHLW only approves Ivermectin for two diseases: Strongyloidiasis and Scabies. Resale or transfer to third parties is strictly prohibited.',
                'country_of_origin' => 'Japan',
                'manufacturer' => 'Nantosha Pharmaceuticals Co., Ltd.',
                'supplement_facts' => 'Each capsule contains: Ivermectin 12mg. Manufactured in Japan under strict quality control standards.',
                'is_best_seller' => true,
                'is_new_arrival' => false,
                'is_customer_favorite' => false,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Ivermectin 6mg Capsules (30 count)',
                'slug' => 'ivermectin-6mg-capsules-30',
                'brand_id' => $medpharmBrand->id,
                'category_id' => $ivermectinCategory->id,
                'price' => 8500, // JPY
                'sale_price' => null,
                'price_per_capsule' => 283.33, // JPY
                'price_per_mg' => 47.22, // JPY per mg
                'sku' => 'IVM-6MG-30',
                'stock_quantity' => 50,
                'in_stock' => true,
                'description' => 'Ivermectin 6mg capsules, 30 count. Lower dosage option for sensitive individuals or pediatric use. Verified by Japanese research institutions.',
                'short_description' => 'Ivermectin 6mg capsules - 30 count. Lower dosage option.',
                'ingredients' => "Ivermectin: 6mg\nInactive Ingredients: Microcrystalline Cellulose, Magnesium Stearate, Gelatin Capsule",
                'directions' => 'Take as directed by your healthcare professional. Lower dosage may be appropriate for certain individuals. Consult your doctor for proper dosage based on body weight and condition.',
                'warnings' => 'This product is not intended for pregnant or nursing women or by persons under 18. Consult your healthcare professional prior to use if you have or suspect a medical condition or are taking prescription drugs. Discontinue use and seek advice of a doctor if any adverse reactions occur. The Japanese MHLW only approves Ivermectin for two diseases: Strongyloidiasis and Scabies. Resale or transfer to third parties is strictly prohibited.',
                'country_of_origin' => 'Japan',
                'manufacturer' => 'MedPharm Japan Co., Ltd.',
                'supplement_facts' => 'Each capsule contains: Ivermectin 6mg. Manufactured in Japan under strict quality control standards.',
                'is_best_seller' => false,
                'is_new_arrival' => true,
                'is_customer_favorite' => false,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Ivermectin 12mg Capsules (90 count)',
                'slug' => 'ivermectin-12mg-capsules-90',
                'brand_id' => $nantoshaBrand->id,
                'category_id' => $ivermectinCategory->id,
                'price' => 40000, // JPY
                'sale_price' => 36000, // JPY
                'price_per_capsule' => 400, // JPY
                'price_per_mg' => 33.33, // JPY per mg
                'sku' => 'IVM-12MG-90',
                'stock_quantity' => 40,
                'in_stock' => true,
                'description' => 'Ivermectin 12mg capsules, 90 count. Maximum value package for long-term treatment. Verified by Japanese research institutions.',
                'short_description' => 'Ivermectin 12mg capsules - 90 count. Maximum value package.',
                'ingredients' => "Ivermectin: 12mg\nInactive Ingredients: Microcrystalline Cellulose, Magnesium Stearate, Gelatin Capsule",
                'directions' => 'Take as directed by your healthcare professional. For Strongyloidiasis: Typically 200 mcg/kg body weight as a single dose. For Scabies: Typically 200 mcg/kg body weight, may require a second dose after 1-2 weeks. Consult your doctor for proper dosage.',
                'warnings' => 'This product is not intended for pregnant or nursing women or by persons under 18. Consult your healthcare professional prior to use if you have or suspect a medical condition or are taking prescription drugs. Discontinue use and seek advice of a doctor if any adverse reactions occur. The Japanese MHLW only approves Ivermectin for two diseases: Strongyloidiasis and Scabies. Resale or transfer to third parties is strictly prohibited.',
                'country_of_origin' => 'Japan',
                'manufacturer' => 'Nantosha Pharmaceuticals Co., Ltd.',
                'supplement_facts' => 'Each capsule contains: Ivermectin 12mg. Manufactured in Japan under strict quality control standards.',
                'is_best_seller' => false,
                'is_new_arrival' => false,
                'is_customer_favorite' => true,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($products as $product) {
            $created = Product::firstOrCreate(
                ['slug' => $product['slug']],
                $product
            );
            if ($created->wasRecentlyCreated) {
                $this->command->info("Created product: {$product['name']}");
            } else {
                $this->command->line("Product already exists: {$product['name']}");
            }
        }
        $this->command->info("Product seeding completed. Total products: " . Product::count());
    }
}
