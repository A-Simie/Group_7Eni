<?php
require_once __DIR__ . "/DatabaseConnect.php";

class UserService
{
    private DatabaseConnect $databaseConnect;
    private string $ADMIN = "ADMIN";
    private string $STUDENT = "STUDENT";

    public function __construct()
    {
        $this->databaseConnect = new DatabaseConnect();
    }

    // Student functions

    public function admissionCheck($applicationNumber, $password): string
    {
        $sql = "SELECT users.FIRST_NAME,users.LAST_NAME,students.APPLICATION_NUMBER,students.ADMISSION_STATUS,studentdetails.DEPARTMENT,studentsdetails.FACULTY,
        FROM students s
        JOIN users u ON s.USER_ID = u.USER_ID
        JOIN studentdetails sd ON s.STUDENTS_DETAILS_ID = sd.STUDENTS_DETAILS_ID
        WHERE s.APPLICATION_NUMBER = :application_number";
        $data = $this->databaseConnect->prepare($sql);
        $data->bindParam(':application_number', $applicationNumber);
        $data->execute();
        $student = null;
        while ($outputData = $data->fetch(PDO::FETCH_ASSOC)) {
            $student = array(
                'application_number' => $outputData['APPLICATION_DATE'],
                'first_name' => $outputData['FIRST_NAME'],
                'last_name' => $outputData['LAST_NAME'],
                'rusticated' => $outputData['RUSTICATED'],
                'admission_status' => $outputData['ADMISSION_STATUS'],
                'password' => $outputData['PASSWORD'],
                'department' => $outputData['DEPARTMENT'],
                'faculty' => $outputData['FACULTY'],
            );
        }
        if ($applicationNumber !== $student['application_number'] || $password !== $student['password'])
            throw new InvalidCredentialsException();
        if (!$student['admission_status'])
            throw InvalidStudent::notAdmitted($student['first_name'], $student['last_name']);
        if ($student['rusticated'])
            throw InvalidStudent::rusticated($student['first_name'], $student['last_name']);

        return "Congratulations, you've been offered admission to study " . $student['department'] . " in the " . $student['faculty'] . "faculty. Kindly check your email for more details";
    }

