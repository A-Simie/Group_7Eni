<?php

class UserAlreadyExistsException extends RuntimeException
{

    public function __construct()
    {
        $message = "The Email address or Phone-number provided already exists. ";
        parent::__construct($message);
    }
}

class StudentDoesNotExist extends RuntimeException
{

    public function __construct()
    {
        $message = "Student Not found !!!";
        parent::__construct($message);
    }
}