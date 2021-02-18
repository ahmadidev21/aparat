<?php

namespace App\Http\Requests\Playlist;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class AddVideoToPlaylistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('addVideo', [$this->playlist, $this->video]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
