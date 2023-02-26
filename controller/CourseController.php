<?php

class CourseController
{
    public function getFaculties(): string
    {
        return json_encode(StudentService::getFaculties());
    }

    public function getDepartments(): string
    {
        return json_encode(StudentService::getDepartments());
    }
}