<?php

use Lib\Database\MySQL;
use Lib\Tables\AppreciateTable;
session_start();
include "../vendor/autoload.php";

if(!empty($_POST['blogId'])) {
    $appreciatesTable = new AppreciateTable(new MySQL());

    if($appreciatesTable->checkingStatus($_SESSION['auth']['id'], $_POST['blogId'])) {
        echo json_encode([
            'status' => 400,
            'message' => "You've already appreciated that blog"
        ]);
        exit();
    } else {
        $result = $appreciatesTable->appreciate($_SESSION['auth']['id'], $_POST['blogId']);

        if($result) {
            echo json_encode([
                'status' => 200,
                'message' => 'Appreciated'
            ]);
        }
        exit();
    }
}