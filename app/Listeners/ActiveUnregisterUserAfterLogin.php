<?php

namespace App\Listeners;


use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\ActiveUnregisterUser;
use Laravel\Passport\Events\AccessTokenCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ActiveUnregisterUserAfterLogin
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
     * @param  AccessTokenCreated  $event
     *
     * @throws \Throwable
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        $user = User::withTrashed()->find($event->userId);
        if($user->trashed()){
            try {
                DB::beginTransaction();
                $user->restore();
                event(new ActiveUnregisterUser($user));
                Log::info('active unregister user',['user_id'=>$user->id]);
                DB::commit();
            }catch (Exception $exception){
                DB::rollBack();
                Log::error($exception);
                throw $exception;
            }
        }
    }
}
