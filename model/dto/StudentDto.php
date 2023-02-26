<?php

class StudentDto
{
    private string $studentId;
    private string $firstName;
    private string $middleName;
    private string $lastName;
    private string $age;
    private string $gender;
    private string $phoneNumber;
    private string $emailAddress;
    private string $department;
    private string $faculty;
    private string $allocatedYear;
    private string $desiredYear;
    private string $applicationNumber;
    private string $applicationDate;
    private string $rusticated;

    public function __construct(string $studentId, string $firstName, string $middleName, string $lastName, string $age, string $gender, string $phoneNumber, string $emailAddress, string $department, string $faculty, string $allocatedYear, string $desiredYear, string $applicationNumber, string $applicationDate, string $rusticated)
    {
        $this->studentId = $studentId;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->age = $age;
        $this->gender = $gender;
        $this->phoneNumber = $phoneNumber;
        $this->emailAddress = $emailAddress;
        $this->department = $department;
        $this->faculty = $faculty;
        $this->allocatedYear = $allocatedYear;
        $this->desiredYear = $desiredYear;
        $this->applicationNumber = $applicationNumber;
        $this->applicationDate = $applicationDate;
        $this->rusticated = $rusticated;
    }

//    public function __toString(): string
//    {
//        return "StudentDto{ " . $this->studentId .
//            $this->firstName .
//            $this->middleName .
//            $this->lastName .
//            $this->age .
//            $this->gender .
//            $this->phoneNumber .
//            $this->emailAddress .
//            $this->department .
//            $this->faculty .
//            $this->allocatedYear .
//            $this->desiredYear .
//            $this->applicationNumber .
//            $this->applicationDate .
//            $this->rusticated . " }";
//    }


}