<?php

class InvalidOperationException extends RuntimeException
{

    public function __construct()
    {
        parent::__construct("Operation not supported");
    }
}

class ValidationException extends RuntimeException
{

    public function __construct($input)
    {
        parent::__construct($input . " is not a valid input");
    }
}