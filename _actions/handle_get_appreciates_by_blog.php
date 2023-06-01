<?php

use Lib\Database\MySQL;
use Lib\Tables\AppreciateTable;

include "../vendor/autoload.php";

if(!empty($_POST['blogId'])) {
    $appreciatesTable = new AppreciateTable(new MySQL());

    $users = $appreciatesTable->getAllAppreciatesByUser($_POST['blogId']);
    
    if(count($users)) {
        echo json_encode([
            'status' => 200,
            'users' => $users
        ]);
    } else {
        echo json_encode ([
            'status' => 400,
            'message' => 'No appreciates yet.'
        ]);
    }
}