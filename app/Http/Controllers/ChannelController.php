<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Channel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateChannelRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;

class ChannelController extends Controller
{
    public function updateChannelInfo(UpdateChannelRequest $request)
    {
        try {
            if($channelId = $request->route('id')){
                $channel = Channel::findOrFail($channelId);
                $user = $channel->user;
            }else{
                $user = auth()->user();
                $channel = $user->channel;
            }

            DB::beginTransaction();

            $channel->name = $request->name;
            $channel->info = $request->info;
            $channel->save();

            $user->website = $request->website;
            $user->save();

            DB::commit();

            return response(['message'=>'ثبت تغییرات با موفقیت انجام شد.'], Response::HTTP_ACCEPTED);

        }catch (\Exception $exception){
            DB::rollBack();
            Log::info($exception);

            return response(['message'=>'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
