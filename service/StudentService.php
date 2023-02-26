<?php

use Kreait\Firebase;

class StudentService
{
    private string $student = UserTypeEnum::STUDENT;

    private DatabaseConnect $databaseConnect;
    private string $pathToCredentials;

    public function __construct(DatabaseConnect $databaseConnect)
    {
        $this->databaseConnect = $databaseConnect;
        $this->pathToCredentials = "C:\Users\user\Documents\preston-university-jwt-file.json";
    }

    /**
     * @throws Exception
     */
    static function matricNumberGenerator($departmentCode, int $studentId): string
    {
        $yearOfAdmission = date('y');
        $len = strlen(strval($studentId));
        if ($len == 1) {
            $studentId = "00" . strval($studentId);
        } else if ($len == 2)
            $studentId = 0 . $studentId;
        $generatedString = random_int(10, 99);
        return $yearOfAdmission . "/" . $generatedString . $departmentCode . $studentId;
    }

    public static function getDepartments(): array
    {
        return [Cis::classConstants(), Engineering::classConstants(), Agriculture::classConstants(), Education::classConstants(), Law::classConstants()];
    }

    public static function getFaculties(): array
    {
        return Faculty::classConstants();
    }

    function saveStudent(int $userId, string $department, string $courseCode, string $faculty, int $allocatedYear, int $desiredYear): void
    {
        //save the student details to the student details db...
        $sqlQueryToSaveStudentsDetails =
            "INSERT INTO studentdetails (FACULTY, DEPARTMENT, COURSE_CODE, ALLOCATED_YEAR, DESIRED_LEVEL)
            VALUES (:faculty,:department,:courseCode,:allocatedYear,:desiredLevel)";
        $data = $this->databaseConnect->prepare($sqlQueryToSaveStudentsDetails);
        $data->bindParam(":faculty", $faculty);
        $data->bindParam(":department", $department);
        $data->bindParam(":courseCode", $courseCode);
        $data->bindParam(":allocatedYear", $allocatedYear);
        $data->bindParam(":desiredLevel", $desiredYear);
        $data->execute();

        $studentDetailsId = $this->databaseConnect->lastInsertId(); //the column id...
        $applicationNumber = $this->applicationNumberGenerator();

        $sqlQuery = "INSERT INTO students (APPLICATION_NUMBER, USER_ID, STUDENT_DETAILS_ID)
            values (:applicationNumber,:userId,:studentId)";
        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->bindParam(":applicationNumber", /*automatically generated...*/ $applicationNumber);
        $data->bindParam("userId", $userId);
        $data->bindParam("studentId", $studentDetailsId);
        $data->execute();
    }

    function applicationNumberGenerator(int $length = 20): string
    {
        $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $len = strlen($characters);
        $generatedString = "";
        for ($i = 0; $i < $len; $i++) {
            $generatedString .= $characters[rand(0, $len - 1)];
        }
        return $generatedString;
    }

    public function otpRequest($phoneNumber, $emailAddress): int
    {
        $auth =  
    }

    public function changePassword($matricNumber, $phoneNumber, $emailAddress, $lastName, $newPassword, int $otp): string //this service is for when the user default password is still his last name or when the student wants to change his last name...
    {
        //confirm the otp was generated for the phone number and the email address...
        $sqlToRetrieve =
            "SELECT
            users.USER_ID as user_id,
            users.LAST_NAME as last_name,
            users.PASSWORD AS password,
            users.PHONE_NUMBER as phone_number,
            users.EMAIL_ADDRESS as email_address,
            students.MATRIC_NUMBER AS matric_number,
            students.RUSTICATED AS rusticated
            FROM students 
            INNER JOIN users 
            ON students.USER_ID = users.USER_ID
            INNER JOIN studentdetails 
            ON students.STUDENT_DETAILS_ID = studentdetails.STUDENT_DETAILS_ID
            WHERE students.MATRIC_NUMBER = :matric_number";
        $data = $this->databaseConnect->prepare($sqlToRetrieve);
        $data->bindParam(':matric_number', $matricNumber);
        $data->execute();
        $data = $data->fetchAll(PDO::FETCH_ASSOC);
        if (sizeof($data) === 0)
            throw new AuthenticationException();
        $data = $data[0];
        if ($data['phone_number'] != $phoneNumber || $data['email_address'] != $emailAddress || $data['last_name'] != $lastName)
            throw new AuthenticationException();

        $sqlToUpdate = "UPDATE 
            users SET PASSWORD = :password
            WHERE users.USER_ID = :userId";

        $data = $this->databaseConnect->prepare($sqlToUpdate);
        $data->bindParam(":password", $newPassword);
        $data->bindParam(":userId", $data['user_id']);
        $data->execute();
        return "Password successfully updated !!!";
    }

    public function StudentLogin($matricNumber, $password,): string
    {
        $sqlQuery = "SELECT users.USER_ID,users.FIRST_NAME,users.LAST_NAME,users.PASSWORD,students.MATRIC_NUMBER as matric_number
                        FROM students
                        INNER JOIN users 
                        ON students.USER_ID = users.USER_ID
                        where MATRIC_NUMBER = :matric_number and users.ROLE = :role";
        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->bindParam(':role', $this->student);
        $data->execute();
        $rows = $data->fetchAll(PDO::FETCH_NUM);
        if (sizeof($rows) === 0 || !password_verify($password, $rows[0][3]))
            throw new AuthenticationException();
        //return encrypted jwt...
        return UserService::jwtEncodePayload($rows[0][0], $rows[0][1], $rows[0][2], "ADMIN");
    }
}