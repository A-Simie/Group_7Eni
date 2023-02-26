<?php

use Firebase\JWT\JWT;

class UserService
{
    private DatabaseConnect $databaseConnect;

    public function __construct()
    {
        $this->databaseConnect = new DatabaseConnect();
    }

    //admission application
    public function admissionApplication($firstName, $middleName, $lastName, $age, $emailAddress, $phoneNumber, $gender, $localGovernmentArea, $stateOfOrigin, $country, $department, $faculty, $desiredLevel = 100): string|bool
    {
        //faculty and department check...
        $facultyMatches = false; //this will be reassigned if faculty matches...
        $facultyName = null; //this will be reassigned to the faculty name...
        $departmentMatches = false; //this will be reassigned if department matches...
        $departmentArray = null; // when reassigned, it contains the course code and the CourseNumber of years

        foreach (Faculty::classConstants() as $constant) {
            if ($faculty == $constant) {
                $facultyMatches = true;
                $facultyName = $constant;
                break;
            }
        }

        if (!$facultyMatches)
            throw new ValidationException($faculty);

        switch ($facultyName) {
            case Faculty::CIS:
                foreach (Cis::classConstants() as $constant) {
                    if ($department == $constant[0]) {
                        $departmentMatches = true;
                        $departmentArray[0] = $constant[1];
                        $departmentArray[1] = Cis::getAllocatedYear();
                        break;
                    }
                }
            case Faculty::ENGR:
                foreach (Engineering::classConstants() as $constant) {
                    if ($department == $constant[0]) {
                        $departmentMatches = true;
                        $departmentArray[0] = $constant[1];
                        $departmentArray[1] = Engineering::getAllocatedYear();
                        break;
                    }
                }
            case Faculty::AGRIC:
                foreach (Agriculture::classConstants() as $constant) {
                    if ($department == $constant[0]) {
                        $departmentMatches = true;
                        $departmentArray[0] = $constant[1];
                        $departmentArray[1] = Agriculture::getAllocatedYear();
                        break;
                    }
                }
            case Faculty::EDU:
                foreach (Education::classConstants() as $constant) {
                    if ($department == $constant[0]) {
                        $departmentMatches = true;
                        $departmentArray[0] = $constant[1];
                        $departmentArray[1] = Education::getAllocatedYear();
                        break;
                    }
                }
            case Faculty::LAW:
                foreach (Law::classConstants() as $constant) {
                    if ($department == $constant[0]) {
                        $departmentMatches = true;
                        $departmentArray[0] = $constant[1];
                        $departmentArray[1] = Law::getAllocatedYear();
                        break;
                    }
                }
            default:
                if (!$departmentMatches)
                    throw new ValidationException($department);
        }

        $userId = $this->createUser($firstName, $middleName, $lastName, $emailAddress, $phoneNumber, $age, $gender, $localGovernmentArea, $stateOfOrigin, $country, $lastName, UserTypeEnum::STUDENT);
        //txt files
        $congratulationText = "text files\CongratulatoryApplicationMessage.txt";

        //save student to db
        $studentService = new StudentService($this->databaseConnect);
        //the desiredYear parameter has to be there because a student can be given admission into year 2.
        $studentService->saveStudent($userId, $department, $departmentArray[0], $faculty, $departmentArray[1], $desiredLevel);
        $headers = "";
        $subject = "Admission application at Preston ;)";
        $emailBody = file_get_contents($congratulationText);
        $search1 = "[Applicant]";
        $replace1 = $firstName . " " . $middleName . " " . $lastName;
        $search2 = "[Your Name]";
        $replace2 = "Group 7 team";
        $emailBody = str_replace($search1, $replace1, $emailBody);
        $emailBody = str_replace($search2, $replace2, $emailBody);
        $this->sendMail($emailAddress, $subject, $emailBody, $headers);
        return "Application for admission successful. Kindly check your email for further details. ";
    }

