<?php
require __DIR__ . '../vendor/autoload.php';
require "service/UserService.php";
require "service/StudentService.php";
require "controller/StudentController.php";
require "controller/AdminController.php";
require "controller/LoginController.php";
require "controller/CourseController.php";
require "exceptions/InvalidOperationException.php";

require "model/Faculty.php";
require "model/Course.php";
require "model/FetchAllDto.php";
require "model/StudentDto.php";

use Faker\Factory;

$faker = Factory::create();
$studentController = new StudentController();
$userService = new UserService();
$adminController = new AdminController();
$loginController = new LoginController();
$courseController = new CourseController();

//$result = $studentController->applyForAdmission("Juice", $faker->userName(), $faker->lastName,
//    $faker->year, $faker->email, $faker->phoneNumber, "male", $faker->city(), $faker->country(), $faker->country, "Computer Science", Faculty::CIS, 200);
//$result = $studentController->checkForAdmission("MQA8OxsagWoBbJ2a6By7", "Bins");
//$result = $adminController->retrieveAllPendingApplications();
//$result = $adminController->approveOrDeclineApplications(15, "HW6LCbOHkKmochBAlrVJ", "PENDING");
//$result = $loginController->adminLogin("Banji@gmail.com", "Banji#007");
//$result = $courseController->getFaculties();
//$result = $courseController->getDepartments();
//echo $result;
