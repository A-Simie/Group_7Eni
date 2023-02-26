<?php
require "controller/AdminController.php";
require "controller/LoginController.php";
require "controller/StudentController.php";
require "exceptions/AuthenticationException.php";
require "exceptions/AuthorizationFailedException.php";
require "exceptions/InvalidOperationException.php";
require "exceptions/InvalidStudentException.php";
require "exceptions/UserAlreadyExistsException.php";
require "model/Course.php";
require "model/Faculty.php";
require "service/DatabaseConnect.php";
require "service/StudentService.php";
require "service/UserService.php";
require __DIR__ . '/vendor/autoload.php';

header('Content-Type: application/json');

//localhost
$localMachine = "/PrestonSchoolManagementSystem/"; //local root folder

//student
$admissionApplicationRoute = "api/preston/student/admission/apply"; //post
$admissionCheckRoute = "api/preston/admission/student/check"; //get
//admin
$createAdminRoute = "api/preston/admin/create"; //post
$adminLoginRoute = "api/preston/admin/login"; //post
$approveOrDeclineAdmissionsRoute = "api/preston/admin/applications"; //post
$retrieveAllPendingApplicationsRoute = "api/preston/admin/pending-applications"; //get
$addDepartmentRoute = "api/preston/admin/department/create"; //post

//helper routes
$retrieveAllDepartments = "api/preston/courses";
$retrieveAllFaculties = "api/preston/faculties";


$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody);

//Get Requests
switch ($method) {
    case 'POST' && $uri == $localMachine . $admissionCheckRoute:
        $student = new StudentController();
        echo $student->checkForAdmission($data->applicationNumber, $data->password);
        break;
    case  'POST' && $uri == $localMachine . $approveOrDeclineAdmissionsRoute:
        $admin = new AdminController();
        echo $admin->approveOrDeclineApplications($data->studentId, $data->applicationNumber, $data->admissionStatus);
        break;
    case 'GET' && $uri == $localMachine . $retrieveAllPendingApplicationsRoute:
        $admin = new AdminController();
        $result = $admin->retrieveAllPendingApplications();
        echo $result;
        break;
    case 'POST' && $uri == $localMachine . $admissionApplicationRoute:
        $student = new StudentController();
        echo $student->applyForAdmission($data->firstName, $data->middleName, $data->lastName, $data->age, $data->emailAddress, $data->phoneNumber, $data->gender, $data->localGovernmentArea, $data->stateOfOrigin, $data->country, $data->department, $data->faculty, $data->desiredLevel);
        break;
    case 'POST' && $uri == $localMachine . $createAdminRoute :
        $admin = new AdminController();
        echo $admin->createAdmin($data->firstName, $data->middleName, $data->lastName, $data->age, $data->emailAddress, $data->phoneNumber, $data->gender, $data->race, $data->localGovernmentArea, $data->stateOfOrigin, $data->country, $data->password);
        break;
    case 'POST' && $uri == $localMachine . $adminLoginRoute:
        $adminLogin = new AdminController();
        $adminInfo = $adminLogin->adminLogin($data->emailAddress, $data->password);
        echo $adminInfo;
        break;
    case 'POST' && $uri == $localMachine . $addDepartmentRoute:
        $admin = new AdminController();
        echo $admin->addDepartment($data->department, $data->faculty, $data->numberOfyears, $data->courseCode);
        break;
    case 'GET' && $uri == $localMachine . $retrieveAllDepartments:
        $courseController = new CourseController();
        echo $courseController->getDepartments();
        break;
    case 'GET' && $uri == $localMachine . $retrieveAllFaculties:
        $courseController = new CourseController();
        echo $courseController->getFaculties();
        break;
    default:
        echo "hello";
        break;
}