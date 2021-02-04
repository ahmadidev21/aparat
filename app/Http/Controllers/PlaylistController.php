<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use Illuminate\Http\Request;

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
}
