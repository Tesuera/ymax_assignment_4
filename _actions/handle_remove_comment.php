<?php
session_start();

use Lib\Database\MySQL;
use Lib\Tables\CommentsTable;
include "../vendor/autoload.php";

if(!empty($_POST['commentId'])) {
    $commentsTable = new CommentsTable(new MySQL());
    
    $permission = $commentsTable->permission($_SESSION['auth']['id'], $_POST['commentId']); 
    if($permission) {
        $result = $commentsTable->removeComment($_POST['commentId']);
        if($result) {
            echo json_encode([
                'status' => 200,
                'message' => 'Comment removed'
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