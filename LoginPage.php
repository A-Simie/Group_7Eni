<?php
header("Content-Type: application/json; charset=UTF-8");

$emailAddress = $_SESSION["emailAddress"];
$loggedIn = $_SESSION["loggedIn"];

$apiRoute = "api/preston/login";

$method = $_SERVER['REQUEST_METHOD'];

if ($loggedIn) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode("User is already logged in");
}
if ($method !== 'POST'){
    header("HTTP/1.1 415 Unsupported Media Type");
    echo json_encode("Method not allowed!!!");
}


