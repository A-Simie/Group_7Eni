<?php

class InvalidStudentException extends RuntimeException
{

    private function __construct(string $message)
    {
        parent::__construct($message);
    }


    public static function rusticated($firstName, $lastName): InvalidStudentException
    {
        $message = $firstName . " " . $lastName . " is not a student of Preston. He/she has been rusticated.";
        return new InvalidStudentException($message);
    }

    public static function rusticatedAlready(): InvalidStudentException
    {
        $message = "This student has been already rusticated from Preston";
        return new InvalidStudentException($message);
    }

    public static function notAdmitted($firstName, $lastName): InvalidStudentException
    {
        $message = $firstName . " " . $lastName . " has not been offered admission yet :(, keep on checking...";
        return new InvalidStudentException($message);
    }

}