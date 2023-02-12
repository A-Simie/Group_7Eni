<?php

class InvalidCredentialsException extends RuntimeException
{

    public function __construct()
    {
        $message = " The provided Credentials are not valid";
        parent::__construct($message);
    }
}