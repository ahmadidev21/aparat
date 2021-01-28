<?php

namespace App\Http\Requests\Auth;

trait GetRegisterFieldAndValueTrait
{
    public function getFieldName()
    {
        return $this->has('email') ? 'email' : 'mobile';
    }

    public function getFieldValue()
    {
        return $this->input($this->getFieldName());
    }
}