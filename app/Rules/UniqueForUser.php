<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueForUser implements Rule
{
    private $table;

    /**
     * @var \App\Rules\string
     */
    private $columnName;

    /**
     * @var \App\Rules\string
     */
    private $userId;

    /**
     * @var \App\Rules\string
     */
    private $userIdField;

    /**
     * Create a new rule instance.
     *
     * @param  \App\Rules\string  $table
     * @param  \App\Rules\string|null  $columnName
     * @param  \App\Rules\string|null  $useId
     * @param  \App\Rules\string  $userIdField
     */
    public function __construct(string $table, string $columnName = null, string $useId = null, string $userIdField = 'user_id')
    {
        $this->table = $table;
        $this->columnName = $columnName;
        $this->userId = $useId ?? auth()->id();
        $this->userIdField = $userIdField;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $field = ! empty($this->columnName) ? $this->columnName : $attribute;
        $count = DB::table($this->table)->where([$field=>$value, $this->userIdField=>$this->userId])->count();
        return $count ===0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This value already exists';
    }
}
