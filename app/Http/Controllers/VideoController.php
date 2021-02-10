<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Video;
use App\Models\Playlist;
use Illuminate\Support\Str;
use FFMpeg\Format\Video\X264;
use App\Models\VideoFavorite;
use App\Models\VideoRepublish;
use App\Events\UploadeNewVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use FFMpeg\Filters\Video\CustomFilter;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ListVideosRequest;
use App\Http\Requests\Video\LikeRequest;
use Symfony\Component\HttpFoundation\Response;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use App\Http\Requests\Video\UploadVideoRequest;
use App\Http\Requests\Video\CreateVideoRequest;
use App\Http\Requests\Video\RepublishVideoRequest;
use App\Http\Requests\Video\ChangeStateVideoRequest;
use App\Http\Requests\Video\UploadVideoBannerRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class VideoController extends Controller
{
    public function index(ListVideosRequest $request)
    {
        $user = auth('api')->user();
        if ($request->has('republished')) {
            if($user){

                $videos = $request->republished ? $user->republishVideos() : $user->channelVideos();
            }else{
                $videos = $request->republished? Video::whereRepublished() : Video::whereNotRepublished();
            }
        } else {
            $videos = $user ? $user->videos() : Video::query();
        }
        $videos = $videos->orderBy('id')->paginate(20);

        return response([$videos], Response::HTTP_OK);
    }

    /**
     * آپلود ویدیو به صورت موقت
     */
    public function uploadVideo(UploadVideoRequest $request)
    {
        try {
            $video = $request->file('video');
            $fileName = time() . '_' . Str::random(10);
            Storage::disk('videos')->put('temp/' . $fileName, $video->get());

            return response(['video' => $fileName], Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * آپلود کردن بنر برای ویدیو
     */
    public function uploadBanner(UploadVideoBannerRequest $request)
    {
        try {
            $banner = $request->file('banner');
            $fileName = time() . '_' . Str::random(10) . '-banner';
            Storage::disk('videos')->put('temp/' . $fileName, $banner->get());

            return response(['banner' => $fileName], Response::HTTP_OK);
        } catch (Exception $exception) {
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * انتشار ویدیو و ذخیره اطلاعات در دیتابیس
     */
    public function createVideo(CreateVideoRequest $request)
    {
        try {
            DB::beginTransaction();

            $video = Video::create([
                'title' => $request->title,
                'user_id' => auth()->id(),
                'category_id' => $request->category_id,
                'channel_category_id' => $request->channel_category,
                'slug' => '',
                'info' => $request->info,
                'duration' => 0,
                'banner' => null,
                'enable_comments' => $request->enable_comments,
                'publish_at' => $request->publish_at,
                'state' => Video::STATE_PENDING,
            ]);

            $video->slug = uniqid($video->id);
            $video->banner = $video->slug . '-banner';
            $video->save();

            event(new UploadeNewVideo($video, $request));

            if ($request->banner) {
                $banner = $request->video_id . '-banner';
                Storage::disk('videos')->move('temp/' . $request->banner, auth()->id() . '/' . $video->banner);
            }

            if ($request->playlist) {
                $playlist = Playlist::find($request->playlist);
                $playlist->videos()->attach($video->id);
            }

            if (! empty($request->tags)) {
                $video->tags()->attach($request->tags);
            }

            DB::commit();

            return response([$video], Response::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::info($exception);

            return response(['message' => 'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeState(ChangeStateVideoRequest $request)
    {
        $video = $request->video;
        if (empty($video)) {
            throw new ModelNotFoundException('Video Model Not found');
        }
        $video->state = $request->state;
        $video->save();

        return response(['video' => $video], Response::HTTP_ACCEPTED);
    }

    public function republish(RepublishVideoRequest $request)
    {
        try {
            VideoRepublish::create([
                'user_id' => auth()->id(),
                'video_id' => $request->video->id,
            ]);

            return response(['message' => 'باز نشر با موفقیت انجام شد.'], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            Log::info($exception);

            return response(['message' => 'عملیات بازنشر با خطا مواجه شد. مچددا تلاش کنید'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function like(LikeRequest $request)
    {
        $user = auth('api')->user();
        $video = $request->video;
        $like = $request->like;
        $favorite = $user ? $user->favoriteVideos()->where('video_id', $video->id)->first() : null;
        if (empty($favorite)) {
            if ($like) {
                $clientIp = client_ip();
                //if anonymous user want to like so that already liked it.
                if (! $user && VideoFavorite::where([
                        'user_id' => null,
                        'user_ip' => $clientIp,
                        'video_id'=>$video->id
                    ])->count()) {
                    return response(['message' => 'شما قبلا این ویدیو را پسندیده اید.'], Response::HTTP_BAD_REQUEST);
                }
                VideoFavorite::create([
                    'video_id' => $video->id,
                    'user_id' => $user ? $user->id : null,
                    'user_ip' => $clientIp,
                ]);
            } else {
                return response(['message' => 'شما قادر به انجام این کار نیستید.'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            if (! $like) {
                VideoFavorite::where([
                    'video_id' => $video->id,
                    'user_id' => $user->id,
                ])->delete();
            } else {
                return response(['message' => 'شما قبلا این ویدیو را پسندیده اید.'], Response::HTTP_BAD_REQUEST);
            }
        }

        return response(['message' => 'با موفثیت ثبت شد'], Response::HTTP_CREATED);
    }
}
