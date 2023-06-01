<?php
date_default_timezone_set("Asia/Yangon");

use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\UsersTable;


$error_status = 0;
$errors = [];

if(isset($_POST['email'])) {
    $usersTable = new UsersTable(new MySQL());

    $email;
    $password;

    if(empty($_POST['email'])) {
        $errors_status = 1;
        $errors['email'] = "Email is required";
    } else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error_status = 1;
        $errors['email'] = "Email must be a valid value";
    } else {
        $email = $_POST['email'];
    }

    if(empty($_POST['password'])) {
        $error_status = 1;
        $errors['password'] = "Password is required";
    } else if(strlen($_POST['password']) < 8) {
        $error_status = 1;
        $errors['password'] = "Password must be longer than 8 characters";
    } else if(strlen($_POST['password']) > 20) {
        $error_status = 1;
        $errors['password'] = "Password must not be longer than 20 characters";
    } else {
        $password = $_POST['password'];
    }


    if(!$error_status) {
        $data = [
            "email" => $email,
            "password" => $password
        ];

        $result = $usersTable->login($data);
        if($result['status'] == 400) {
            $error_status = 1;
            $errors['password'] = $result['message'];
        } else if ($result['status'] == 200) {
            session_start();
            $_SESSION['auth'] = json_decode( json_encode($result['auth']), true);

            HTTP::redirect("./index.php");
        }
    }
}