<?php

namespace App\Events;

use App\Models\Video;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class DeleteVideo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    private Video $video;

    /**
     * DeleteVideo constructor.
     *
     * @param  \App\Models\Video  $video
     */
    public function __construct(Video $video)
    {
        //
        $this->video = $video;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }


    public function getVideo()
    {
        return $this->video;
    }
}
