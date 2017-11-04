<?php

namespace AppBundle\Exception;

class APIException extends \Exception
{
    /**
     * @var string
     */
    protected $errorCode;

    public function __construct($errorCode, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->errorCode = $errorCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
