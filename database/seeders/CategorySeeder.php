<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ivermectin Products',
                'slug' => 'ivermectin-products',
                'description' => 'Genuine Ivermectin capsules verified by Japanese research institutions.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Antiparasitic Medications',
                'slug' => 'antiparasitic-medications',
                'description' => 'Medications for treating parasitic infections.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Prescription Medications',
                'slug' => 'prescription-medications',
                'description' => 'Prescription-only medications requiring proper documentation.',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

