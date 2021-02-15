<?php

namespace App\Providers;

use App\Events\VisitVideo;
use App\Events\DeleteVideo;
use App\Listeners\DeleteVideoData;
use App\Events\ActiveUnregisterUser;
use App\Events\UploadeNewVideo;
use Illuminate\Auth\Events\Registered;
use App\Listeners\ProcessUploadedVideo;
use App\Listeners\AddVisitVideoLogToVideoView;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use  Laravel\Passport\Events\AccessTokenCreated;
use Illuminate\Support\Facades\Event;

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
            ProcessUploadedVideo::class,
        ],

        VisitVideo::class=>[
            AddVisitVideoLogToVideoView::class,
        ],

        DeleteVideo::class=>[
            DeleteVideoData::class,
        ],

        AccessTokenCreated::class=>[
            'App\Listeners\ActiveUnregisterUserAfterLogin',
        ],

        ActiveUnregisterUser::class=>[
            //TODO: The things should do it
        ],

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
