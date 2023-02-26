<?php

class StudentController
{
    private UserService $userService;
    private StudentService $studentService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->studentService = new StudentService(new DatabaseConnect());
    }

    //student controller
    public function checkForAdmission($applicationNumber, $password): string
    {
        try {
            http_response_code(200); //ok

            $result = $this->userService->admissionCheck($applicationNumber, $password);
            return json_encode($result);
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public function changePassword($matricNumber, $lastName, $password, int $otp): string
    {
        try {
            http_response_code(200); //ok
            $response = $this->studentService->changePassword($matricNumber, $lastName, $password, $otp);
            return json_encode($response);
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public
    function studentLogin($matricNumber, $password): string
    {
        try {
            http_response_code(201); //ok
            $response = $this->studentService->StudentLogin($matricNumber, $password);
            return json_encode($response);
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public
    function applyForAdmission($firstName, $middleName, $lastName, $age, $emailAddress, $phoneNumber, $gender, $localGovernmentArea, $stateOfOrigin, $country, $department, $faculty, $desiredLevel): string
    {
        try {
            http_response_code(201); //created
            $response = $this->userService->admissionApplication($firstName, $middleName, $lastName, $age, $emailAddress, $phoneNumber, $gender, $localGovernmentArea, $stateOfOrigin, $country, $department, $faculty, $desiredLevel);
            return json_encode($response);
        } catch (ValidationException $validationException) {
            http_response_code(400); //bad request
            return json_encode($validationException->getMessage());
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public function otpRequest($phoneNumber,$emailAddress): int
    {
        try {
            http_response_code(200);
            $response = $this->studentService->otpRequest($phoneNumber,$emailAddress);
            return json_encode($response);
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }
}