    public function admissionApplication($firstName, $middleName, $lastName, $age, $emailAddress, $phoneNumber, $gender, $race, $localGovernmentArea, $stateOfOrigin, $country, string $department, string $faculty, $desiredLevel = 100): string|bool
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
                    throw new ValidationException($faculty);
        }

        //Encrypt the password
        $hashedPassword = password_hash($lastName, PASSWORD_BCRYPT);

        //save user to db if everything checks out and throw a exception if a violation occurs...
        $queryToSaveUser_Student = "INSERT INTO users (FIRST_NAME,MIDDLE_NAME,LAST_NAME,AGE,ROLE,GENDER,RACE,LGA,STATE_OF_ORIGIN,COUNTRY,PHONE_NUMBER,EMAIL_ADDRESS,PASSWORD)
                   VALUES (:firstName,:middleName,:lastName,:age,:role,:gender,:race,:localGovernmentArea,:stateOfOrigin,:country,:phoneNumber,:emailAddress,:hashedPassword)";
        $data = $this->databaseConnect->prepare($queryToSaveUser_Student);
        $data->bindParam(":firstName", $firstName);
        $data->bindParam(":middleName", $middleName);
        $data->bindParam(":lastName", $lastName);
        $data->bindParam(":age", $age);
        $data->bindParam(":role", $this->STUDENT);
        $data->bindParam(":gender", $gender);
        $data->bindParam(":race", $race);
        $data->bindParam(":localGovernmentArea", $localGovernmentArea);
        $data->bindParam(":stateOfOrigin", $stateOfOrigin);
        $data->bindParam(":country", $country);
        $data->bindParam(":phoneNumber", $phoneNumber);
        $data->bindParam(":emailAddress", $emailAddress);
        $data->bindParam(":hashedPassword", $hashedPassword);

        $data->execute();

        //save student to db
        $studentService = new StudentService($this->databaseConnect);
        //the desiredYear parameter has to be there because a student can be given admission into year 2.
        $studentService->saveStudent($this->databaseConnect->lastInsertId(), $department, $departmentArray[0], $faculty, $departmentArray[1], $desiredLevel);
        $headers = "";
        $subject = "Admission application at Preston ;)";
        $emailBodyFile = __DIR__ . "./CongratulatoryApplicationMessage.txt";
        $emailBody = (string)file_get_contents($emailBodyFile);
        $search1 = "[Applicant]";
        $replace1 = $firstName . " " . $middleName . " " . $lastName;
        $search2 = "[Your Name]";
        $replace2 = "Group 7 team";
        $emailBody = str_replace($search1, $replace1, $emailBody);
        $emailBody = str_replace($search2, $replace2, $emailBody);

        $this->sendMail($emailAddress, $subject, $emailBody, $headers);
        return "Application for admission successful. Kindly check your email for further details";
    }

    //Admin only functions

    //create admin

    public function sendMail(string $recipientMail, string $subject, string $emailBody, string $headers): void
    {
        $headers = "From: " . $headers;
        if (mail($recipientMail, $subject, $emailBody, $headers))
            echo "Email sent successfully...";
        else
            echo "Email sending failed...";
    }

    public function createAdmin($firstName, $middleName, $lastName, $age, $email, $phoneNumber, $gender, $race, $localGovernmentArea, $stateOfOrigin, $country, $password): void
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $queryToCreateAdmin = "INSERT INTO users (FIRST_NAME,MIDDLE_NAME,LAST_NAME,AGE,ROLE,GENDER,RACE,LGA,STATE_OF_ORIGIN,COUNTRY,PHONE_NUMBER,EMAIL_ADDRESS,PASSWORD,
        VALUES ($firstName,$middleName,$lastName,$age,$this->ADMIN,$gender,$race,$localGovernmentArea,$stateOfOrigin,$country,$phoneNumber,$email,$hashedPassword)";
        $this->databaseConnect->query($queryToCreateAdmin);
    }

    public function loginFunctionalityAdmin(string $emailAddress, string $password): bool|string
    {
        $users = array();
        $sqlQuery = "SELECT ID,ROLE,FIRST_NAME,LAST_NAME,PASSWORD FROM users where EMAIL_ADDRESS";
        $data = $this->databaseConnect->prepare($sqlQuery, $emailAddress);
        $data->execute();
        while ($outputData = $data->fetch(PDO::FETCH_ASSOC)) {
            $users[$outputData['ID']] = array(
                'id' => $outputData['ID'],
                'role' => $outputData['ROLE'],
                'first_name' => $outputData['FIRST_NAME'],
                'last_name' => $outputData['LAST_NAME'],
                'password' => $outputData['PASSWORD']
            );
        }
        if (!password_verify($password, $users['password']))
            return "error related sha--------";
        if ($users['role'] != "ADMIN")
            return "error related sha--------";
        //rather than returning json, maybe it would be wise to simply store some credentials in the session id
        return json_encode($users);
    }

    //Get all students irrespective of admission status

    public function approveOrDeclineApplications(string $studentId, string $applicationNumber, string $admissionStatus): string
    {
        $sqlQuery = "SELECT students.STUDENT_ID,students.RUSTICATED,users.EMAIL_ADDRESS,studentdetails.FACULTY,studentdetails.DEPARTMENT,studentdetails.COURSE_CODE,studentdetails.ALLOCATED_YEAR
        FROM students s
        JOIN users u ON s.USER_ID = u.USER_ID
        JOIN studentdetails su ON STUDENT_DETAILS_ID = su.STUDENT_DETAILS_ID
        WHERE s.APPLICATION_NUMBER = :application_number";

        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->bindParam("application_number", $applicationNumber);
        $data->execute();

        $student = null;

        while ($outputData = $data->fetch(PDO::FETCH_ASSOC)) {
            $student = array(
                'student_id' => $outputData['STUDENT_ID'],
                'rusticated' => $outputData['RUSTICATED'],
                'email_address' => $outputData['EMAIL_ADDRESS'],
                'faculty' => $outputData['FACULTY'],
                'department' => $outputData['DEPARTMENT'],
                'course_code' => $outputData['COURSE_CODE'],
                'allocated_year' => $outputData['ALLOCATED_YEAR']
            );
        }

        if ($student['rusticated'])
            throw InvalidStudent::rusticatedAlready();

        if ($studentId !== $student['student_id'])
            throw new ValidationException ($studentId);

        $matricNumber = StudentService::matricNumberGenerator($student['course_code'], $student['student_id']);

        $sql = "INSERT INTO students (MATRIC_NUMBER,ADMISSION_STATUS) VALUES (:matric,:status)";

        $data = $this->databaseConnect->prepare($sql);
        $data->bindParam(":matric", $matricNumber);
        $data->bindParam(":status", $admissionStatus);
        $data->execute();

        $headers = "";
        $subject = "Welcome at Preston ;)";
        $recipientMail = $student['email_address'];
        $emailBodyFile = __DIR__ . "./WelcomeToPreston.txt";
        $emailBody = (string)file_get_contents($emailBodyFile);
        $search1 = "[insert matriculation number]";
        $replace1 = $matricNumber;
        $search2 = "[insert faculty/department name]";
        $replace2 = $student['department'] . " " . $student['faculty'];
        $search3 = "[Your Name]";
        $replace3 = "Group 7 team";
        $search4 = "[insert department]";
        $replace4 = $student['department'];
        $search5 = "[insert year]";
        $replace5 = $student['allocated_year'];
        $emailBody = str_replace($search1, $replace1, $emailBody);
        $emailBody = str_replace($search2, $replace2, $emailBody);
        $emailBody = str_replace($search3, $replace3, $emailBody);
        $emailBody = str_replace($search4, $replace4, $emailBody);
        $emailBody = str_replace($search5, $replace5, $emailBody);
        $this->sendMail($recipientMail, $subject, $emailBody, $headers);
        return "Successful!!!";
    }

    //Get all students per level

    public function retrieveAllPendingApplications(): string
    {
        $sqlQueryToRetrieve = "SELECT * FROM students s
        JOIN users u ON s.USER_ID = u.USER_ID
        JOIN studentdetails sd ON s.STUDENTS_DETAILS_ID = sd.STUDENTS_DETAILS_ID 
        WHERE s.ADMISSIONSTATUS = :status";
        $data = $this->databaseConnect->prepare($sqlQueryToRetrieve);
        $str = "PENDING";
        $data->bindParam(":status", $str);
        $data->execute();

        return $data->fetchAll();
    }

    //Get all students per course

    public function retrieveAllStudentPerLevel(): string
    {

    }

    //check if a student is rusticated

    public function retrieveAllStudentPerCourse(): string
    {

    }

    public function checkIfAStudentIsRusticated(string $matricNumber)
    {

    }

    public function addCourse($department, $faculty, $numberOfYears, $courseCode): string
    {

        $sqlQuery = "INSERT INTO courses(DEPARTMENT,FACULTY,NUMBER_OF_YEARS,CODE,
                    VALUES($department,$faculty,$numberOfYears,$courseCode))";
        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->execute();
        return "The " . $department . " department has been added to the " . $faculty . " faculty successfully";
    }
}