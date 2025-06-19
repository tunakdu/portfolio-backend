<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PersonalInfo;

class PersonalInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personalData = [
            // About bilgileri
            ['key' => 'full_name', 'value' => 'Tunahan Akduhan', 'type' => 'text', 'category' => 'about'],
            ['key' => 'title', 'value' => 'Full Stack Developer', 'type' => 'text', 'category' => 'about'],
            ['key' => 'bio', 'value' => 'Passionate full stack developer with expertise in modern web technologies', 'type' => 'text', 'category' => 'about'],
            ['key' => 'location', 'value' => 'Istanbul, Turkey', 'type' => 'text', 'category' => 'about'],
            ['key' => 'birth_date', 'value' => '1990-01-01', 'type' => 'date', 'category' => 'about'],
            
            // İletişim bilgileri
            ['key' => 'email', 'value' => 'tunahan@akduhan.com', 'type' => 'email', 'category' => 'contact'],
            ['key' => 'phone', 'value' => '+90 555 123 4567', 'type' => 'phone', 'category' => 'contact'],
            ['key' => 'address', 'value' => 'Istanbul, Turkey', 'type' => 'text', 'category' => 'contact'],
            
            // Sosyal medya
            ['key' => 'github_url', 'value' => 'https://github.com/tunahanakduhan', 'type' => 'url', 'category' => 'social'],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com/in/tunahanakduhan', 'type' => 'url', 'category' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/tunahanakduhan', 'type' => 'url', 'category' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/tunahanakduhan', 'type' => 'url', 'category' => 'social'],
            
            // Yetenekler
            ['key' => 'skills', 'value' => json_encode([
                'Frontend' => ['Vue.js', 'React', 'JavaScript', 'TypeScript', 'HTML5', 'CSS3', 'Tailwind CSS'],
                'Backend' => ['PHP', 'Laravel', 'Node.js', 'Python', 'MySQL', 'PostgreSQL', 'Redis'],
                'DevOps' => ['Docker', 'Git', 'Linux', 'AWS', 'Nginx'],
                'Tools' => ['VS Code', 'Figma', 'Postman', 'Jira']
            ]), 'type' => 'json', 'category' => 'skills'],
            
            // Site ayarları
            ['key' => 'site_title', 'value' => 'Tunahan Akduhan - Portfolio', 'type' => 'text', 'category' => 'site'],
            ['key' => 'site_description', 'value' => 'Full Stack Developer Portfolio', 'type' => 'text', 'category' => 'site'],
            ['key' => 'site_keywords', 'value' => 'php, laravel, vue.js, full stack developer, web development', 'type' => 'text', 'category' => 'site'],
            
            // Hero bölümü ayarları
            ['key' => 'heroGreeting', 'value' => '👋 Merhaba, Ben', 'type' => 'text', 'category' => 'site'],
            ['key' => 'contactButtonText', 'value' => 'İletişime Geç', 'type' => 'text', 'category' => 'site'],
            ['key' => 'cvButtonText', 'value' => "CV'mi İndir", 'type' => 'text', 'category' => 'site'],
            
            // CV ve dosyalar
            ['key' => 'cv_url', 'value' => '/files/tunahan_akduhan_cv.pdf', 'type' => 'url', 'category' => 'files'],
            ['key' => 'profile_image', 'value' => '/images/profile.jpg', 'type' => 'url', 'category' => 'files'],
        ];

        foreach ($personalData as $data) {
            PersonalInfo::updateOrCreate(
                ['key' => $data['key']],
                $data
            );
        }
    }
}
