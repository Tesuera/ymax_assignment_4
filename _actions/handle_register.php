<?php
date_default_timezone_set("Asia/Yangon");

use Carbon\Carbon;
use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\UsersTable;


$error_status = 0;
$errors = [];


if(isset($_POST['username'])) {
    
    $username;
    $email;
    $password;
    $profile;

  

    $usersTable = new UsersTable(new MySQL());

    if(empty($_POST['username'])) {
        $error_status = 1;
        $errors['username'] = "Username is required";
    } else if (strlen($_POST['username']) < 3) {
        $error_status = 1;
        $errors['username'] = "Username must be longer than 3 characters";
    } else if(strlen($_POST['username']) > 20) {
        $error_status = 1;
        $errors['username'] = "Username must not be longer than 20 characters";
    } else if(preg_match("[a-zA-Z ]", $_POST['username'])) {
        $error_status = 1;
        $errors['username'] = "Username only allows characters";
    } else {
        $username = filterInput($_POST['username']);
    }


    if(empty($_POST['email'])) {
        $error_status = 1;
        $errors['email'] = "Email is required";
    } else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error_status = 1;
        $errors['email'] = "Email must be a valid value";
    } else if ($usersTable->email_exist($_POST['email'])) {
        $error_status  = 1;
        $errors['email'] = "This email is already existed in the server";
    } else {
        $email = $_POST['email'];
    }

    if(empty($_POST['password'])) {
        $error_status = 1;
        $errors['password'] = "Password is required";
    } else if(strlen($_POST['password']) < 8) {
        $error_status = 1;
        $errors['password'] = "Password must be at least 8 characters";
    } else if(strlen($_POST['password']) > 20) {
        $error_status = 1;
        $errors['password'] = "Password must not be more than 20 characters";
    } else if ($_POST['password'] !== $_POST['password_confirmation']) {
        $error_status = 1;
        $errors['password_confirmation'] = "Password confirmation does match";
    } else {
        $password = $_POST['password'];
    }

    if(!empty($_POST['croppedProfile'])) {
        $image_parts = explode(";base64,", $_POST['croppedProfile']);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $name =  uniqid() . '.png';
        $profile = $name;
    }

    if(!$error_status) {
        $unique_id = uniqid() . '__user__' . uniqid();

        $data = [
            'unique_id' => $unique_id,
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'modified_at' => Carbon::now()
        ];

        if(!empty($profile)) {
            $data['profile'] = $profile; 
        
            $folderPath = 'images/profiles/';
            $file = $folderPath . $profile;
            file_put_contents($file, $image_base64);
        }

        $user = json_decode(json_encode($usersTable->register($data)), true);
        if($user['id']) {
            session_start();
            $_SESSION['auth'] = $user;

            HTTP::redirect("./index.php");
        }
    }
}