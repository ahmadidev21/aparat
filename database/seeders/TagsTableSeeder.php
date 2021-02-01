<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Tag::query()->count()){
            Tag::query()->truncate();
        }
        $tags = [
            'علم و تکنولوژی',
            'خبری',
            'کارتون',
            'طنز',
            'آموزشی',
            'تفریحی',
            'فیلم',
            'مذهبی',
            'موسیقی',
            'سیاسی',
            'ورزشی',
            'حوادث',
            'گیم',
            'گردشگری',
            'حیوانات',
            'متفرقه',
            'تبلیغات',
            'هنری',
            'بانوان',
            'سلامت',
            'آشپزی',
            'سریال و فیلم‌های سینمایی'
        ];

        foreach ($tags as $tagTitle) {
            Tag::create([
                'title'=>$tagTitle
            ]);
        }
        $this->command->info('add this tag'. implode(', ', $tags));
    }
}
