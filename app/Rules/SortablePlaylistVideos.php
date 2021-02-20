<?php

namespace App\Rules;

use App\Models\Playlist;
use Illuminate\Contracts\Validation\Rule;

class SortablePlaylistVideos implements Rule
{
    /**
     * @var \App\Models\Playlist
     */
    private Playlist $playlist;

    /**
     * Create a new rule instance.
     *
     * @param  \App\Models\Playlist  $playlist
     */
    public function __construct(Playlist $playlist)
    {
        //
        $this->playlist = $playlist;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(is_array($value)){
            $videos = $this->playlist->videos()->pluck('videos.id')->toArray();
            $value = array_map('intval', $value);
            sort($value);
            sort($videos);

            return  $videos === $value;
        }

        return  false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'video list is not valid for this play list.';
    }
}