    private function createUser($firstName, $middleName, $lastName, $emailAddress, $phoneNumber, $age, $gender, $localGovernmentArea, $stateOfOrigin, $country, $password, string $role): string|bool
    {
        //Encrypt the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        //save user to db if everything checks out and throw a exception if a violation occurs...
        $queryToSaveUser_Student = "INSERT INTO users (FIRST_NAME,MIDDLE_NAME,LAST_NAME,AGE,ROLE,GENDER,LGA,STATE_OF_ORIGIN,COUNTRY,PHONE_NUMBER,EMAIL_ADDRESS,PASSWORD)
                   VALUES (:firstName,:middleName,:lastName,:age,:role,:gender,:localGovernmentArea,:stateOfOrigin,:country,:phoneNumber,:emailAddress,:hashedPassword)";
        $data = $this->databaseConnect->prepare($queryToSaveUser_Student);
        $data->bindParam(":firstName", $firstName);
        $data->bindParam(":middleName", $middleName);
        $data->bindParam(":lastName", $lastName);
        $data->bindParam(":age", $age);
        $data->bindParam(":role", $role);
        $data->bindParam(":gender", $gender);
//        $data->bindParam(":race", $race);
        $data->bindParam(":localGovernmentArea", $localGovernmentArea);
        $data->bindParam(":stateOfOrigin", $stateOfOrigin);
        $data->bindParam(":country", $country);
        $data->bindParam(":phoneNumber", $phoneNumber);
        $data->bindParam(":emailAddress", $emailAddress);
        $data->bindParam(":hashedPassword", $hashedPassword);
        $data->execute();
        return $this->databaseConnect->lastInsertId();
    }

    private function sendMail(string $recipientMail, string $subject, string $emailBody, string $headers): void
    {
        $headers = "From: " . $headers;
        if (mail($recipientMail, $subject, $emailBody, $headers))
            echo nl2br("Email sent successfully... \n");
        else
            echo nl2br("Email sending failed... \n");
    }

    //Student function
    public function admissionCheck($applicationNumber, $password): string //the passwordField is the lastName encrypted
    {
        $sql =
            "SELECT
            users.FIRST_NAME AS first_name,
            users.LAST_NAME AS last_name,
            users.PASSWORD AS password,
            students.APPLICATION_NUMBER AS application_number,
            students.ADMISSION_STATUS AS admission_status,
            students.RUSTICATED AS rusticated,
            studentdetails.DEPARTMENT AS department,
            studentdetails.FACULTY AS faculty,
            studentdetails.ALLOCATED_YEAR AS allocated_year
            FROM students 
            INNER JOIN users 
            ON students.USER_ID = users.USER_ID
            INNER JOIN studentdetails 
            ON students.STUDENT_DETAILS_ID = studentdetails.STUDENT_DETAILS_ID
            WHERE students.APPLICATION_NUMBER = :application_number";
        $data = $this->databaseConnect->prepare($sql);
        $data->bindParam(':application_number', $applicationNumber);
        $data->execute();
        $data = $data->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($data) === 0)
            throw new AuthenticationException();
        $data = $data[0];
        if (!password_verify($password, $data['password']))
            throw new AuthenticationException();
        if ($data['admission_status'] == "PENDING")
            throw InvalidStudentException::notAdmitted($data['first_name'], $data['last_name']);
        if ($data['admission_status'] == "DECLINED")
            throw InvalidStudentException::notAdmitted($data['first_name'], $data['last_name']);
        if ($data['rusticated'])
            throw InvalidStudentException::rusticated($data['first_name'], $data['last_name']);

