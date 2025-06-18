<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'title' => 'Backend Development',
                'description' => 'Laravel, PHP, Node.js ile güvenli ve ölçeklenebilir backend sistemler geliştiriyorum.',
                'icon' => 'Server',
                'color' => 'from-blue-500 to-blue-600',
                'technologies' => ['Laravel', 'PHP', 'Node.js', 'Express.js', 'MySQL', 'PostgreSQL', 'Redis'],
                'proficiency_level' => 5,
                'order' => 1,
                'is_active' => true
            ],
            [
                'title' => 'API Development',
                'description' => 'RESTful API tasarımı, entegrasyonu ve dokümantasyonu konularında uzmanlığım var.',
                'icon' => 'Code',
                'color' => 'from-green-500 to-green-600',
                'technologies' => ['REST API', 'GraphQL', 'Sanctum', 'Postman', 'Swagger', 'API Security'],
                'proficiency_level' => 5,
                'order' => 2,
                'is_active' => true
            ],
            [
                'title' => 'Database Management',
                'description' => 'Veri modelleme, optimizasyon ve yönetimi konularında deneyimliyim.',
                'icon' => 'Database',
                'color' => 'from-purple-500 to-purple-600',
                'technologies' => ['MySQL', 'PostgreSQL', 'Eloquent ORM', 'Redis', 'MongoDB'],
                'proficiency_level' => 4,
                'order' => 3,
                'is_active' => true
            ],
            [
                'title' => 'Frontend Development',
                'description' => 'Modern JavaScript framework\'leri ile kullanıcı dostu arayüzler geliştiriyorum.',
                'icon' => 'Globe',
                'color' => 'from-orange-500 to-orange-600',
                'technologies' => ['React', 'Vue.js', 'Next.js', 'TypeScript', 'Tailwind CSS'],
                'proficiency_level' => 4,
                'order' => 4,
                'is_active' => true
            ],
            [
                'title' => 'DevOps & Deployment',
                'description' => 'CI/CD pipeline\'ları ve cloud teknolojileri ile deployment süreçlerini yönetiyorum.',
                'icon' => 'Cloud',
                'color' => 'from-indigo-500 to-indigo-600',
                'technologies' => ['Docker', 'AWS', 'CI/CD', 'Linux', 'Nginx', 'Apache'],
                'proficiency_level' => 4,
                'order' => 5,
                'is_active' => true
            ],
            [
                'title' => 'Search & Analytics',
                'description' => 'Elasticsearch ve analitik sistemlerin tasarımı ve optimizasyonu.',
                'icon' => 'Search',
                'color' => 'from-red-500 to-red-600',
                'technologies' => ['Elasticsearch', 'Laravel Scout', 'Algolia', 'Analytics'],
                'proficiency_level' => 4,
                'order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($skills as $skillData) {
            \App\Models\Skill::create($skillData);
        }
    }
}
