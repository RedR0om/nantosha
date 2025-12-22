<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Other uses?',
                'question_ja' => '他の用途はありますか？',
                'answer' => 'MHLW only approves two diseases, but global research on antiviral/anti-tumor effects exists.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Can I resell?',
                'question_ja' => '転売できますか？',
                'answer' => 'No. Only doctors with Yakkan Shoumei permits can prescribe to others.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Dosage advice?',
                'question_ja' => '用量のアドバイスは？',
                'answer' => 'The manufacturing doctor can provide minimal advice based on your Health Check Sheet.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Want a doctor\'s exam?',
                'question_ja' => '医師の診察を受けたいのですが？',
                'answer' => 'We can provide a list of medical institutions that prescribe Ivermectin (Non-insured).',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'question' => 'Quality?',
                'question_ja' => '品質は？',
                'answer' => '99%+ purity verified by Japanese labs. Reports are kept private to protect the institutions involved.',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            $created = Faq::firstOrCreate(
                ['question' => $faq['question']],
                $faq
            );
            if ($created->wasRecentlyCreated) {
                $this->command->info("Created FAQ: {$faq['question']}");
            } else {
                $this->command->line("FAQ already exists: {$faq['question']}");
            }
        }
        $this->command->info("FAQ seeding completed. Total FAQs: " . Faq::count());
    }
}
