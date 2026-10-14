<?php

namespace Database\Seeders;

use App\Models\LearningModule;
use Illuminate\Database\Seeder;

class LearningModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'title' => 'Dasar Klaim & Premis Argumen',
                'slug' => 'dasar-klaim-premis',
                'stage' => 1,
                'order' => 1,
                'description' => 'Pelajari struktur AREL (Assertion, Reasoning, Evidence, Linkback) dan hindari logical fallacies dasar dalam menyusun argumen pembuka.',
                'format' => 'all',
                'difficulty' => 'novice',
                'xp_reward' => 100,
                'is_published' => true,
            ],
            [
                'title' => 'Mekanisme & Rantai Kausalitas',
                'slug' => 'mekanisme-rantai-kausalitas',
                'stage' => 2,
                'order' => 2,
                'description' => 'Jelaskan secara mendalam bagaimana dan mengapa proposal kebijakan atau mosi benar-benar membawa dampak nyata, bukan sekadar korelasi semu.',
                'format' => 'all',
                'difficulty' => 'novice',
                'xp_reward' => 120,
                'is_published' => true,
            ],
            [
                'title' => 'Rebuttal & Direct Clash',
                'slug' => 'rebuttal-direct-clash',
                'stage' => 3,
                'order' => 3,
                'description' => 'Menyerang premis utama lawan dan membongkar asumsi fundamental mereka, bukan hanya menanggapi contoh sekunder.',
                'format' => 'all',
                'difficulty' => 'intermediate',
                'xp_reward' => 150,
                'is_published' => true,
            ],
            [
                'title' => 'Comparative Weighing & Metrics',
                'slug' => 'comparative-weighing-metrics',
                'stage' => 4,
                'order' => 4,
                'description' => 'Bandingkan dampak dua dunia secara proporsional menggunakan metrik urgensi, skala, reversibilitas, dan kewajiban moral.',
                'format' => 'all',
                'difficulty' => 'intermediate',
                'xp_reward' => 180,
                'is_published' => true,
            ],
            [
                'title' => 'Role Fulfillment BP & AP',
                'slug' => 'role-fulfillment-bp-ap',
                'stage' => 5,
                'order' => 5,
                'description' => 'Kuasai tugas spesifik tiap pembicara dari Prime Minister, Whip, hingga Reply Speech dalam format British Parliamentary & Asian Parliamentary.',
                'format' => 'all',
                'difficulty' => 'advanced',
                'xp_reward' => 200,
                'is_published' => true,
            ],
        ];

        foreach ($modules as $data) {
            LearningModule::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
