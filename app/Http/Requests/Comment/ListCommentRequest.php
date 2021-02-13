<?php

namespace App\Http\Requests\Comment;

use App\Models\Comment;
use Illuminate\Validation\Rules\In;
use Illuminate\Foundation\Http\FormRequest;

class ListCommentRequest extends FormRequest
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
            'state'=>['required', new In(Comment::STATES)]
        ];
    }
}
