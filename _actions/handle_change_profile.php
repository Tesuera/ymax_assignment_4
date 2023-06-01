<?php

use Carbon\Carbon;
use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\UsersTable;

$error_status = 0;
$errors = [];

if(isset($_POST['email'])) {
    $usersTable = new UsersTable(new MySQL());

    $username;
    $email;
    $profile;


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
        $errors_status = 1;
        $errors['email'] = "Email is required";
    } else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error_status = 1;
        $errors['email'] = "Email must be a valid value";
    } else {
        $email = $_POST['email'];
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
        $data = [
            'username' => $username,
            'email' => $email,
            'user_id' => $_SESSION['auth']['id'],
            'modified_at' => Carbon::now()
        ];

        if(!empty($profile)) {
            $data['profile'] = $profile; 
        
            $folderPath = 'images/profiles/';
            $file = $folderPath . $profile;
            file_put_contents($file, $image_base64);
        }

        $result = $usersTable->updateProfile($data);
        if(isset($result->id)) {
            $_SESSION['auth']['username'] = $username;
            $_SESSION['auth']['email'] = $email;
            if(!empty($profile)) {
                $_SESSION['auth']['profile'] = $profile;
            }
            HTTP::redirect('./profile.php');
        }
    }
}