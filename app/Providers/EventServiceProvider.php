<?php

namespace App\Providers;

use App\Events\VisitVideo;
//use App\Events\UploadeNewVideoLaravel\Passport\Events\AccessTokenCreated;
use App\Events\ActiveUnregisterUser;
use Illuminate\Auth\Events\Registered;
use App\Listeners\ProcessUploadedVideo;
use App\Listeners\AddVisitVideoLogToVideoView;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use  Laravel\Passport\Events\AccessTokenCreated;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UploadeNewVideo::class=>[
            ProcessUploadedVideo::class
        ],

        VisitVideo::class=>[
            AddVisitVideoLogToVideoView::class
        ],
        AccessTokenCreated::class=>[
            'App\Listeners\ActiveUnregisterUserAfterLogin'
        ],
        ActiveUnregisterUser::class=>[
            //TODO: The things should do it
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
//        Event::listen('*', function ($event){
//            var_dump($event);
//        });
    }
}
