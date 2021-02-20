<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Playlist\ShowPlaylistRequest;
use App\Http\Requests\Playlist\CreatePlaylistRequest;
use App\Http\Requests\Playlist\AddVideoToPlaylistRequest;
use App\Http\Requests\Playlist\SortVideoInPlaylistRequest;

class PlaylistController extends Controller
{
    public function getAllPlaylist()
    {
       return Playlist::all();
    }

    public function getMyPlaylist()
    {
        return auth()->user()->playlists;
    }

    public function create(CreatePlaylistRequest $request)
    {
        $user = auth()->user();
        $playlist = $user->playlists()->create($request->validated());

        return response([$playlist], Response::HTTP_CREATED);
    }

    public function addVideo(AddVideoToPlaylistRequest $request)
    {
        // if exist do nothing, but not exist add this video
//        $request->playlist->videos()->syncWithoutDetaching($request->video->id);

        DB::table('playlist_videos')->where(['video_id'=> $request->video->id])->delete();
        $request->playlist->videos()->attach($request->video->id);

        return response(['message'=>'ویدیو با موفقیت به لیست پخش مورد نظر اضافه شد.'], Response::HTTP_OK);
    }

    public function show(ShowPlaylistRequest $request)
    {
        return Playlist::query()->with('videos')->find($request->playlist->id);
    }
    
    public function sortVideosInPlaylist(SortVideoInPlaylistRequest $request)
    {
        $request->playlist->videos()->detach($request->videos);
        $request->playlist->videos()->attach($request->videos);

        return response(['message'=>'لیست پخش با موفقیت مرتب سازی شد.'], Response::HTTP_ACCEPTED);
    }
}
