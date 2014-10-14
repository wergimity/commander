<?php
namespace Wergimity\Commander\Exception;

use Exception;

class CommandValidationException extends Exception
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}