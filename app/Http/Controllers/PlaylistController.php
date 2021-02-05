<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Playlist\CreatePlaylistRequest;

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
}
