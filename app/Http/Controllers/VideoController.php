<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Video;
use App\Models\Playlist;
use App\Events\VisitVideo;
use Illuminate\Support\Str;
use App\Events\DeleteVideo;
use App\Models\VideoFavorite;
use App\Models\VideoRepublish;
use App\Events\UploadeNewVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Video\LikeRequest;
use App\Http\Requests\DeleteVideoRequest;
use App\Http\Requests\Video\unLikeRequest;
use App\Http\Requests\Video\ShowVideoRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Video\ListVideosRequest;
use App\Http\Requests\Video\UploadVideoRequest;
use App\Http\Requests\Video\CreateVideoRequest;
use App\Http\Requests\Video\LikedByCurrentUser;
use App\Http\Requests\Video\UpdateVideoRequest;
use App\Http\Requests\User\FollowersUserRequest;
use App\Http\Requests\User\FollowingsUserRequest;
use App\Http\Requests\Video\RepublishVideoRequest;
use App\Http\Requests\Video\ChangeStateVideoRequest;
use App\Http\Requests\Video\UploadVideoBannerRequest;
use App\Http\Requests\Video\ShowVideoStatisticsRequest;
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
        VideoFavorite::create([
            'video_id'=>$request->video->id,
            'user_id'=> auth('api')->id(),
            'user_ip'=>client_ip()
        ]);

        return response(['message'=>'با موفثیت ثبت شد '], Response::HTTP_OK);
    }

    public function unLike(unLikeRequest $request)
    {
        $user = auth('api')->user();
        $condition = [
            'video_id'=>$request->video->id,
            'user_id'=>$user ? $user->id : null
        ];
        if(empty($user)){
            $condition['user_ip'] = client_ip();
        }

        VideoFavorite::query()->where($condition)->delete();

        return response(['message'=>'با موفثیت ثبت شد '], Response::HTTP_OK);
    }

    public function likedByCurrentUser(LikedByCurrentUser $request)
    {
        return $request->user()->favoriteVideos()->paginate();
    }

    public function show(ShowVideoRequest $request)
    {
        event(new VisitVideo($request->video));
        return $request->video;
    }

    public function delete(DeleteVideoRequest $request)
    {
        try {
            DB::beginTransaction();
            $request->video->forceDelete();
            event(new DeleteVideo($request->video));
            DB::commit();

            return response(['message'=>'حذف ویدیو با موفقیت انحام شد.'], Response::HTTP_OK);
        }catch (Exception $exception){
            DB::rollBack();
            Log::error($exception);
            return response(['message'=>' ویدیوحذف نشد.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function statistics(ShowVideoStatisticsRequest $request)
    {
        $fromDate = now()->subDays($request->get('last_n_days', 7))->toDateString();

        $data = [
            'views'=>[],
            'total_views'=>0,
        ];

        Video::views(auth('api')->id())
            ->where('videos.id', $request->video->id)
            ->whereRaw("date(video_views.created_at) >= '{$fromDate}'")
            ->selectRaw('date(video_views.created_at) as date, count(*) as views')
            ->groupByRaw('date(video_views.created_at)')->get()->each(function ($item)use (&$data){
                $data['views'][$item->date] = $item->views;
                $data['total_views'] += $item->views;
            });

        return $data;
    }

    public function update(UpdateVideoRequest $request)
    {
        $video = $request->video;

        try {
            DB::beginTransaction();

            if($request->has('title')) $video->title = $request->title;
            if($request->has('info')) $video->info = $request->info;
            if($request->has('category_id')) $video->category_id = $request->category_id;
            if($request->has('channel_category')) $video->channel_category_id = $request->channel_category;
            if($request->has('enable_comments')) $video->enable_comments = $request->enable_comments;

            if($request->banner){
                Storage::disk('videos')->delete(auth()->id() . '/' . $video->banner);
                Storage::disk('videos')->move('temp/' . $request->banner, auth()->id() . '/' . $video->banner);
            }

            if(!empty($request->tags)){
                $video->tags()->sync($request->tags);
            }
            $video->save();
            DB::commit();

            return response([$video], Response::HTTP_OK);

        }catch (Exception $exception){
            DB::rollBack();
            Log::error($exception);

            return response(['message'=>'خطایی در سمت سرور رخ داده است.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}
