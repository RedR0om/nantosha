<?php

namespace Database\Seeders;

use App\Models\HomePageSection;
use Illuminate\Database\Seeder;

class HomePageSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create static sections for ordering
        // These sections control the order of static content (carousel, product sections, etc.)
        
        // Carousel section
        HomePageSection::firstOrCreate(
            ['type' => 'carousel'],
            [
                'title' => null,
                'subtitle' => null,
                'content' => null,
                'background_color' => 'white',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        // Hero section (the main hero with badges)
        HomePageSection::firstOrCreate(
            ['type' => 'hero'],
            [
                'title' => 'Nantosha Import & Export Division',
                'subtitle' => 'Genuine Ivermectin Capsules (15mg) - Verified by Japanese Research Institutions',
                'content' => [
                    'badges' => [
                        ['text' => '99%+ Purity', 'icon' => 'CheckCircle'],
                        ['text' => 'Japanese Lab Verified', 'icon' => 'Shield'],
                        ['text' => 'From Dr. Allan Landrito', 'icon' => 'Award'],
                    ],
                    'buttons' => [
                        ['text' => 'View Product Details', 'href' => '/products', 'style' => 'primary'],
                        ['text' => 'How to Order', 'href' => '/how-to-order', 'style' => 'secondary'],
                    ],
                ],
                'background_color' => 'white',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        // Best Sellers section
        HomePageSection::firstOrCreate(
            ['type' => 'best_sellers'],
            [
                'title' => 'Our Best Sellers',
                'subtitle' => null,
                'content' => null,
                'background_color' => 'white',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Featured Product section (for single product mode)
        HomePageSection::firstOrCreate(
            ['type' => 'featured_product'],
            [
                'title' => null,
                'subtitle' => null,
                'content' => null,
                'background_color' => 'white',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Customer Favorites section
        HomePageSection::firstOrCreate(
            ['type' => 'customer_favorites'],
            [
                'title' => "Customer's Favourite",
                'subtitle' => null,
                'content' => null,
                'background_color' => 'gray-50',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // New Arrivals section
        HomePageSection::firstOrCreate(
            ['type' => 'new_arrivals'],
            [
                'title' => 'New arrivals',
                'subtitle' => null,
                'content' => null,
                'background_color' => 'white',
                'sort_order' => 4,
                'is_active' => true,
            ]
        );
    }
}
