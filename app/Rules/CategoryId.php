<?php

namespace App\Rules;

use App\Models\Category;
use Illuminate\Contracts\Validation\Rule;

class CategoryId implements Rule
{
    const PUBLIC_CATEGORY = 'public';
    const PRIVATE_CATEGORY = 'private';
    const ALL_CATEGORY = 'all';

    /**
     * @var string
     */
    private $categoryType;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($categoryType = self::ALL_CATEGORY)
    {
        $this->categoryType = $categoryType;
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
        if($this->categoryType === self::PUBLIC_CATEGORY){
            return  Category::where('id', $value)->whereNull('user_id')->count();
        }
        if($this->categoryType === self::PRIVATE_CATEGORY){
            return Category::where('id', $value)->where('user_id', auth()->id())->count();
        }

        return Category::where('id', $value)->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Category Id';
    }
}
