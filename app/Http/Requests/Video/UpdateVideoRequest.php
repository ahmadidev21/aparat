<?php

namespace App\Http\Requests\Video;

use App\Rules\CategoryId;
use App\Rules\OwnPlaylistId;
use Illuminate\Support\Facades\Gate;
use App\Rules\UploadedVideoBannerId;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', $this->video);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'=>'required|string|max:255',
            'info'=>'nullable|string',
            'tags'=>'nullable|array',
            'tags.*'=>'exists:tags,id',
            'category_id'=>['required', new CategoryId(CategoryId::PUBLIC_CATEGORY)],
            'channel_category'=>['nullable', new CategoryId(CategoryId::PRIVATE_CATEGORY)],
            'enable_comments'=>'required|boolean',
            'banner'=>['nullable', new UploadedVideoBannerId()],
        ];
    }
}
