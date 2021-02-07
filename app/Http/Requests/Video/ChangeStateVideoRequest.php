<?php

namespace App\Http\Requests\Video;

use App\Models\Video;
use App\Rules\CanChangeVieoState;
use Illuminate\Support\Facades\Gate;
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
        return Gate::allows('change-state', $this->video);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'state'=>['required', new CanChangeVieoState($this->video)]
        ];
    }
}
