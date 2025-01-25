<?php

namespace App\Exceptions;

use Exception;

class InvalidModelRelationException extends Exception
{
    public $errorCode;

    public function __construct($message = "The method is not a valid Eloquent relationship.", $errorCode = 422, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->errorCode ?: 500);
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
