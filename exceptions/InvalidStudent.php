<?php

class InvalidStudent extends RuntimeException
{

    private function __construct(string $message)
    {
        parent::__construct($message);
    }


    public static function rusticated($firstName, $lastName): InvalidStudent
    {
        $message = $firstName . " " . $lastName . " is not a student of Preston. He/she has been rusticated.";
        return new InvalidStudent($message);
    }

    public static function rusticatedAlready(): InvalidStudent
    {
        $message = "This student has been already rusticated from Preston";
        return new InvalidStudent($message);
    }

    public static function notAdmitted($firstName, $lastName): InvalidStudent
    {
        $message = $firstName . " " . $lastName . " has not been offered admission yet :(, keep on checking...";
        return new InvalidStudent($message);
    }

}