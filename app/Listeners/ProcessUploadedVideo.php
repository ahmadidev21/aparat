<?php

namespace App\Listeners;

use Illuminate\Http\Request;
use FFMpeg\Format\Video\X264;
use App\Events\UploadeNewVideo;
use FFMpeg\Filters\Video\CustomFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use App\Jobs\ConvertAndAddWaterMartToUploadedVideo;

class ProcessUploadedVideo
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UploadeNewVideo  $event
     * @return void
     */
    public function handle(UploadeNewVideo $event)
    {
        $video = $event->getVideo();
        $VideoId = $event->getRequest()->video_id;
       ConvertAndAddWaterMartToUploadedVideo::dispatch($video, $VideoId);
    }
}
