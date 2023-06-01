<?php 

use Lib\Helpers\HTTP;
use Lib\Database\MySQL;
use Lib\Tables\UsersTable;

$error_status = 0;
$errors = [];

if(isset($_POST['old_password'])) {
    $usersTable = new UsersTable(new MySQL());

    $old_password;
    $new_password;

    if(empty($_POST['old_password'])) {
        $error_status = 1;
        $errors['old_password'] = "Old password is required";
    } else if (strlen($_POST['old_password']) < 8) {
        $error_status = 1;
        $errors['old_password'] = "This field must have at least 8 characters";
    } else if(strlen($_POST['old_password']) > 20) {
        $error_status = 1;
        $errors['old_password'] = "This field must not be longer than 20 characters";
    } else if(!password_verify($_POST['old_password'], $usersTable->getPasswordByUser($_SESSION['auth']['id']))) {
        $error_status = 1;
        $errors['old_password'] = "Old password doesn't match";
    } else {
        $old_password = $_POST['old_password'];
    }


    if(empty($_POST['new_password'])) {
        $error_status = 1;
        $errors['new_password'] = "New password is required";
    } else if (strlen($_POST['new_password']) < 8) {
        $error_status = 1;
        $errors['new_password'] = "This field must have at least 8 characters";
    } else if(strlen($_POST['new_password']) > 20) {
        $error_status = 1;
        $errors['new_password'] = "This field must not be longer than 20 characters";
    } else if($_POST['new_password'] !== $_POST['confirm_password']) {
        $error_status = 1;
        $errors['confirm_password'] = "Password confirmation doesn't match";
    } else {
        $new_password = $_POST['new_password'];
    }

    if(!$error_status) {
        $password = password_hash($new_password, PASSWORD_DEFAULT);
        $count = $usersTable->changePassword($password, $_SESSION['auth']['id']);

        if($count) {
            $_SESSION['auth']['password'] = $password;
            HTTP::redirect('./profile.php');
        }
    }
}