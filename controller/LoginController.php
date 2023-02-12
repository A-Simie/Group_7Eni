<?php

class LoginController
{

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function adminLogin($emailAddress, $password): string
    {
        header("Content-Type: application/json");
        try {
            http_response_code(200); //ok
            return $this->userService->loginFunctionalityAdmin($emailAddress, $password);
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public function studentLogin($matricNumber, $password): string
    {
        header("Content-Type: application/json");
        try {
            http_response_code(200); //ok
            return $this->userService->checkForAdmission($matricNumber, $password);
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }
}