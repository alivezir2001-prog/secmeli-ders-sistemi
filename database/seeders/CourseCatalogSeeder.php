<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Database\Seeder;
use App\Models\CourseGradeOption;

class CourseCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'ITB',
                'name' => 'İnsan, Toplum ve Bilim',
                'sort_order' => 1,
            ],
            [
                'code' => 'DAD',
                'name' => 'Din, Ahlak ve Değer',
                'sort_order' => 2,
            ],
            [
                'code' => 'KSS',
                'name' => 'Kültür, Sanat ve Spor',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            CourseCategory::updateOrCreate(
                ['code' => $category['code']],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'active' => true,
                ]
            );
        }

                /*
         * MEB Haftalık Ders Çizelgesine göre
         * sınıf → haftalık ders saati seçenekleri
         */
        $gradeOptions = [

            // İNSAN, TOPLUM VE BİLİM
            'Matematik ve Bilim Uygulamaları' => [
                5 => [2],
                6 => [2],
            ],

            'Okuma Becerileri' => [
                5 => [2],
                6 => [2],
            ],

            'Yazarlık ve Yazma Becerileri' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
                8 => [2],
            ],

            'Yaşayan Diller ve Lehçeler' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Yabancı Dil' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Çevre Eğitimi ve İklim Değişikliği' => [
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Şehrimiz ...' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
                8 => [2],
            ],

            'Hukuk ve Adalet' => [
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Düşünme Eğitimi' => [
                7 => [1, 2],
                8 => [2],
            ],

            'Robotik Kodlama' => [
                6 => [2],
                7 => [2],
            ],

            'Yapay Zeka Uygulamaları' => [
                7 => [2],
                8 => [2],
            ],

            'Proje Tasarımı ve Uygulamaları' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
            ],

            'Okul Temelli Sosyal Sorumluluk Çalışmaları' => [
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Medya Okuryazarlığı' => [
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Afet Bilinci' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
            ],

            'Temel Yaşam Becerileri' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
                8 => [2],
            ],

            // DİN, AHLAK VE DEĞER
            'Türk Sosyal Hayatında Aile' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            "Kur'an-ı Kerim" => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Peygamberimizin Hayatı' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Temel Dinî Bilgiler' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Kültür ve Medeniyetimize Yön Verenler' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Ahlak ve Vatandaşlık Eğitimi' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
                8 => [2],
            ],

            'Görgü Kuralları ve Nezaket' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            // KÜLTÜR, SANAT VE SPOR
            'Müzik' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
                8 => [2],
            ],

            'Spor ve Fizikî Etkinlikler' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],

            'Oyun ve Oyun Etkinlikleri' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
            ],

            'Dijital Sanatlar' => [
                6 => [1, 2],
                7 => [1, 2],
            ],

            'Masal ve Destanlarımız' => [
                5 => [1, 2],
                6 => [1, 2],
                7 => [1, 2],
                8 => [2],
            ],

            'Geleneksel Sanatlar' => [
                6 => [1, 2],
                7 => [1, 2],
            ],

            'Halk Oyunları' => [
                5 => [2],
                6 => [2],
                7 => [2],
                8 => [2],
            ],
        ];

        foreach ($gradeOptions as $courseName => $grades) {
            $course = Course::where('name', $courseName)->first();

            if (! $course) {
                continue;
            }

            foreach ($grades as $grade => $hours) {
                foreach ($hours as $weeklyHours) {
                    CourseGradeOption::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'grade' => $grade,
                            'weekly_hours' => $weeklyHours,
                        ]
                    );
                }
            }
        }

        $courses = [
            // İNSAN, TOPLUM VE BİLİM
            [
                'category' => 'ITB',
                'name' => 'Matematik ve Bilim Uygulamaları',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Okuma Becerileri',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Yazarlık ve Yazma Becerileri',
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Yaşayan Diller ve Lehçeler',
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => false,
            ],
            [
                'category' => 'ITB',
                'name' => 'Yabancı Dil',
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Çevre Eğitimi ve İklim Değişikliği',
                'max_selections' => 1,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Şehrimiz ...',
                'max_selections' => 1,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Hukuk ve Adalet',
                'max_selections' => 1,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Düşünme Eğitimi',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Robotik Kodlama',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => false,
            ],
            [
                'category' => 'ITB',
                'name' => 'Yapay Zeka Uygulamaları',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => false,
            ],
            [
                'category' => 'ITB',
                'name' => 'Proje Tasarımı ve Uygulamaları',
                'max_selections' => 3,
                'is_modular' => true,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Okul Temelli Sosyal Sorumluluk Çalışmaları',
                'max_selections' => 3,
                'is_modular' => true,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Medya Okuryazarlığı',
                'max_selections' => 1,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Afet Bilinci',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'ITB',
                'name' => 'Temel Yaşam Becerileri',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],

            // DİN, AHLAK VE DEĞER
            [
                'category' => 'DAD',
                'name' => 'Türk Sosyal Hayatında Aile',
                'max_selections' => 1,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'DAD',
                'name' => "Kur'an-ı Kerim",
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => true,
            ],
            [
                'category' => 'DAD',
                'name' => 'Peygamberimizin Hayatı',
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => true,
            ],
            [
                'category' => 'DAD',
                'name' => 'Temel Dinî Bilgiler',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'DAD',
                'name' => 'Kültür ve Medeniyetimize Yön Verenler',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'DAD',
                'name' => 'Ahlak ve Vatandaşlık Eğitimi',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'DAD',
                'name' => 'Görgü Kuralları ve Nezaket',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],

            // KÜLTÜR, SANAT VE SPOR
            [
                'category' => 'KSS',
                'name' => 'Müzik',
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => false,
            ],
            [
                'category' => 'KSS',
                'name' => 'Spor ve Fizikî Etkinlikler',
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => true,
            ],
            [
                'category' => 'KSS',
                'name' => 'Oyun ve Oyun Etkinlikleri',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'KSS',
                'name' => 'Dijital Sanatlar',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => false,
            ],
            [
                'category' => 'KSS',
                'name' => 'Masal ve Destanlarımız',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'KSS',
                'name' => 'Geleneksel Sanatlar',
                'max_selections' => 2,
                'is_modular' => false,
                'offered' => true,
            ],
            [
                'category' => 'KSS',
                'name' => 'Halk Oyunları',
                'max_selections' => 4,
                'is_modular' => true,
                'offered' => false,
            ],
        ];

        foreach ($courses as $course) {
            $category = CourseCategory::where('code', $course['category'])->firstOrFail();

            Course::updateOrCreate(
                [
                    'course_category_id' => $category->id,
                    'name' => $course['name'],
                ],
                [
                    'max_selections' => $course['max_selections'],
                    'is_modular' => $course['is_modular'],
                    'offered' => $course['offered'],
                    'active' => true,
                    'notes' => null,
                ]
            );
        }
    }
}