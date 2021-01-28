<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class UserAlreadyRegisteredException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response([
            'message'=>$this->getMessage()
        ], Response::HTTP_BAD_REQUEST);
    }
}
