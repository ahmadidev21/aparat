<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Video;
use App\Policies\UserPolicy;
use App\Policies\VideoPolicy;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Video::class => VideoPolicy::class,
//        User::class => UserPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        //اضافه کردن روت های پاسپورت
//        Passport::routes();
        //اضافه کردن زمان انقضا برای توکن و رفرش توکن
        Passport::tokensExpireIn(now()->addMinutes(config('auth.token_expiration.token')));
        Passport::refreshTokensExpireIn(now()->addMinutes(config('auth.token_expiration.refresh_token')));

        $this->registerGates();
    }

    private function registerGates()
    {
        Gate::before(function ($user, $ability){
            if($user->isAdmin()){
                return true;
            }
        });
    }
}
