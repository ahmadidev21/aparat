<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Channel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Channel\updateSocialRequest;
use App\Http\Requests\Channel\UpdateChannelRequest;
use App\Http\Requests\Channel\UploadAvatarForChannelRequest;

class ChannelController extends Controller
{
    /**
     * آپدیت کردن اطلاعات کانال
     */
    public function updateChannelInfo(UpdateChannelRequest $request)
    {
        try {
            if ($channelId = $request->route('id')) {
                $channel = Channel::findOrFail($channelId);
                $user = $channel->user;
            } else {
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

            return response(['message' => 'ثبت تغییرات با موفقیت انجام شد.'], Response::HTTP_ACCEPTED);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * آپلود کردن آواتار برای کانال
     */
    public function uploadAvatarForChannel(UploadAvatarForChannelRequest $request)
    {
        try {
            $banner = $request->file('banner');
            $fileName = auth()->id() . '-' . Str::random(15);
            Storage::disk('channels')->put($fileName, $banner->get());
            $channel = auth()->user()->channel;

            if ($channel->banner) {
                $delFileName = Str::after($channel->banner, 'channels\\');
                Storage::disk('channels')->delete($delFileName);
            }

            $channel->banner = Storage::disk('channels')->path($fileName);
            $channel->save();

            return response([
                'banner' => Storage::disk('channels')->url($fileName),
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            dd($exception);
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateSocials(updateSocialRequest $request)
    {
        try {
            $socials = [
                'cloob' => $request->cloob,
                'lenzor' => $request->lenzor,
                'twitter' => $request->twitter,
                'facebook' => $request->facebook,
                'telegram' => $request->telegram,
            ];

            auth()->user()->channel->update(['socials' =>$socials]);

            return response(['message' => 'ثبت تغییرات با موفقیت انجام شد'], Response::HTTP_ACCEPTED);
        } catch (Exception $exception) {
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
