<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(User::query()->count()){
            User::query()->truncate();
        }
        $this->createAdminUser();
        for ($i=1 ; $i<5 ; $i++){
            $this->createUser($i);
        }
    }

    private function createAdminUser()
    {
        User::factory()->create([
            'type'=>User::TYPE_ADMIN,
            'name'=>'مدیر اصلی',
            'email'=> 'admin@yahoo.com',
            'mobile'=>'+989000000000'
        ]);

        $this->command->info('کاربر ادمین اصلی سایت ایجاد شد.');
    }

    private function createUser($num)
    {
        User::factory()->create([
            'name'=>'کاربر'.$num,
            'email'=>'user'.$num.'@yahoo.com',
            'mobile'=>'+989'.str_repeat($num, 9),
        ]);

        $this->command->info('به سیستم اضافه شد.'.$num. 'کاربر');
    }
}
