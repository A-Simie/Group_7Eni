<?php

class AuthenticationException extends RuntimeException
{

    public function __construct()
    {
        $message = "Invalid Credentials";
        parent::__construct($message);
    }
}