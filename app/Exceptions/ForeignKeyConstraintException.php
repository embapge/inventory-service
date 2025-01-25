<?php

namespace App\Exceptions;

use Exception;

class ForeignKeyConstraintException extends Exception
{
    public $errorCode;

    public function __construct($message = "Cannot delete this record because it is referenced in other records.", $errorCode = 409, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
