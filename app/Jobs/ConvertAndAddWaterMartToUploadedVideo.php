<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use FFMpeg\Format\Video\X264;
use FFMpeg\Filters\Video\CustomFilter;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ConvertAndAddWaterMartToUploadedVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\Video
     */
    private Video $video;

    /**
     * @var string
     */
    private string $videoId;

    /**
     * @var int|string|null
     */
    private $authId;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\Video  $video
     * @param  string  $videoId
     */
    public function __construct(Video $video, string $videoId)
    {
        //
        $this->video = $video;
        $this->videoId = $videoId;
        $this->authId = auth()->id();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $videoUploadedPath = '/temp/' . $this->videoId;
        $videoUploaded = FFMpeg::fromDisk('videos')->open($videoUploadedPath);

        $filter = new CustomFilter(
            "drawtext=text='http\\://pydeveloper.ir: fontcolor=white: fontsize=24: 
                box=1: boxcolor=red@0.5: boxborderw=5: x=10: y=(h - text_h - 10)'");
        $format = new X264('libmp3lame');
        $videoFile = $videoUploaded->addFilter($filter)
            ->export()
            ->toDisk('videos')
            ->inFormat($format);

        $videoFile->save($this->authId.'/'. $this->video->slug.'.mp4');
        Storage::disk('videos')->delete($videoUploadedPath);
        $this->video->duration = $videoUploaded->getDurationInSeconds();
    }
}
