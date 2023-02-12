<?php

class StudentService
{
    private string $department;
    private string $faculty;
    private DatabaseConnect $databaseConnect;

    public function __construct(DatabaseConnect $databaseConnect)
    {
        $this->databaseConnect = $databaseConnect;
    }

    public function saveStudent(int $userId, string $department, string $courseCode, string $faculty, int $allocatedYear, int $desiredYear): void
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

        $sqlQuery = "INSERT INTO students (MATRIC_NUMBER, APPLICATION_NUMBER, USER_ID, STUDENT_DETAILS_ID)
            values (:matricNumber,:applicationNumber,:userId,:studentId)";
        $data = $this->databaseConnect->prepare($sqlQuery);
        $data->bindParam(":applicationNumber", /*automatically generated...*/ $applicationNumber);
        $data->bindParam("userId", $userId);
        $data->bindParam("studentId", $studentDetailsId);
        $data->execute();
    }

    public function getDepartment(): string
    {
        return $this->department;
    }

    public function getFaculty(): string
    {
        return $this->faculty;
    }

    public function getAdmissionStatus(): AdmissionStatus
    {
        return $this->admissionStatus;
    }

    public function setDepartment(string $department): void
    {
        $this->department = $department;
    }

    public function setFaculty(string $faculty): void
    {
        $this->faculty = $faculty;
    }

    public function setApplicationDate(DateTime $applicationDate): void
    {
        $this->applicationDate = $applicationDate;
    }

    public function setAdmissionStatus(AdmissionStatus $admissionStatus): void
    {
        $this->admissionStatus = $admissionStatus;
    }

    public function applicationNumberGenerator(int $length = 20): string
    {
        $characters = "0123456789abcdefghijklmnopqrstwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $len = strlen($characters);
        $generatedString = "";
        for ($i = 0; $i < $len; $i++) {
            $generatedString .= $characters[rand(0, $len - 1)];
        }
        return $generatedString;
    }

    public static function matricNumberGenerator($departmentCode, $studentId): string
    {
        $yearOfAdmission = date('y');

        $characters = "0123456789";
        $len = strlen($characters);
        $generatedString = "";
        for ($i = 0; $i < $len; $i++) {
            $generatedString .= $characters[rand(0, 2)];
        }

        return $yearOfAdmission . "/" . $generatedString . $departmentCode . $studentId;
    }
}







































