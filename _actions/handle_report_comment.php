<?php 
session_start();

use Lib\Database\MySQL;
use Lib\Tables\ReportsCommentsTable;

include "../vendor/autoload.php";

if(!empty($_POST['commentId'])) {
    $reportsCommentsTable = new ReportsCommentsTable(new MySQL());
    $result = $reportsCommentsTable->reportComment($_POST['commentId'], $_SESSION['auth']['id']);
   
    if($result['status'] == 4) {
        echo json_encode([
            'status' => 200,
            'comment_status' => 4,
            'message' => 'Comment deleted by too many reports'
        ]);
    } else if($result['status'] == 1) {
        echo json_encode([
            'status' => 200,
            'comment_status' => 1,
            'message' => 'Comment'
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