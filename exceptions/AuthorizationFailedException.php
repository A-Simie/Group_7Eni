<?php

class AuthorizationFailedException extends RuntimeException
{

    public function __construct()
    {
        parent::__construct("Forbidden");
    }
}