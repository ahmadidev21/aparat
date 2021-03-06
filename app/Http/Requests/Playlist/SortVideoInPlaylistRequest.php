<?php

namespace App\Http\Requests\Playlist;

use Illuminate\Support\Facades\Gate;
use App\Rules\SortablePlaylistVideos;
use Illuminate\Foundation\Http\FormRequest;

class SortVideoInPlaylistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('sortVideoInPlaylist', $this->playlist);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
                'videos'=>['required', new SortablePlaylistVideos($this->playlist)]
        ];
    }
}
