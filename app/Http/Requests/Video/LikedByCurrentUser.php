<?php

namespace App\Http\Requests\Video;

use App\Models\Video;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class LikedByCurrentUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        return Gate::allows('seeLikedVideos', Video::class);
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
