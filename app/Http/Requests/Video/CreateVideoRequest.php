<?php

namespace App\Http\Requests\Video;

use App\Rules\CategoryId;
use App\Rules\OwnPlaylistId;
use App\Rules\UploadedVideoId;
use App\Rules\UploadedVideoBannerId;
use Illuminate\Foundation\Http\FormRequest;

class CreateVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'video_id'=>['required', new UploadedVideoId()],
            'category_id'=>['required', new CategoryId(CategoryId::PUBLIC_CATEGORY)],
            'title'=>'required|string|max:255',
            'info'=>'nullable|string',
            'tags'=>'nullable|array',
            'tags.*'=>'exists:tags,id',
            'playlist'=>['nullable', new OwnPlaylistId()],
            'channel_category'=>['nullable', new CategoryId(CategoryId::PRIVATE_CATEGORY)],
            'banner'=>['nullable', new UploadedVideoBannerId()],
            'publish_at'=>'nullable|date_format:Y-m-d H:i:s|after:now',
        ];
    }
}

