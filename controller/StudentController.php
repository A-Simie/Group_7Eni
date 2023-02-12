<?php

class StudentController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function applyForAdmission($firstName, $middleName, $lastName, $age, $emailAddress, $phoneNumber, $gender, $race, $localGovernmentArea, $stateOfOrigin, $country, string $department, string $faculty): string
    {
        header("Content-Type: application/json");
        try {
            $response = $this->userService->admissionApplication($firstName, $middleName, $lastName, $age, $emailAddress, $phoneNumber, $gender, $race, $localGovernmentArea, $stateOfOrigin, $country, $department, $faculty);
            http_response_code(201); //created
            return json_encode($response);
        } catch (PDOException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public function checkForAdmission($applicationNumber, $password): string
    {
        header("Content-Type: application/json");
        try {
            http_response_code(200); //ok
            $result = $this->userService->admissionCheck($applicationNumber, $password);
            return json_encode("Congratulations, you've been offered admission to study " . $result['department'] . "in the " . $result['faculty'] . "faculty " . "for a total of 4 years. Check your email for more details.");
            //simply send a document which includes a letter generated with chat gpt that includes his/her matric number and a note telling him/her to proceed to his/her department in the faculty for clearance.
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }
}