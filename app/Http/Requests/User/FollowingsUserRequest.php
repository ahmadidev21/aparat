<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class FollowingsUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('seeFollowingList', User::class);
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
