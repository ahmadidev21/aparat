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
        $this->createAdminUser();
        $this->createUser();
    }

    private function createAdminUser()
    {
        User::factory()->create([
            'type'=>User::TYPE_ADMIN,
            'name'=>'مدیر اصلی',
            'email'=> 'admin@yahoo.com',
            'mobile'=>'+989111111111'
        ]);

        $this->command->info('کاربر ادمین اصلی سایت ایجاد شد.');
    }

    private function createUser()
    {
        User::factory()->create([
            'name'=>'کاربر1',
            'email'=>'user@yahoo.com',
            'mobile'=>'+989222222222',
        ]);

        $this->command->info('یک کاربر پیش فرض به سیستم اضافه شد.');
    }
}
