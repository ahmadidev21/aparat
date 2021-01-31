<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Category::query()->count()){
            Category::query()->truncate();
        }
        $categories = [
            'علم و تکنولوژی' => [
                'icon' => '',
                'banner' => null,
            ],
            'خبری' => [
                'icon' => '',
                'banner' => null,
            ],
            'کارتون' => [
                'icon' => '',
                'banner' => null,
            ],
            'طنز' => [
                'icon' => '',
                'banner' => null,
            ],
            'آموزشی' => [
                'icon' => '',
                'banner' => null,
            ],
            'تفریحی' => [
                'icon' => '',
                'banner' => null,
            ],
            'فیلم' => [
                'icon' => '',
                'banner' => null,
            ],
            'مذهبی' => [
                'icon' => '',
                'banner' => null,
            ],
            'موسیقی' => [
                'icon' => '',
                'banner' => null,
            ],
            'سیاسی' => [
                'icon' => '',
                'banner' => null,
            ],
            'ورزشی' => [
                'icon' => '',
                'banner' => null,
            ],
            'حوادث' => [
                'icon' => '',
                'banner' => null,
            ],
            'گیم' => [
                'icon' => '',
                'banner' => null,
            ],
            'گردشگری' => [
                'icon' => '',
                'banner' => null,
            ],
            'حیوانات' => [
                'icon' => '',
                'banner' => null,
            ],
            'متفرقه' => [
                'icon' => '',
                'banner' => null,
            ],
            'تبلیغات' => [
                'icon' => '',
                'banner' => null,
            ],
            'هنری' => [
                'icon' => '',
                'banner' => null,
            ],
            'بانوان' => [
                'icon' => '',
                'banner' => null,
            ],
            'سلامت' => [
                'icon' => '',
                'banner' => null,
            ],
            'آشپزی' => [
                'icon' => '',
                'banner' => null,
            ],
            'سریال و فیلم‌های سینمایی' => [
                'icon' => '',
                'banner' => null,
            ],
        ];

        foreach ($categories as $categoryName => $option){
                Category::create([
                    'title'=>$categoryName,
                    'icon'=>$option['icon'],
                    'banner'=>$option['banner']
                ]);
            $this->command->info('add '.$categoryName.' category');
        }
    }
}
