<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
//require "../service/UserService.php";
//require "../exceptions/AuthorizationFailedException.php";
//require "../exceptions/AuthenticationException.php";

class AdminController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function adminLogin($emailAddress, $password): string
    {
        try {
            http_response_code(200); //ok
            //regex to match email address
            $response = $this->userService->loginFunctionalityAdmin($emailAddress, $password);
            return json_encode($response);
        } catch (AuthorizationFailedException $exception) {
            http_response_code(403); //bad request
            return json_encode($exception->getMessage());
        } catch (AuthenticationException $exception) {
            http_response_code(401); //bad request
            return json_encode($exception->getMessage());
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public function approveOrDeclineApplications(int $studentId, string $applicationNumber, string $admissionStatus): string
    {
        $matches = false;
        try {
            http_response_code(200); //ok
            if (strlen($applicationNumber) != 20)
                throw new ValidationException($applicationNumber);
            for ($i = 0; $i < AdmissionStatusEnum::cases(); $i++) {
                if (AdmissionStatusEnum::cases()[$i] == $admissionStatus) {
                    $matches = true;
                    break;
                }
            }
            if (!$matches)
                throw new ValidationException($admissionStatus);
            $result = $this->userService->approveOrDeclineApplications($studentId, $applicationNumber, $admissionStatus);
            return json_encode($result);
        } catch (AuthorizationFailedException $exception) {
            http_response_code(403);
            return json_encode($exception->getMessage());
        } catch (AuthenticationException $exception) {
            http_response_code(401);
            return json_encode($exception->getMessage());
        } catch (ValidationException $validationException) {
            http_response_code(400); //bad request
            return json_encode($validationException->getMessage());
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        } catch (Exception $exception) {
            http_response_code(500); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public function retrieveAllPendingApplications(): string
    {
        try {
            http_response_code(200); //ok
            $result = $this->userService->retrieveAllPendingApplications();
            return json_encode($result);
        } catch (AuthorizationFailedException $exception) {
            http_response_code(403);
            return json_encode($exception->getMessage());
        } catch (AuthenticationException $exception) {
            http_response_code(401);
            return json_encode($exception->getMessage());
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        } catch (Exception $exception) {
            http_response_code(500); //bad request
            return json_encode("An exception occurred");
        }
    }

    public function createAdmin($firstName, $middleName, $lastName, $age, $email, $phoneNumber, $gender, $localGovernmentArea, $stateOfOrigin, $country, $password): string
    {
        try {
            http_response_code(200); //ok
            //input regex to match phone number and email address
            $message = $this->userService->createAdmin($firstName, $middleName, $lastName, $age, $email, $phoneNumber, $gender, $localGovernmentArea, $stateOfOrigin, $country, $password);
            return json_encode($message);
        } catch (PDOException $exception) {
            http_response_code(400); //bad request
            echo $exception->getMessage();
            return json_encode($exception->getMessage());
        } catch (AuthorizationFailedException $exception) {
            http_response_code(403);
            return json_encode($exception->getMessage());
        } catch (AuthenticationException $exception) {
            http_response_code(401);
            return json_encode($exception->getMessage());
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }

    public function addDepartment($department, $faculty, $numberOfYears, $courseCode): string
    {
        try {
            http_response_code(200); //ok
            if ($numberOfYears <= 1)
                throw new ValidationException($numberOfYears);
            return $this->userService->addDepartmentAndFaculty($department, $faculty, $numberOfYears, $courseCode);
        } catch (AuthorizationFailedException $exception) {
            http_response_code(403);
            return json_encode($exception->getMessage());
        } catch (AuthenticationException $exception) {
            http_response_code(401);
            return json_encode($exception->getMessage());
        } catch (RuntimeException $exception) {
            http_response_code(400); //bad request
            return json_encode($exception->getMessage());
        }
    }
}