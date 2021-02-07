<?php

namespace App\Events;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UploadeNewVideo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    private $video;

    /**
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Video  $video
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Video $video, Request $request)
    {
        //
        $this->video = $video;
        $this->request = $request;
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

    /**
     * @return \App\Events\Video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @return \Illuminate\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
