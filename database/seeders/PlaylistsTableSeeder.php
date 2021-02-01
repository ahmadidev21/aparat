<?php

namespace Database\Seeders;

use App\Models\Playlist;
use Illuminate\Database\Seeder;

class PlaylistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Playlist::count()){
            Playlist::query()->truncate();
        }

        $playlists = [
            'لیست پخش 1',
            'لیست پخش 2'
        ];

        foreach ($playlists as $playlistName){
            Playlist::create([
                'title'=>$playlistName,
                'user_id'=>2
            ]);
        }

        $this->command->info('create playlists: '.implode(', ', $playlists));
    }
}