        return "Congratulations, you've been offered admission to study " . $data['department'] . " in the " . $data['faculty'] . " faculty for a total of " . $data['allocated_year'] . " years. " . "Kindly check your email for more details";
    }

    //Admin function
    public function createAdmin($firstName, $middleName, $lastName, $age, $email, $phoneNumber, $gender, $localGovernmentArea, $stateOfOrigin, $country, $password): string
    {
//        $decryptedDetails = self::decodeJwtInCookie();
//        if ($decryptedDetails['role'] !== "ADMIN")
//            throw new AuthorizationFailedException();
        $this->createUser($firstName, $middleName, $lastName, $age, $email, $phoneNumber, $gender, $localGovernmentArea, $stateOfOrigin, $country, $password, UserTypeEnum::ADMIN);
        return $firstName . " " . $lastName . " was successfully created as an admin";
    }

    public function loginFunctionalityAdmin(string $emailAddress, string $password): string //works
    {
        $admin = UserTypeEnum::ADMIN;
        $sqlQuery = "SELECT USER_ID,FIRST_NAME,LAST_NAME,PASSWORD FROM users where EMAIL_ADDRESS = :email and ROLE = :role";
        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->bindParam(':email', $emailAddress);
        $data->bindParam(':role', $admin);
        $data->execute();
        $rows = $data->fetchAll(PDO::FETCH_NUM);
        if (sizeof($rows) === 0 || !password_verify($password, $rows[0][3]))
            throw new AuthenticationException();
        //return encrypted jwt...
        return self::jwtEncodePayload($rows[0][0], $rows[0][1], $rows[0][2], "ADMIN");
    }

    public static function jwtEncodePayload($userId, $firstName, $lastName, $role): string //works
    {
//        $secretKey = getenv("JWT_TOKEN");
        $secretKey = "JWT_TOKEN";
        $jwtToken = JWT::encode([$userId, $firstName, $lastName, $role, 'exp' => time() + 1800], $secretKey, 'HS256');
        setcookie("Preston", $jwtToken, time() + 1800, '/api/preston/admin', true, true);
        return "Signed in";
    }

    /**
     * @throws Exception
     */
    public function approveOrDeclineApplications(string $studentId, string $applicationNumber, string $admissionStatus): string
    {
//        $decryptedDetails = self::decodeJwtInCookie();
//        if ($decryptedDetails['role'] !== "ADMIN")
//            throw new AuthorizationFailedException();
        $sqlQuery = "SELECT 
        students.STUDENT_ID AS student_id,
        students.RUSTICATED as rusticated,
        students.ADMISSION_STATUS as admission_status,
        users.EMAIL_ADDRESS as email_address,
        users.FIRST_NAME AS first_name,
        users.MIDDLE_NAME as middle_name,
        users.LAST_NAME as last_name,
        users.ROLE AS role,
        studentdetails.FACULTY AS faculty,
        studentdetails.DEPARTMENT AS department,
        studentdetails.COURSE_CODE AS course_code,
        studentdetails.ALLOCATED_YEAR AS allocated_year
        FROM students 
        JOIN users ON students.USER_ID =users.USER_ID
        JOIN studentdetails ON students.STUDENT_DETAILS_ID = studentdetails.STUDENT_DETAILS_ID
        WHERE students.APPLICATION_NUMBER = :application_number AND students.STUDENT_ID = :studentId";

        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->bindParam(":application_number", $applicationNumber);
        $data->bindParam(":studentId", $studentId);
        $data->execute();

        $student = $data->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($student) == 0)
            throw new StudentDoesNotExist();
        $student = $student[0];
        if ($student['rusticated'])
            throw InvalidStudentException::rusticatedAlready();

        if ($studentId != $student['student_id'])
            throw new ValidationException ($studentId);

        if ($admissionStatus == "GRANTED")
            return $this->grantAdmission($student, $admissionStatus, $studentId);
        else if ($admissionStatus == "DECLINED")
            return $this->declineAdmission($student, $admissionStatus, $studentId);
        else
            return $this->moveAdmissionToPending($student, $admissionStatus, $studentId);
    }

    /**
     * @throws Exception
     */
    private function grantAdmission($student, $admissionStatus, $studentId): string
    {
        if ($student['admission_status'] == "GRANTED")
            throw new InvalidOperationException("Student has been granted admission already");
        $matricNumber = StudentService::matricNumberGenerator($student['course_code'], $student['student_id']);
        $sql = "UPDATE 
            students SET MATRIC_NUMBER = :matric,
                         ADMISSION_STATUS = :status
            WHERE students.STUDENT_ID = :studentId";

        $data = $this->databaseConnect->prepare($sql);
        $data->bindParam(":matric", $matricNumber);
        $data->bindParam(":status", $admissionStatus);
        $data->bindParam(":studentId", $studentId);
        $data->execute();

        $headers = "";
        $subject = "Welcome at Preston;)";
        $recipientMail = $student['email_address'];
        $welcomeMessage = "text files\WelcomeToPreston.txt";
        $emailBodyFile = $welcomeMessage;
        $emailBody = (string)file_get_contents($emailBodyFile);
        $search1 = "[insert matriculation number]";
        $replace1 = $matricNumber;
        $search2 = "[insert faculty]";
        $replace2 = $student['faculty'];
        $search3 = "Group ";
        $replace3 = "Group 7 team";
        $search4 = "[insert department]";
        $replace4 = $student['department'];
        $search5 = "[insert year]";
        $replace5 = $student['allocated_year'];
        $search6 = "[Your name]";
        $replace6 = "Group 7 admin";
        $search7 = "[Applicant]";
        $replace7 = $student['first_name'] . " " . $student['middle_name'] . " " . $student['last_name'];
        $emailBody = str_replace($search1, $replace1, $emailBody);
        $emailBody = str_replace($search2, $replace2, $emailBody);
        $emailBody = str_replace($search3, $replace3, $emailBody);
        $emailBody = str_replace($search4, $replace4, $emailBody);
        $emailBody = str_replace($search5, $replace5, $emailBody);
        $emailBody = str_replace($search6, $replace6, $emailBody);
        $emailBody = str_replace($search7, $replace7, $emailBody);
        $this->sendMail($recipientMail, $subject, $emailBody, $headers);
        return "Admission Granted Successfully";
    } //helper function

    private function declineAdmission($student, $admissionStatus, $studentId): string
    {
        $sql = "UPDATE 
            students SET ADMISSION_STATUS = :status
            WHERE students.STUDENT_ID = :studentId";
        if ($student['admission_status'] == "DECLINED")
            throw new InvalidOperationException("Student admission has been moved declined already");
        $data = $this->databaseConnect->prepare($sql);
        $data->bindParam(":status", $admissionStatus);
        $data->bindParam(":studentId", $studentId);
        $data->execute();
        $headers = "";
        $admissionDeclinedFile = "text files\AdmissionDeclined.txt";
        $subject = "Admission Application Decline to Preston University";
        $recipientMail = $student['email_address'];
        $search1 = '[Student’s Name]';
        $replace1 = $student['first_name'] . " " . $student['middle_name'] . " " . $student['last_name'];
        $search2 = "[Your Name]";
        $replace2 = "Group 7 Admin";
        $emailBody = (string)file_get_contents($admissionDeclinedFile);
        $emailBody = str_replace($search1, $replace1, $emailBody);
        $emailBody = str_replace($search2, $replace2, $emailBody);
        $this->sendMail($recipientMail, $subject, $emailBody, $headers);
        return "Admission Declined Successfully";
    } //helper function

    private function moveAdmissionToPending($student, $admissionStatus, $studentId): string
    {
        if ($student['admission_status'] == "PENDING")
            throw new InvalidOperationException("Student admission has been moved to pending already");
        $sql = "UPDATE 
            students SET ADMISSION_STATUS = :status
            WHERE students.STUDENT_ID = :studentId";

        $data = $this->databaseConnect->prepare($sql);
        $data->bindParam(":status", $admissionStatus);
        $data->bindParam(":studentId", $studentId);
        $data->execute();
        return "Admission Status for student with id :" . $studentId . " has been moved to Pending. ";
    }

    public function retrieveAllPendingApplications(): array
    {
        $decryptedDetails = self::decodeJwtInCookie();
        if ($decryptedDetails['role'] !== "ADMIN")
            throw new AuthorizationFailedException();
        $sqlQueryToRetrieve = "SELECT * FROM students
        JOIN users ON students.USER_ID = users.USER_ID
        JOIN studentdetails ON students.STUDENT_DETAILS_ID = studentdetails.STUDENT_DETAILS_ID 
        WHERE students.ADMISSION_STATUS = :status";
        $data = $this->databaseConnect->prepare($sqlQueryToRetrieve);
        $str = "PENDING";
        $data->bindParam(":status", $str);
        $data->execute();
        array($result = $data->fetchAll(PDO::FETCH_ASSOC));
        $fetch = new FetchAllDto($result);
        return $fetch->map();
    }

    private static function decodeJwtInCookie(): array  //helper function
    {
        $secretKey = getenv("JWT_TOKEN");
        if (isset($_COOKIE['Preston'])) {
            $jwtToken = $_COOKIE['Preston'];
            try {
                return (array)JWT::decode($jwtToken, $secretKey);
            } catch (LogicException|UnexpectedValueException $e) {
                throw new AuthenticationException();
            }
        } else {
            throw new AuthenticationException();
        }
    }

    public function addDepartmentAndFaculty($department, $faculty, $numberOfYears): string
    {
        $faculties = Faculty::classConstants();
        for ($i = 0; $i < sizeof(Faculty::classConstants()); $i++) {
            if ($faculties[$i] == $faculty)
                throw new InvalidOperationException("Faculty already exists !!!");
        }
        $decryptedDetails = self::decodeJwtInCookie();
        if ($decryptedDetails['role'] !== "ADMIN")
            throw new AuthorizationFailedException();
        $sqlQuery = "INSERT INTO courses(DEPARTMENT, FACULTY, ALLOCATED_YEAR, COURSE_CODE,
        VALUES($department, $faculty, $numberOfYears,substring($department,0,3)))";
        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->execute();
        return "The " . $department . " department has been added to the " . $faculty . " faculty successfully";
    }

}