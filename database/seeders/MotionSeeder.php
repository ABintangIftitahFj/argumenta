<?php

namespace Database\Seeders;

use App\Models\Motion;
use Illuminate\Database\Seeder;

class MotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $motions = [
            [
                'title' => 'Dewan ini akan mewajibkan transparansi penuh atas penggunaan model AI generatif di sektor publik.',
                'info_slide' => 'Sektor publik mencakup lembaga penegak hukum, layanan kesehatan masyarakat, penerimaan pegawai negeri, dan penyaluran bantuan sosial.',
                'theme' => 'Teknologi & AI',
                'difficulty' => 'novice',
                'language' => 'id',
                'is_featured' => true,
            ],
            [
                'title' => 'Dewan ini menyesalkan narasi bahwa pendidikan tinggi adalah satu-satunya jalur mobilitas sosial ekonomi.',
                'info_slide' => null,
                'theme' => 'Sosial & Pendidikan',
                'difficulty' => 'novice',
                'language' => 'id',
                'is_featured' => true,
            ],
            [
                'title' => 'Sebagai negara berkembang dengan cadangan nikel melimpah, dewan ini akan memprioritaskan industrialisasi hijau hilir daripada ekspor bahan mentah.',
                'info_slide' => 'Kebijakan hilirisasi mewajibkan seluruh pengolahan bijih nikel dilakukan di smelter domestik sebelum dapat diekspor.',
                'theme' => 'Ekonomi & Energi',
                'difficulty' => 'intermediate',
                'language' => 'id',
                'is_featured' => false,
            ],
            [
                'title' => 'This House would ban the commercial development of autonomous lethal weapon systems.',
                'info_slide' => 'Autonomous weapon systems are military systems capable of selecting and engaging targets without human intervention.',
                'theme' => 'Geopolitics & Defense',
                'difficulty' => 'intermediate',
                'language' => 'en',
                'is_featured' => true,
            ],
            [
                'title' => 'This House regrets the dominance of venture capital in determining the direction of scientific research.',
                'info_slide' => null,
                'theme' => 'Science & Society',
                'difficulty' => 'advanced',
                'language' => 'en',
                'is_featured' => false,
            ],
        ];

        foreach ($motions as $data) {
            Motion::firstOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
