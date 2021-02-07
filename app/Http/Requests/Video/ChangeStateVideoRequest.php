<?php

namespace App\Http\Requests\Video;

use App\Models\Video;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStateVideoRequest extends FormRequest
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
            'state'=>'required|in:'.Video::STATE_ACCEPTED.','.Video::STATE_BLOCKED
        ];
    }
}
