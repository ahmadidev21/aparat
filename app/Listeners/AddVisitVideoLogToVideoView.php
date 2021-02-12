<?php

namespace App\Listeners;

use Exception;
use App\Models\VideoView;
use App\Events\VisitVideo;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddVisitVideoLogToVideoView
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
     * @param  VisitVideo  $event
     * @return void
     */
    public function handle(VisitVideo $event)
    {
        try {
            $data = [
                'video_id'=>$event->getVideo()->id,
                'user_id'=>auth('api')->id(),
                'user_ip'=>client_ip(),
            ];
            $condition = $data + [['created_at', '>', now()->subDays(1)]];
            if(!VideoView::query()->where($condition)->count()){
                VideoView::create($data);
            }
        }catch (Exception $exception){
            Log::error($exception);
        }
    }
}
