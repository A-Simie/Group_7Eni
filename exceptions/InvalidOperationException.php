<?php

class InvalidOperationException extends RuntimeException
{

    public
    function __construct($message = "Operation not supported")
    {
        parent::__construct($message);
    }

}

class ValidationException extends RuntimeException
{

    public function __construct($input)
    {
        parent::__construct($input . " is not a valid input");
    }
}