<?php
session_start();

use Lib\Database\MySQL;
use Lib\Tables\ViewsTable;
include "../vendor/autoload.php";

if(!empty($_POST['blogId'])) {
    $viewsTable = new ViewsTable(new MySQL());

    $status = $viewsTable->checkingStatus($_POST['blogId'], $_SESSION['auth']['id']);

    if(count($status)) {
        echo json_encode([
            'status' => 400,
            'message' => "Already viewed"
        ]);
        exit();
    }

    $result = $viewsTable->setView($_POST['blogId'], $_SESSION['auth']['id']);
    if($result) {
        echo json_encode([
            'status' => 200,
            'message' => 'Viewed'
        ]);
    } else {
        echo json_encode([
            'status' => 500,
            'message' => "Something went wrong with the server"
        ]);
    }
}