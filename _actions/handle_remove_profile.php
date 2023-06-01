<?php
session_start();
use Lib\Database\MySQL;
use Lib\Tables\UsersTable;

include "../vendor/autoload.php";

if(isset($_POST['userId'])) {
    $usersTable = new UsersTable(new MySQL());
    if($_POST['userId'] == $_SESSION['auth']['id']) {
        $result = $usersTable->removeProfile($_POST['userId']);
        if($result) {
            $_SESSION['auth']['profile'] ='default.jpg';
            echo json_encode([
                'status' => 200,
                'message' => 'profile removed'
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Bad request'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 500,
            'message' => 'Bad request'
        ]);
    }
} else {
    echo json_encode([
        'status' => 500,
        'message' => 'Bad request'
    ]);
}