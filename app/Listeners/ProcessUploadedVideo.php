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
        $request = $event->getRequest();
        $video = $event->getVideo();

        $videoUploadedPath = '/temp/' . $request->video_id;
        $videoUploaded = FFMpeg::fromDisk('videos')->open($videoUploadedPath);

        $filter = new CustomFilter(
            "drawtext=text='http\\://pydeveloper.ir: fontcolor=white: fontsize=24: 
                box=1: boxcolor=red@0.5: boxborderw=5: x=10: y=(h - text_h - 10)'");
        $format = new X264('libmp3lame');
        $videoFile = $videoUploaded->addFilter($filter)
            ->export()
            ->toDisk('videos')
            ->inFormat($format);

        $videoFile->save(auth()->id().'/'. $video->slug.'.mp4');
        Storage::disk('videos')->delete($videoUploadedPath);
        $video->duration = $videoUploaded->getDurationInSeconds();
    }
}
