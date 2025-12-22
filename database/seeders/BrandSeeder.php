<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Nantosha Pharmaceuticals',
                'slug' => 'nantosha-pharmaceuticals',
                'description' => 'Leading manufacturer of Ivermectin products verified by Japanese research institutions.',
                'is_active' => true,
            ],
            [
                'name' => 'MedPharm Japan',
                'slug' => 'medpharm-japan',
                'description' => 'Japanese pharmaceutical company specializing in antiparasitic medications.',
                'is_active' => true,
            ],
            [
                'name' => 'Global Health Solutions',
                'slug' => 'global-health-solutions',
                'description' => 'International pharmaceutical manufacturer with Japanese quality standards.',
                'is_active' => true,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['slug' => $brand['slug']],
                $brand
            );
        }
    }
}

